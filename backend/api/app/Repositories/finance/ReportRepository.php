<?php
        
namespace App\Repositories\finance;

use App\Models\BankAccounts;
use App\Models\Debts;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportRepository
{

    public function getAllCashInCashOut($data = [], $isAll = false)
    {
        $year = $data['year'] ?? Carbon::now()->year;
        $month = $data['month'] ?? Carbon::now()->month;

        $result = Transaction::leftJoin('cash_in_sub_sub_categories', 'transactions.sub_sub_category_id', '=', 'cash_in_sub_sub_categories.id')
            ->leftJoin('cash_out_sub_categories', 'transactions.sub_category_id', '=', 'cash_out_sub_categories.id')
            ->select(
                'transactions.id',
                'transactions.type',
                'transactions.amount',
                'cash_in_sub_sub_categories.code as in_code',
                'cash_out_sub_categories.code as out_code',
            )
            ->when($isAll === false, function ($query) use ($month, $year) {
                $query->whereMonth('transactions.created_at', $month)
                ->whereYear('transactions.created_at', $year);
            })
            ->when($isAll, function ($query) use ($month, $year) {
                $dateMinTwoMonth = Carbon::create($year, $month, 1)->subMonth(2)->toDateTimeString();
                $month = Date('m', strtotime($dateMinTwoMonth));
                $year = Date('Y', strtotime($dateMinTwoMonth));
                $query->whereMonth('transactions.created_at', '<=', (int) $month)
                ->whereYear('transactions.created_at', '<=', (int) $year);
            })
            ->orderBy('transactions.created_at', 'asc')
            ->get()->map(function ($item) {
                return [
                    'amount' => $item->amount,
                    'code' => $item->in_code ?? $item->out_code,
                ];
            });



        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getBebanPenyusutan($data = [], $isAll = false)
    {
        if ($isAll) {
            $year = $data['year'] ?? Carbon::now()->year;
            $month = $data['month'] ?? Carbon::now()->month;

            $dateMinTwoMonth = Carbon::create($year, $month, 1)->subMonth(2)->toDateTimeString();
            $month = (int) Date('m', strtotime($dateMinTwoMonth));
            $year = (int) Date('Y', strtotime($dateMinTwoMonth));
        } else {
            $year = $data['year'] ?? Carbon::now()->year;
            $month = $data['month'] ?? Carbon::now()->month;
        }

        $untilDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateTimeString();

        $depreciation = DB::table('assets')
                ->selectRaw("
                    SUM(
                        LEAST(
                            (
                                EXTRACT(YEAR FROM AGE(?, acquisition_date)) * 12 +
                                EXTRACT(MONTH FROM AGE(?, acquisition_date))
                            ),
                            useful_life
                        ) * (price / useful_life)
                    ) as total_depreciation
                ", [$untilDate, $untilDate])
                ->where('has_depreciation', true)
                ->value('total_depreciation');

        return [
            'error' => null,
            'status' => 200,
            'result' => $depreciation
        ];
    }

    public function getAll($data = [])
    {
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;

        $result = Transaction::leftJoin('cash_in_sub_sub_categories', 'transactions.sub_sub_category_id', '=', 'cash_in_sub_sub_categories.id')
            ->leftJoin('cash_out_sub_categories', 'transactions.sub_category_id', '=', 'cash_out_sub_categories.id')
            ->select(
                'transactions.id',
                'transactions.type',
                'transactions.amount',
                'cash_in_sub_sub_categories.code as in_code',
                'cash_out_sub_categories.code as out_code',
            )
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('transactions.created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('transactions.created_at', '<=', $endDate);
            })
            ->orderBy('transactions.created_at', 'asc')
            ->get();


        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getBankSaldoAwal()
    {
        $result = BankAccounts::select('id', 'name', 'opening_balance as saldo')->get()->groupBy('id')->map(function ($item) {
            return $item[0]->saldo;
        });
        
        return $result;
    }

    // getBankSaldo
    public function getBankSaldo($data)
    {
        $bulan = $data['month'] ?? Carbon::now()->month;
        $tahun = $data['year'] ?? Carbon::now()->year;

        $result = DB::table('bank_accounts as b')
            ->select(
                'b.id',
                'b.code',
                'b.name',
                'b.account_number',
                'b.opening_balance',
                DB::raw("
                    b.opening_balance 
                    + COALESCE(SUM(CASE WHEN t.type = 'in' THEN t.amount ELSE 0 END), 0)
                    - COALESCE(SUM(CASE WHEN t.type = 'out' THEN t.amount ELSE 0 END), 0)
                    as balance
                ")
            )
            ->leftJoin('transactions as t', 't.bank_account_id', '=', 'b.id')
            ->where(function ($query) use ($bulan, $tahun) {
                $query->whereDate('t.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
                    ->orWhereNull('t.created_at');
            })
            ->whereDate('b.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
            ->groupBy('b.id', 'b.code', 'b.name', 'b.account_number', 'b.opening_balance')
            ->get();

        return $result;
    }

    // public function getPinjaman($data)
    // {
    //     $bulan = $data['month'] ?? Carbon::now()->month;
    //     $tahun = $data['year'] ?? Carbon::now()->year;

    //     $subCategory = DB::table('cash_in_sub_sub_categories')->where('code', 'ilike', 'pinjaman.%')->pluck('id')->toArray();

    //     $data = DB::table('cash_flow_ins as cfi')
    //         ->whereIn('cfi.sub_sub_category_id', $subCategory)
    //         ->leftJoin('transactions as t', 't.reference_id', '=', 'cfi.id')
    //         ->select(
    //             'cfi.description',
    //             DB::raw('SUM(cfi.total_amount) as total_cash_in'),
    //             DB::raw('COALESCE(SUM(t.amount), 0) as total_paid'),
    //             DB::raw('(SUM(cfi.total_amount) - COALESCE(SUM(t.amount), 0)) as total_unpaid')
    //         )
    //         ->whereMonth('cfi.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
    //         ->where(function ($query) use ($bulan, $tahun) {
    //             $query->whereDate('t.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
    //                 ->orWhereNull('t.created_at');
    //         })
    //         ->groupBy('cfi.description')
    //         ->get();

    //     return $data;
    // }

    public function getPinjaman($data)
    {
        $bulan = $data['month'] ?? Carbon::now()->month;
        $tahun = $data['year'] ?? Carbon::now()->year;

        $endOfMonth = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $data = Debts::select(
                'name as description',
                DB::raw('SUM(total_amount::numeric) as total_cash_in'),
                DB::raw('COALESCE(SUM(paid_amount::numeric), 0) as total_paid'),
                DB::raw('(SUM(total_amount::numeric) - COALESCE(SUM(paid_amount::numeric), 0)) as total_unpaid')
            )
            ->whereDate('created_at', '<=', $endOfMonth)
            ->groupBy('name')
            ->get();

        return $data;
    }

    public function getPemodalan($data)
    {
        $bulan = $data['month'] ?? Carbon::now()->month;
        $tahun = $data['year'] ?? Carbon::now()->year;

        $subCategory = DB::table('cash_in_sub_sub_categories')->where('code', 'ilike', 'pemodalan.%')->pluck('id')->toArray();

        $data = DB::table('cash_flow_ins as cfi')
            ->whereIn('cfi.sub_sub_category_id', $subCategory)
            ->select(
                'cfi.description',
                DB::raw('SUM(cfi.total_amount) as total_cash_in'),
            )
            ->whereDate('cfi.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
            ->groupBy('cfi.description')
            ->get();

        return $data;
    }

    public function getKas($data)
    {
        $bulan = $data['month'] ?? Carbon::now()->month;
        $tahun = $data['year'] ?? Carbon::now()->year;

        $perAccountQuery = DB::table('bank_accounts as b')
            ->select(
                'b.id',
                'b.code',
                'b.name',
                'b.account_number',
                'b.opening_balance',
                DB::raw("
                    b.opening_balance 
                    + COALESCE(SUM(CASE WHEN t.type = 'in' THEN t.amount ELSE 0 END), 0)
                    - COALESCE(SUM(CASE WHEN t.type = 'out' THEN t.amount ELSE 0 END), 0)
                    as balance
                ")
            )
            ->leftJoin('transactions as t', 't.bank_account_id', '=', 'b.id')
            ->where(function ($query) use ($bulan, $tahun) {
                $query->whereDate('t.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
                    ->orWhereNull('t.created_at');
            })
            ->whereDate('b.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
            ->groupBy('b.id', 'b.code', 'b.name', 'b.account_number', 'b.opening_balance');

        $totalSaldo = DB::table(DB::raw("({$perAccountQuery->toSql()}) as sub"))
            ->mergeBindings($perAccountQuery)
            ->sum('balance');

        return $totalSaldo;
    }

    // getPiutangUsaha
    public function getPiutangUsaha($data)
    {
        $bulan = $data['month'] ?? Carbon::now()->month;
        $tahun = $data['year'] ?? Carbon::now()->year;

        $subCategory = DB::table('cash_in_sub_sub_categories')
            ->whereNot('code', 'ilike', 'penjualan-rumah.kpr.pencairan-kpr.%')
            ->whereNot('code', 'ilike', 'pencarian-sbum.%')
            ->whereNot('code', 'ilike', 'pinjaman.%')
            ->pluck('id')->toArray();

        $data = DB::table('cash_flow_ins as cfi')
            ->whereIn('cfi.sub_sub_category_id', $subCategory)
            ->leftJoin('transactions as t', 't.reference_id', '=', 'cfi.id')
            ->select(
                DB::raw('(SUM(cfi.total_amount) - COALESCE(SUM(t.amount), 0)) as total_unpaid')
            )
            ->whereDate('cfi.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
            ->where(function ($query) use ($bulan, $tahun) {
                $query->whereDate('t.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
                    ->orWhereNull('t.created_at');
            })
            ->value('total_unpaid');

        return $data;
    }

    // getPiutangRetensi
    public function getPiutangRetensi($data)
    {
        $bulan = $data['month'] ?? Carbon::now()->month;
        $tahun = $data['year'] ?? Carbon::now()->year;

        $subCategory = DB::table('cash_in_sub_sub_categories')
            ->where('code', 'ilike', 'penjualan-rumah.kpr.pencairan-kpr.%')
            ->pluck('id')->toArray();

        $data = DB::table('cash_flow_ins as cfi')
            ->whereIn('cfi.sub_sub_category_id', $subCategory)
            ->leftJoin('transactions as t', 't.reference_id', '=', 'cfi.id')
            ->select(
                DB::raw('(SUM(cfi.total_amount) - COALESCE(SUM(t.amount), 0)) as total_unpaid')
            )
            ->whereDate('cfi.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
            ->where(function ($query) use ($bulan, $tahun) {
                $query->whereDate('t.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
                    ->orWhereNull('t.created_at');
            })
            ->value('total_unpaid');

        return $data;
    }

    public function getPiutangSBUM($data)
    {
        $bulan = $data['month'] ?? Carbon::now()->month;
        $tahun = $data['year'] ?? Carbon::now()->year;

        $subCategory = DB::table('cash_in_sub_sub_categories')
            ->where('code', 'ilike', 'pencarian-sbum.%')
            ->pluck('id')->toArray();

        $data = DB::table('cash_flow_ins as cfi')
            ->whereIn('cfi.sub_sub_category_id', $subCategory)
            ->leftJoin('transactions as t', 't.reference_id', '=', 'cfi.id')
            ->select(
                DB::raw('(SUM(cfi.total_amount) - COALESCE(SUM(t.amount), 0)) as total_unpaid')
            )
            ->whereDate('cfi.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
            ->where(function ($query) use ($bulan, $tahun) {
                $query->whereDate('t.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
                    ->orWhereNull('t.created_at');
            })
            ->value('total_unpaid');

        return $data;
    }

    public function getNilaiAsset($type, $data)
    {
        $bulan = $data['month'] ?? Carbon::now()->month;
        $tahun = $data['year'] ?? Carbon::now()->year;

        if ($type !== "akumulasi_penyusutan") {
          $mapping = [
            "tanah" => "Tanah",
            "bangunan" => "Bangunan",
            "kendaraan" => "Kendaraan",
            "peralatan" => "Peralatan & Perlengkapan",
            "surat" => "Surat Berharga",
          ];

          if (!isset($mapping[$type])) {
            return 0;
          } else {
            $type = $mapping[$type];
          }
          
          $assets = DB::table('assets as a')
            ->join('asset_categories as c', 'a.category_id', '=', 'c.id')
            ->selectRaw('SUM(a.price) as price')
            ->where('c.name', $type)
            ->whereDate('a.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateString())
            ->value('price');

          return $assets;
        } else {
            $data = $this->getBebanPenyusutan($data);

            return $data['result'] ?? 0;
        }
    }

    public function getPrive($data) {
        $bulan = $data['month'] ?? Carbon::now()->month;
        $tahun = $data['year'] ?? Carbon::now()->year;

        $subCategory = DB::table('cash_out_sub_categories')
            ->where('code', 'ilike', 'tarikan.%')
            ->pluck('id')->toArray();

        $data = DB::table('transactions as t')
            ->select(
                DB::raw('SUM(t.amount) as total_amount')
            )
            ->whereMonth('t.created_at', '<=', Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateTimeString())
            ->whereIn('t.sub_category_id', $subCategory)
            ->value('total_amount');

        return $data;
    }
}