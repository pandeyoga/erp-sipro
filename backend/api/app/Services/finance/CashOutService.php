<?php
        
namespace App\Services\finance;

use App\Repositories\finance\CashOutRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CashOutService
{
    public function __construct(
        protected CashOutRepository $repository
    ) {}

    public function categories()
    {
        $categories = $this->repository->categories();
        return $categories;
    }

    public function subCategories($categoryId)
    {
        $subCategories = $this->repository->subCategories($categoryId);
        return $subCategories;
    }

    public function getBankList()
    {
        $data = $this->repository->getBankList();
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];
        
        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function create($data)
    {
        $category = $this->repository->getCategory($data['category_id']);
        if ($category['error']) {
            return $category;
        }

        $subCategory = $this->repository->getSubCategory($data['sub_category_id'], $data['category_id']);
        if ($subCategory['error']) {
            return $subCategory;
        }

        $data = [
            'category_id' => $data['category_id'],
            'sub_category_id' => $data['sub_category_id'],
            'total_amount' => $data['total_amount'],
            'description' => $data['description'],
            'bank_account_id' => $data['bank_account_id'],
            'notes' => $data['notes'] ?? null
        ];
        
        $create = $this->repository->create($data);
        
        return $create;
    }

    public function getAll($data)
    {
        $cashIns = $this->repository->getAll($data);

        if ($cashIns['error']) {
            return $cashIns;
        }

        $item = $cashIns['result']->map(function ($item) {
            return [
                'id' => $item->id,
                'category_id' => $item->category_id,
                'category' => $item->category,
                'sub_category' => $item->sub_category,
                'total_amount' => $item->total_amount,
                'paid_amount' => $item->paid_amount,
                'description' => $item->description,
                'status' => $item->total_amount == $item->paid_amount ? 'lunas' : 'belum-lunas',
                'created_at' => date('Y-m-d', strtotime($item->created_at)),
                'notes' => $item->notes,
            ];
        });

        // append item ke paginasi
        $cashIns['result']->setCollection($item);

        return $cashIns;
    }

    public function export($startDate, $endDate)
    {

        $data = $this->repository->export($startDate, $endDate);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];
        
        $spreadsheet = new Spreadsheet();

        /**
         * SHEET 1 : Ringkasan
         */
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Laporan Cash Out');

        $row = 1;

        $summarySheet->setCellValue("A{$row}", "Description");
        $summarySheet->setCellValue("B{$row}", "Date");
        $summarySheet->setCellValue("C{$row}", "Total Amount");
        $summarySheet->setCellValue("D{$row}", "Paid Amount");
        $summarySheet->setCellValue("E{$row}", "Bank Account");
        $summarySheet->setCellValue("F{$row}", "Notes");

        $row++;
        $cashinIds = [];
        $transaction_count = 1;
        foreach ($data as $value) {

            if(!in_array($value->reference_id,$cashinIds)){
                $transaction_count = 1;
                $summarySheet->setCellValue("A{$row}", '(Cash-Out) '.$value->category.' - '.$value->sub_category.' - '.$value->cashout_description);
                $summarySheet->setCellValue("B{$row}", $value->cashout_dates);
                $summarySheet->setCellValue("C{$row}", $value->cashout_total_amount);
                $summarySheet->setCellValue("D{$row}", $value->cashout_paid_amount);
                $summarySheet->setCellValue("E{$row}", $value->bank_account);
                $summarySheet->setCellValue("F{$row}", $value->cashout_notes);
                $row++;
                $summarySheet->setCellValue("A{$row}", '(Transaksi ke-'.$transaction_count.')');
                $summarySheet->setCellValue("B{$row}", $value->created_at);
                $summarySheet->setCellValue("C{$row}", $value->amount);
                $summarySheet->setCellValue("D{$row}", $value->amount);
                $summarySheet->setCellValue("E{$row}", $value->bank_account);
                $summarySheet->setCellValue("F{$row}", $value->notes);
                $transaction_count++;

                $cashinIds[] = $value->reference_id;
            }else{
                $summarySheet->setCellValue("A{$row}", '(Transaksi ke-'.$transaction_count.')');
                $summarySheet->setCellValue("B{$row}", $value->created_at);
                $summarySheet->setCellValue("C{$row}", $value->amount);
                $summarySheet->setCellValue("D{$row}", $value->amount);
                $summarySheet->setCellValue("E{$row}", $value->bank_account);
                $summarySheet->setCellValue("F{$row}", $value->notes);
                $transaction_count++;
            }
            $row++;
        }

        // auto size
        foreach (range('A', 'F') as $col) {
            $summarySheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [
            'error' => null,
            'status' => 200,
            'result' => $spreadsheet
        ];
    }

    public function getById($id)
    {
        // cek apakah ada data dengan id tersebut
        $result = $this->repository->getCategoryType($id);
        if ($result['error']) {
            return $result;
        }

        return [
            'error' => null,
            'status' => 200,
            'result' => $result['result']
        ];
        
    }

    public function delete($id)
    {
        $delete = $this->repository->delete($id);
        return $delete;
    }

    public function createTransaction($data)
    {
        $create = $this->repository->createTransaction($data);
        return $create;
    }

    public function deleteTransaction($id)
    {
        $delete = $this->repository->deleteTransaction($id);
        return $delete;
    }

    public function getTransactionByParentId($id)
    {
        $transactions = $this->repository->getTransactionByParentId($id);
        return $transactions;
    }

    public function update($id, $data)
    {
        $update = $this->repository->update($id, $data);
        return $update;
    }
    
}