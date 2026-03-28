<?php
        
namespace App\Services\finance;

use App\Repositories\finance\ReportRepository;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReportService
{
    public function __construct(
        protected ReportRepository $repository
    ) {}

    public function labaRugi($data = [], $isALl = false)
    {
        $configMaster = config('setting.laba_rugi');
        
        $transactions = $this->repository->getAllCashInCashOut($data, $isALl);

        if ($transactions['error']) {
            return $transactions;
        }

        $bebanPenyusutan = $this->repository->getBebanPenyusutan($data, $isALl);

        if ($bebanPenyusutan['error']) {
            return $bebanPenyusutan;
        }

        $res = [];
        foreach ($configMaster as $key => $value) {
            foreach ($value as $k => $v) {
                // cek jika $k adalah numerik
                if (is_numeric($k)) {
                    $k = $key;
                }
                foreach ($v as $k2 => $v2) {
                    $res[$key][$k][$k2] = 0;
                    // cek jika v2 bukan "" atau v2 tidak diawali dengan >>
                    if ($v2 == '>>beban-penyusutan') {
                        $res[$key][$k][$k2] = $bebanPenyusutan['result'];
                    } elseif ($v2 !== "") {
                        // pisah dengan koma jika ada
                        $v2 = explode(',', $v2);
                        $total = 0;
                        foreach ($v2 as $k3 => $v3) {
                            $total = $total + $transactions['result']->where('code', $v3)->sum('amount');
                        }
                        $res[$key][$k][$k2] = $total;
                    }
                }
            }
        }

        $totalPendapatan        = $this->sumValues($res['4-000 PENDAPATAN']);
        $totalBiayaPendapatan   = $this->sumValues($res['5-000 BIAYA ATAS PENDAPATAN']);
        $totalBiayaOperasional  = $this->sumValues($res['6-000 BIAYA OPERASIONAL']);
        $totalPendapatanLainnya = $this->sumValues($res['7-000 PENDAPATAN LAINNYA']);
        $totalBiayaLainnya      = $this->sumValues($res['8-000 BIAYA LAINNYA']);
        $totalBagiHasil         = $this->sumValues($res['9-000 BAGI HASIL/ TARIKAN PROFIT']);


        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $monthNumber = $data['month'] ?? Carbon::now()->month;
        $year        = $data['year'] ?? Carbon::now()->year;

        if (isset($data['search'])) {
            $res = $this->filterAllNestedCollection($res, $data['search']);
        }

        return [
            'error' => null,
            'status' => 200,
            'result' => [
                "periode"                  => $months[(int) $monthNumber] . ' ' . $year,
                'total_pendapatan'         => $totalPendapatan,
                'total_biaya_pendapatan'   => $totalBiayaPendapatan,
                'total_biaya_operasional'  => $totalBiayaOperasional,
                'total_pendapatan_lainnya' => $totalPendapatanLainnya,
                'total_biaya_lainnya'      => $totalBiayaLainnya,
                'total_tarikan'            => $totalBagiHasil,
                'laba_kotor'               => $totalPendapatan - $totalBiayaPendapatan,
                'laba_rugi'                => ($totalPendapatan - $totalBiayaPendapatan) + $totalPendapatanLainnya - ($totalBiayaOperasional + $totalBiayaLainnya + $totalBagiHasil),
                'detail'                   => $res
            ]
        ];

    }

    public function cashIn($data = [], $isALl = false)
    {
        $configMaster = config('setting.cash_in_report');
        
        $transactions = $this->repository->getAllCashInCashOut($data, $isALl);

        if ($transactions['error']) {
            return $transactions;
        }

        $res = [];
        foreach ($configMaster as $key => $value) {
            foreach ($value as $k => $v) {
                // cek jika $k adalah numerik
                if (is_numeric($k)) {
                    $k = $key;
                }
                foreach ($v as $k2 => $v2) {
                    $res[$key][$k][$k2] = 0;
                    // cek jika v2 bukan "" atau v2 tidak diawali dengan >>
                    if ($v2 !== "") {
                        $v2 = explode(',', $v2);
                        $total = 0;
                        foreach ($v2 as $k3 => $v3) {
                            $total = $total + $transactions['result']->where('code', $v3)->sum('amount');
                        }
                        $res[$key][$k][$k2] = $total;
                    }
                }
            }
        }

        $totalPendapatan        = $this->sumValues($res['4-000 PENDAPATAN']);
        $totalPendapatanLainnya = $this->sumValues($res['7-000 PENDAPATAN LAINNYA']);


        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $monthNumber = $data['month'] ?? Carbon::now()->month;
        $year        = $data['year'] ?? Carbon::now()->year;

        if (isset($data['search'])) {
            $res = $this->filterAllNestedCollection($res, $data['search']);
        }

        return [
            'error' => null,
            'status' => 200,
            'result' => [
                "periode"                  => $months[(int) $monthNumber] . ' ' . $year,
                'total_pendapatan'         => $totalPendapatan,
                'total_pendapatan_lainnya' => $totalPendapatanLainnya,
                'detail'                   => $res
            ]
        ];

    }

    private function sumValues($arr): float {
        $total = 0;
        foreach ($arr as $value) {
            if (is_array($value)) {
                $total += $this->sumValues($value);
            } elseif (is_numeric($value)) {
                $total += $value;
            }
        }
        return $total;
    }

    public function export($data = [])
    {
        $data = $this->labaRugi($data);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];
        
        $spreadsheet = new Spreadsheet();

        /**
         * SHEET 1 : Ringkasan
         */
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Ringkasan');

        $row = 1;
        foreach ($data as $key => $value) {
            // skip detail biar ga dobel
            if ($key === 'detail') {
                continue;
            }

            if (is_array($value)) {
                $summarySheet->setCellValue("A{$row}", ucfirst(str_replace('_', ' ', $key)));
                $summarySheet->setCellValue("B{$row}", json_encode($value));
            } else {
                $summarySheet->setCellValue("A{$row}", ucfirst(str_replace('_', ' ', $key)));
                $summarySheet->setCellValue("B{$row}", $value);
            }
            $row++;
        }

        // auto size
        foreach (range('A', 'B') as $col) {
            $summarySheet->getColumnDimension($col)->setAutoSize(true);
        }

        /**
         * SHEET 2 : Detail
         */
        $detailSheet = $spreadsheet->createSheet();
        $detailSheet->setTitle('Detail');

        // Header
        $detailSheet->setCellValue('A1', 'Kategori');
        $detailSheet->setCellValue('B1', 'Sub Kategori');
        $detailSheet->setCellValue('C1', 'Akun');
        $detailSheet->setCellValue('D1', 'Nilai');

        $row = 2;
        foreach ($data['detail'] as $kategori => $sub) {
            foreach ($sub as $subKategori => $akun) {
                foreach ($akun as $namaAkun => $nilai) {
                    $detailSheet->setCellValue("A{$row}", $kategori);
                    $detailSheet->setCellValue("B{$row}", $subKategori);
                    $detailSheet->setCellValue("C{$row}", $namaAkun);
                    $detailSheet->setCellValue("D{$row}", $nilai ?? 0);
                    $row++;
                }
            }
        }

        // auto size kolom detail
        foreach (range('A', 'D') as $col) {
            $detailSheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [
            'error' => null,
            'status' => 200,
            'result' => $spreadsheet
        ];
    }

    public function exportCashIn($data = [])
    {
        $data = $this->cashIn($data);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];
        
        $spreadsheet = new Spreadsheet();

        /**
         * SHEET 1 : Ringkasan
         */
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Ringkasan');

        $row = 1;
        foreach ($data as $key => $value) {
            // skip detail biar ga dobel
            if ($key === 'detail') {
                continue;
            }

            if (is_array($value)) {
                $summarySheet->setCellValue("A{$row}", ucfirst(str_replace('_', ' ', $key)));
                $summarySheet->setCellValue("B{$row}", json_encode($value));
            } else {
                $summarySheet->setCellValue("A{$row}", ucfirst(str_replace('_', ' ', $key)));
                $summarySheet->setCellValue("B{$row}", $value);
            }
            $row++;
        }

        // auto size
        foreach (range('A', 'B') as $col) {
            $summarySheet->getColumnDimension($col)->setAutoSize(true);
        }

        /**
         * SHEET 2 : Detail
         */
        $detailSheet = $spreadsheet->createSheet();
        $detailSheet->setTitle('Detail');

        // Header
        $detailSheet->setCellValue('A1', 'Kategori');
        $detailSheet->setCellValue('B1', 'Sub Kategori');
        $detailSheet->setCellValue('C1', 'Akun');
        $detailSheet->setCellValue('D1', 'Nilai');

        $row = 2;
        foreach ($data['detail'] as $kategori => $sub) {
            foreach ($sub as $subKategori => $akun) {
                foreach ($akun as $namaAkun => $nilai) {
                    $detailSheet->setCellValue("A{$row}", $kategori);
                    $detailSheet->setCellValue("B{$row}", $subKategori);
                    $detailSheet->setCellValue("C{$row}", $namaAkun);
                    $detailSheet->setCellValue("D{$row}", $nilai ?? 0);
                    $row++;
                }
            }
        }

        // auto size kolom detail
        foreach (range('A', 'D') as $col) {
            $detailSheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [
            'error' => null,
            'status' => 200,
            'result' => $spreadsheet
        ];
    }

    public function neraca($data = [])
    {
        $akun = [
            "1-000 AKTIVA" => [
                "1-100 AKTIVA LANCAR" => [
                    "1-110 Kas",
                    ">> bank",
                    "1-130 Piutang Usaha",
                    "1-131 Piutang Retensi",
                    "1-132 Piutang SBUM",
                    "1-133 Piutang Karyawan",
                ],
                "1-200 AKTIVA TETAP" => [
                    "1-210 Tanah",
                    "1-220 Bangunan",
                    "1-230 Kendaraan",
                    "1-240 Peralatan & Perlengkapan",
                    "1-221 Akumulasi Penyusutan",
                    "1-250 Surat Berharga",
                ],
            ],
            "2-000 KEWAJIBAN" => [
                "2-100 Hutang" => [
                    ">> pinjaman",
                ],
            ],
            "3-000 MODAL" => [
                "3-000 MODAL" => [
                    ">> pemodalan",
                    "3-300 Prive",
                    "3-400 Laba ditahan",
                    "3-500 Laba berjalan",
                    "3-600 Laba bulan lalu",
                ],
            ],
        ];

        $res = [];
        foreach ($akun as $kategori => $sub) {
            foreach ($sub as $subKategori => $akun) {
                foreach ($akun as $subSub) {
                    if ($subSub === '>> bank') {
                        $getDataBankSaldo = $this->repository->getBankSaldo($data);
                        foreach ($getDataBankSaldo as $value) {
                            $res[$kategori][$subKategori][$value->code . ' ' . $value->name] = $value->balance;
                        }
                    } else if ($subSub === '>> pinjaman') {
                        $getDataPinjaman = $this->repository->getPinjaman($data);
                        $kode = 110;
                        foreach ($getDataPinjaman as $key => $value) {
                            $res[$kategori][$subKategori]["2-". $kode + $key . ' ' . $value->description] = $value->total_unpaid;
                        }
                    } else if ($subSub === '>> pemodalan') {
                        $getDataPinjaman = $this->repository->getPemodalan($data);
                        $kode = 110;
                        foreach ($getDataPinjaman as $key => $value) {
                            $res[$kategori][$subKategori]["3-". $kode + $key . ' ' . $value->description] = $value->total_cash_in;
                        }
                    } else if ($subSub === '1-110 Kas') {
                        $val = $this->repository->getKas($data);
                        $res[$kategori][$subKategori][$subSub] = $val ?? 0;
                    } else if ($subSub === '1-130 Piutang Usaha') {
                        $val = $this->repository->getPiutangUsaha($data);
                        $res[$kategori][$subKategori][$subSub] = $val ?? 0;
                    } else if ($subSub === '1-131 Piutang Retensi') {
                        $val = $this->repository->getPiutangRetensi($data);
                        $res[$kategori][$subKategori][$subSub] = $val ?? 0;
                    } else if ($subSub === '1-132 Piutang SBUM') {
                        $val = $this->repository->getPiutangSBUM($data);
                        $res[$kategori][$subKategori][$subSub] = $val ?? 0;
                    } else if ($subSub === '1-210 Tanah') {
                        $val = $this->repository->getNilaiAsset('tanah', $data);
                        $res[$kategori][$subKategori][$subSub] = $val ?? 0;
                    } else if ($subSub === '1-220 Bangunan') {
                        $val = $this->repository->getNilaiAsset('bangunan', $data);
                        $res[$kategori][$subKategori][$subSub] = $val ?? 0;
                    } else if ($subSub === '1-230 Kendaraan') {
                        $val = $this->repository->getNilaiAsset('kendaraan', $data);
                        $res[$kategori][$subKategori][$subSub] = $val ?? 0;
                    } else if ($subSub === '1-240 Peralatan & Perlengkapan') {
                        $val = $this->repository->getNilaiAsset('peralatan', $data);
                        $res[$kategori][$subKategori][$subSub] = $val ?? 0;
                    } else if ($subSub === '1-221 Akumulasi Penyusutan') {
                        $val = $this->repository->getNilaiAsset('akumulasi_penyusutan', $data);
                        $res[$kategori][$subKategori][$subSub] = $val ?? 0;
                    } else if ($subSub === '1-250 Surat Berharga') {
                        $val = $this->repository->getNilaiAsset('surat', $data);
                        $res[$kategori][$subKategori][$subSub] = $val ?? 0;
                    } else if ($subSub === '3-300 Prive') {
                        $val = $this->repository->getPrive($data);
                        $res[$kategori][$subKategori][$subSub] = $val ?? 0;
                    } else if ($subSub === '3-400 Laba ditahan') {
                        $val = $this->labaRugi($data, true);
                        if ($val['error']) {
                            return $val;
                        }
                        $res[$kategori][$subKategori][$subSub] = $val['result']['laba_rugi'] ?? 0;
                    } else if ($subSub === '3-500 Laba berjalan') {
                        $val = $this->labaRugi($data);
                        if ($val['error']) {
                            return $val;
                        }
                        $res[$kategori][$subKategori][$subSub] = $val['result']['laba_rugi'] ?? 0;
                    } else if ($subSub === '3-600 Laba bulan lalu') {
                        $bulanLalu = Carbon::create($data['year'] ?? Carbon::now()->year, $data['month'] ?? Carbon::now()->month, 1)->subMonth()->toDateTimeString();
                        $data['year'] = Carbon::parse($bulanLalu)->year;
                        $data['month'] = Carbon::parse($bulanLalu)->month;
                        $val = $this->labaRugi($data);
                        if ($val['error']) {
                            return $val;
                        }
                        $res[$kategori][$subKategori][$subSub] = $val['result']['laba_rugi'] ?? 0;
                    } else {
                        $res[$kategori][$subKategori][$subSub] = 0 ?? 0;
                    }
                }
            }
        }

        if (isset($data['search'])) {
            $res = $this->filterAllNestedCollection($res, $data['search']);
        }

        return [
            'error' => null,
            'status' => 200,
            'result' => $res
        ];
    }

    public function exportNeraca($data = [])
    {
        $data = $this->neraca($data);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Kelompok');
        $sheet->setCellValue('B1', 'Sub Kelompok');
        $sheet->setCellValue('C1', 'Akun');
        $sheet->setCellValue('D1', 'Saldo');

        $row = 2;

        foreach ($data as $group => $subGroups) {
            foreach ($subGroups as $subGroup => $accounts) {
                foreach ($accounts as $account => $value) {
                    $sheet->setCellValue("A{$row}", $group);
                    $sheet->setCellValue("B{$row}", $subGroup);
                    $sheet->setCellValue("C{$row}", $account);
                    $sheet->setCellValue("D{$row}", $value);
                    $row++;
                }
            }
        }

        // Auto size kolom
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [
            'error' => null,
            'status' => 200,
            'result' => $spreadsheet
        ];
    }


    private function filterNestedCollection($data, $keyword)
    {
        $filtered = collect($data)->map(function ($value, $key) use ($keyword) {
            // Jika value adalah array atau collection → rekursi
            if (is_array($value) || $value instanceof \Illuminate\Support\Collection) {
                $child = $this->filterNestedCollection($value, $keyword);
                // Hanya return jika child ada isinya
                if ($child && count($child)) {
                    return $child;
                }
            }

            // Cek apakah key terakhir mengandung keyword
            if (stripos($key, $keyword) !== false) {
                return $value;
            }

            return null;
        })->filter(); // buang item null

        return $filtered;
    }

    private function filterAllNestedCollection($data, $keyword)
    {
        return collect($data)->map(function ($value, $key) use ($keyword) {
            $keyMatch = stripos($key, $keyword) !== false;

            // Jika ada nested array → cari di dalamnya juga
            if (is_array($value) || $value instanceof \Illuminate\Support\Collection) {
                $child = $this->filterAllNestedCollection($value, $keyword);

                // Jika key cocok ATAU hasil anak tidak kosong → simpan
                if ($keyMatch || $child->isNotEmpty()) {
                    return $child->isNotEmpty() ? $child : $value;
                }
            }

            // Kalau tidak ada anak (leaf node) dan key cocok → tampilkan
            if ($keyMatch) {
                return $value;
            }

            // Tidak cocok → skip
            return null;
        })->filter(fn($v) => !is_null($v));
    }


}