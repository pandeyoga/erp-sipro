<?php
        
namespace App\Services\finance;

use App\Repositories\finance\BankAccountRepository;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Str;

class BankAccountService
{
    public function __construct(protected BankAccountRepository $repository) {}

    // createBankAccount
    public function createBankAccount($req)
    {
        return $this->repository->createBankAccount($req);
    }

    public function getBankAccounts($req)
    {
        $mapping = [
            'per_page' => $req['per_page'] ?? 10,
            'page' => $req['page'] ?? 1,
            'search' => $req['search'] ?? '',
        ];

        $data = $this->repository->getBankAccounts($mapping);

        $item = collect($data->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'account_number' => $item->account_number,
                'balance' => $item->opening_balance + $item->total_in - $item->total_out
            ];
        });

        $data->setCollection($item);

        return $data;
    }

    public function getBankAccount($id)
    {
        return $this->repository->getBankAccount($id);
    }

    public function update($req, $id)
    {
        return $this->repository->updateBankAccount($req, $id);
    }

    public function delete($id)
    {
        return $this->repository->deleteBankAccount($id);
    }

    public function detailTransaction($id, $req)
    {
        $data = $this->repository->detailTransaction($id, $req);

        $item = collect($data->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->type == 'in' ? $item->cash_in_name : $item->cash_out_name,
                'type' => $item->type,
                'amount' => $item->amount,
                'notes' => $item->notes,
                'date' => $item->created_at
            ];
        });

        $data->setCollection($item);

        return [
            'error' => null,
            'result' => $data,
            'code' => 200
        ];
    }

    public function transferSaldo($req)
    {
        return $this->repository->transferSaldo($req);
    }

    public function listTransfer($req)
    {
        $data = $this->repository->listTransfer($req);

        $item = collect($data['result']->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'from_bank_account' => $item->from_bank_account_name,
                'to_bank_account' => $item->to_bank_account_name,
                'amount' => $item->amount,
                'notes' => $item->notes,
                'date' => $item->created_at
            ];
        });

        $data['result']->setCollection($item);
        
        return [
            'error' => null,
            'result' => $data['result'],
            'code' => 200
        ];
    }
}