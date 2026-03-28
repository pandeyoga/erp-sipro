<?php
        
namespace App\Repositories\finance;

use App\Models\BankAccounts;
use App\Models\CashFlowIn;
use App\Models\CashFlowOut;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Transaction;
use App\Models\TransferBanks;
use Aws\S3\Transfer;
use Illuminate\Support\Facades\DB;

class BankAccountRepository
{
    public function createBankAccount($data)
    {
        try {
            DB::beginTransaction();
            $bankAccount = BankAccounts::create($data);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'error' => $th->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }
        return [
            'error' => null,
            'result' => $bankAccount,
            'code' => 200
        ];
    }
    public function getBankAccounts($data)
    {
        $banksAccounts = BankAccounts::query()
            ->leftJoin('transactions', 'bank_accounts.id', '=', 'transactions.bank_account_id')
            ->when(!empty($data['search']), function ($query) use ($data) {
                $query->where('bank_accounts.name', 'ilike', '%' . $data['search'] . '%');
            })
            ->select(
                'bank_accounts.*',
                DB::raw("COALESCE(SUM(CASE WHEN transactions.type = 'in' THEN transactions.amount ELSE 0 END), 0) as total_in"),
                DB::raw("COALESCE(SUM(CASE WHEN transactions.type = 'out' THEN transactions.amount ELSE 0 END), 0) as total_out")
            )
            ->groupBy('bank_accounts.id')
            ->orderBy('bank_accounts.created_at', 'desc')
            ->when(isset($data['search']), function ($query) use ($data) {
                $query->where(function ($q) use ($data) {
                    $q->where('bank_accounts.name', 'ilike', '%' . $data['search'] . '%')
                        ->orWhere('bank_accounts.code', 'ilike', '%' . $data['search'] . '%')
                        ->orWhere('bank_accounts.account_number', 'ilike', '%' . $data['search'] . '%');
                });
            })
            ->paginate($data['per_page'], ['*'], 'page', $data['page']);
            

        return $banksAccounts;
    }

    public function getBankAccount($id)
    {
        $data = BankAccounts::where('bank_accounts.id', $id)
            ->leftJoin('transactions', 'bank_accounts.id', '=', 'transactions.bank_account_id')
            ->select(
                'bank_accounts.*',
                DB::raw("COALESCE(SUM(CASE WHEN transactions.type = 'in' THEN transactions.amount ELSE 0 END), 0) as total_in"),
                DB::raw("COALESCE(SUM(CASE WHEN transactions.type = 'out' THEN transactions.amount ELSE 0 END), 0) as total_out")
            )
            ->groupBy('bank_accounts.id')
            ->first();

        if ($data) {

            $data = [
                "id" => $data->id,
                "code" => $data->code,
                "name" => $data->name,
                "account_number" => $data->account_number,
                "opening_balance" => $data->opening_balance,
                "balance" => $data->opening_balance + $data->total_in - $data->total_out,
                "created_at" => $data->created_at
            ];
            return [
                'error' => null,
                'result' => $data,
                'code' => 200
            ];
        } else {
            return [
                'error' => 'Bank account not found',
                'result' => null,
                'code' => 404
            ];
        }
    }

    public function detailTransaction($id, $request)
    {
        $startDate = $request['start_date'] ?? null;
        $endDate = $request['end_date'] ?? null;

        $perPage = $request['per_page'] ?? 10;
        $page = $request['page'] ?? 1;

        $data = Transaction::where('transactions.bank_account_id', $id)
            ->leftJoin('cash_in_sub_sub_categories', 'transactions.sub_sub_category_id', '=', 'cash_in_sub_sub_categories.id')
            ->leftJoin('cash_out_sub_categories', 'transactions.sub_category_id', '=', 'cash_out_sub_categories.id')
            ->select(
                'transactions.*',
                'cash_in_sub_sub_categories.name as cash_in_name',
                'cash_out_sub_categories.name as cash_out_name'
            )
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('transactions.created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('transactions.created_at', '<=', $endDate);
            })
            ->orderBy('transactions.created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $data;
    }

    public function updateBankAccount($data, $id)
    {
        try {
            DB::beginTransaction();
            $bankAccount = BankAccounts::where('id', $id)->update($data);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'error' => $th->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }
        return [
            'error' => null,
            'result' => $bankAccount,
            'code' => 200
        ];
    }

    public function deleteBankAccount($id)
    {
        try {
            DB::beginTransaction();
            $bankAccount = BankAccounts::where('id', $id)->first();
            if (!$bankAccount) {
                DB::rollBack();
                return [
                    'error' => 'Bank account not found',
                    'result' => null,
                    'code' => 404
                ];
            }

            // cek jika ada cashIn / cash out / transaction yang memiliki bank account ini
            $cashIn = CashFlowIn::where('bank_account_id', $id)->first();
            $cashOut = CashFlowOut::where('bank_account_id', $id)->first();
            $transaction = Transaction::where('bank_account_id', $id)->first();

            if ($cashIn || $cashOut || $transaction) {
                DB::rollBack();
                return [
                    'error' => 'Bank account has cash in / cash out / transaction',
                    'result' => null,
                    'code' => 400
                ];
            }

            $bankAccount->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'error' => $th->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }
        return [
            'error' => null,
            'result' => $bankAccount,
            'code' => 200
        ];
    }

    public function transferSaldo($data)
    {
        try {
            $note = $data['note'] ?? null;
            DB::beginTransaction();
            $fromBankAccount = BankAccounts::where('id', $data['from_bank_account_id'])->first();
            $toBankAccount = BankAccounts::where('id', $data['to_bank_account_id'])->first();

            if (!$fromBankAccount || !$toBankAccount) {
                DB::rollBack();
                return [
                    'error' => 'Bank account not found',
                    'result' => null,
                    'code' => 404
                ];
            }
            
            $totalAmount = $data['amount'] + ($data['transfer_fee'] ?? 0);

            $parentCashOutId = $this->createCashOutfromBankAccount($fromBankAccount, $totalAmount, $note);
            $parentCashInId = $this->createCashInToBankAccount($toBankAccount, $totalAmount, $note);

            TransferBanks::create([
                'parent_cash_in_id' => $parentCashInId,
                'parent_cash_out_id' => $parentCashOutId,
                'from_bank_account_id' => $fromBankAccount->id,
                'to_bank_account_id' => $toBankAccount->id,
                'amount' => $data['amount'],
                'transfer_fee' => $data['transfer_fee'],
                'notes' => $note
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'error' => $th->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }
        return [
            'error' => null,
            'result' => null,
            'code' => 200
        ];
    }

    public function listTransfer($data)
    {
        $page = $data['page'] ?? 1;
        $perPage = $data['per_page'] ?? 10;
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;

        $data = TransferBanks::join('bank_accounts as from_bank_accounts', 'from_bank_accounts.id', '=', 'transfer_banks.from_bank_account_id')
            ->join('bank_accounts as to_bank_accounts', 'to_bank_accounts.id', '=', 'transfer_banks.to_bank_account_id')
            ->select('transfer_banks.*', 'from_bank_accounts.name as from_bank_account_name', 'to_bank_accounts.name as to_bank_account_name')
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('transfer_banks.created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('transfer_banks.created_at', '<=', $endDate);
            })
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'error' => null,
            'result' => $data,
            'code' => 200
        ];
    }

    private function createCashInToBankAccount($bankAccount, $amount, $note) : string
    {
        $category = DB::table('cash_in_categories')->where('name', 'Bank Transfer')->first();
        $subCategory = DB::table('cash_in_sub_categories')->where('name', 'Bank Transfer')->first();
        $subSubCategory = DB::table('cash_in_sub_sub_categories')->where('name', 'Bank Transfer')->first();

        $parent = CashFlowIn::create([
            'amount' => $amount,
            'category_id' => $category->id,
            'sub_category_id' => $subCategory->id,
            'sub_sub_category_id' => null,
            'description' => 'Bank Transfer',
            'parent_id' => null,
            'total_amount' => $amount,
            'paid_amount' => $amount,
            'bank_account_id' => $bankAccount->id,
            'notes' => $note
        ]);

        $child = CashFlowIn::create([
            'amount' => $amount,
            'category_id' => $category->id,
            'sub_category_id' => $subCategory->id,
            'sub_sub_category_id' => $subSubCategory->id,
            'description' => 'Bank Transfer',
            'parent_id' => $parent->id,
            'total_amount' => $amount,
            'paid_amount' => $amount,
            'bank_account_id' => $bankAccount->id,
            'notes' => $note
        ]);

        Transaction::create([
            'property_id' => null,
            'reference_id' => $child->id,
            'type' => 'in',
            'category_id' => $category->id,
            'sub_category_id' => $subCategory->id,
            'sub_sub_category_id' => $subSubCategory->id,
            'amount' => $amount,
            'notes' => $note,
            'bank_account_id' => $bankAccount->id,
        ]);

        return $parent->id;
    }

    private function createCashOutfromBankAccount($bankAccount, $amount, $note) : string
    {
        $category = DB::table('cash_out_categories')->where('name', 'Bank Transfer')->first();
        $subCategory = DB::table('cash_out_sub_categories')->where('name', 'Bank Transfer')->first();
        $cashOut = CashFlowOut::create([
            'category_id' => $category->id,
            'sub_category_id' => $subCategory->id,
            'description' => 'Bank Transfer',
            'total_amount' => $amount,
            'paid_amount' => $amount,
            'bank_account_id' => $bankAccount->id,
            'notes' => $note
        ]);

        Transaction::create([
            'property_id' => null,
            'reference_id' => $cashOut->id,
            'type' => 'out',
            'category_id' => $category->id,
            'sub_category_id' => $subCategory->id,
            'amount' => $amount,
            'notes' => $note,
            'bank_account_id' => $bankAccount->id,
        ]);

        return $cashOut->id;
    }
}