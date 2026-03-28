<?php
        
namespace App\Services\finance;

use App\Repositories\finance\CashInRepository;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CashInService
{
    public function __construct(
        protected CashInRepository $repository
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

    public function subSubCategories($subCategoryId)
    {
        $subSubCategories = $this->repository->subSubCategories($subCategoryId);
        return $subSubCategories;
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

        if ($category['result']->is_property_related && !isset($data['property_id'])) {
            return [
                'error' => 'Validation error',
                'status' => 422,
                'result' => [
                    'property_id' => ['The property id field is required, if category is property related.']
                ]
            ];
        }

        $data = [
            'property_id' => $category['result']->is_property_related ? $data['property_id'] : null,
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

    public function getAll($data = [])
    {
        $cashIns = $this->repository->getAll($data);

        if ($cashIns['error']) {
            return $cashIns;
        }

        $item = $cashIns['result']->map(function ($item) {
            // $type = $item->category == 'Penjualan Rumah' ? 'property' : 'non-property-related';
            if ($item->category == 'Penjualan Rumah') {
                $type = strtolower(str_replace(' ', '-', $item->sub_category));
            } else {
                $type = "non-property-related";
            }
            return [
                'id' => $item->id,
                'property_id' => $item->property_id,
                'category_id' => $item->category_id,
                'category' => $item->category,
                'sub_category_id' => $item->sub_category_id,
                'sub_category' => $item->sub_category,
                'total_amount' => $item->total_amount,
                'paid_amount' => $item->paid_amount,
                'description' => $item->description,
                'status' => $item->total_amount == $item->paid_amount ? 'lunas' : 'belum-lunas',
                'type' => $type,
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
        $summarySheet->setTitle('Laporan Cash In');

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
                $summarySheet->setCellValue("A{$row}", '(Cash-IN) '.$value->category.' - '.$value->sub_category.' - '.$value->cashin_description);
                $summarySheet->setCellValue("B{$row}", $value->cashin_dates);
                $summarySheet->setCellValue("C{$row}", $value->cashin_total_amount);
                $summarySheet->setCellValue("D{$row}", $value->cashin_paid_amount);
                $summarySheet->setCellValue("E{$row}", $value->bank_account);
                $summarySheet->setCellValue("F{$row}", $value->cashin_notes);
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
        $cashIn = $this->repository->getCategoryType($id);
        if ($cashIn['error']) {
            return $cashIn;
        }
        
        $result = $this->repository->getDetail($id);

        if (!isset($result) && $cashIn['error']) {
            return [
                'error' => 'Data not found',
                'status' => 404,
                'result' => null
            ];
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

    public function getPropertyList()
    {
        $propertyList = $this->repository->getPropertyList();
        return $propertyList;
    }

    public function createTransaction($data)
    {
        // check if change bank and have transaction
        if (isset($data['bank_account_id'])) {
            $hasTransactionChangeBank = $this->repository->check($data['cash_in_id'], $data['bank_account_id']);
            if ($hasTransactionChangeBank['error']) {
                    return $hasTransactionChangeBank;
            }
        }

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