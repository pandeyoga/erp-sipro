<?php
        
namespace App\Services\finance;

use App\Repositories\finance\DebtRepository;

class DebtService
{
    public function __construct(
        protected DebtRepository $repository
    ) {}

    public function categories()
    {
        $categories = $this->repository->categories();
        return $categories;
    }

    public function create($data)
    {
        $category = $this->repository->getCategoryPinjaman();
        if ($category['error']) {
            return $category;
        }

        $subCategory = $this->repository->getSubCategory($data['category_id']);
        if ($subCategory['error']) {
            return $subCategory;
        }

        $cashInData = [
            'property_id' => null,
            'category_id' => $category['result']->id,
            'sub_category_id' => $data['category_id'],
            'total_amount' => $data['amount'],
            'description' => $data['name'],
            'bank_account_id' => $data['bank_account_id'],
            'notes' => $data['description'] ?? null
        ];
        
        $data = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'cash_in_sub_sub_category_id' => $data['category_id'],
            'bank_account_id' => $data['bank_account_id'],
            'payment_bank_account_id' => null,
            'total_amount' => $data['amount'],
            'paid_amount' => 0,
            'cash_in_id' => null,
            'cash_out_id' => null,
            'created_by' => auth()->user()->id,
        ];
        $create = $this->repository->create($data, $cashInData);
        
        return $create;
    }

    public function getAll($data = [])
    {
        $cashIns = $this->repository->getAll($data);

        if ($cashIns['error']) {
            return $cashIns;
        }

        return $cashIns;
    }

    public function getById($id)
    {
        // cek apakah ada data dengan id tersebut
        $result = $this->repository->getById($id);
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

    public function update($id, $data)
    {
        $update = $this->repository->update($id, $data);
        return $update;
    }

    public function payment($data, $id)
    {
        $payment = $this->repository->payment($data, $id);
        return $payment;
    }
    
}