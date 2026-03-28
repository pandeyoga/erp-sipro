<?php
        
namespace App\Services\finance;

use App\Repositories\finance\CashFlowRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class CashFlowService
{
    public function __construct(
        protected CashFlowRepository $repository
    ) {}

    public function getAll($data = [])
    {
        $cashIns = $this->repository->getAll($data);

        if ($cashIns['error']) {
            return $cashIns;
        }
        
        $bankSaldo = $this->repository->getBankSaldoAwal();

        $item = collect($cashIns['result']->items())->map(function ($item) use ($bankSaldo) {
            if ($item->type == 'in') {
                $bankSaldo[$item->bank_id] = $bankSaldo[$item->bank_id] + $item->amount;
            } else {
                $bankSaldo[$item->bank_id] = $bankSaldo[$item->bank_id] - $item->amount;
            }

            return [
                'id' => $item->id,
                'date' => $item->created_at->format('Y-m-d H:i:s'),
                'category' => $item->type == 'in' ? $item->in_category_name : $item->out_category_name,
                'sub_category' => $item->type == 'in' ? $item->in_sub_category_name : $item->out_sub_category_name,
                'description' => $item->type == 'in' ? $item->in_sub_sub_category_name : $item->notes,
                'debit' => $item->type == 'in' ? $item->amount : "-",
                'credit' => $item->type == 'out' ? $item->amount : "-",
                'bank_name' => $item->bank_name,
                'saldo' => $bankSaldo[$item->bank_id]
            ];
        })->sortByDesc('date');

        if (isset($data['search'])) {
            $search = strtolower($data['search']);
            $item = $item->filter(function ($item) use ($search) {
                return str_contains(strtolower($item->description ?? ''), $search)
                    || str_contains(strtolower($item->category ?? ''), $search)
                    || str_contains(strtolower($item->sub_category ?? ''), $search)
                    || str_contains(strtolower($item->bank_name ?? ''), $search);
            });
        }

        // append item ke paginasi
        $cashIns['result']->setCollection($item);

        $cashIns['result']->setCollection(
            $cashIns['result']->getCollection()->values()
        );

        return $cashIns;
    }

    public function export($data = [])
    {
        $cashIns = $this->repository->export($data);

        if ($cashIns['error']) {
            return $cashIns;
        }

        $bankSaldo = $this->repository->getBankSaldoAwal();

        $items = $cashIns['result']->map(function ($item) use ($bankSaldo) {
            if ($item->type == 'in') {
                $bankSaldo[$item->bank_id] += $item->amount;
            } else {
                $bankSaldo[$item->bank_id] -= $item->amount;
            }

            return [
                'Trx Id'       => $item->id,
                'Date'         => $item->created_at->format('Y-m-d H:i:s'),
                'Category'     => $item->type == 'in' ? $item->in_category_name : $item->out_category_name,
                'Sub Category' => $item->type == 'in' ? $item->in_sub_category_name : $item->out_sub_category_name,
                'Deskripsi'    => $item->type == 'in' ? $item->in_sub_sub_category_name : $item->notes,
                'Debit'        => $item->type == 'in' ? $item->amount : 0,
                'Kredit'       => $item->type == 'out' ? $item->amount : 0,
                'Bank'         => $item->bank_name,
                'Saldo'        => $bankSaldo[$item->bank_id],
            ];
        })->sortByDesc('Date')->values();

        // Mulai bikin Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = array_keys($items->first() ?? []);
        $colIndex = 1;
        foreach ($headers as $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . '1', $header);
            $colIndex++;
        }

        // Data
        $rowIndex = 2;
        foreach ($items as $row) {
            $colIndex = 1;
            foreach ($row as $value) {
                $cell = Coordinate::stringFromColumnIndex($colIndex) . $rowIndex;
                $sheet->setCellValue($cell, $value);
                $colIndex++;
            }
            $rowIndex++;
        }

        // Styling
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        // Header bold & center
        $sheet->getStyle("A1:{$highestColumn}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-size kolom
        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format angka untuk Debit, Kredit, Saldo (misalnya kolom F, G, I)
        $sheet->getStyle("F2:F{$highestRow}")
            ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle("G2:G{$highestRow}")
            ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle("I2:I{$highestRow}")
            ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $fileName = 'cash_flow_' . $data['start_date'] . '_' . $data['end_date'] . '.xlsx';

        // Simpan ke file sementara
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}