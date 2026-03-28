<?php
        
namespace App\Repositories\finance;

use App\Models\BankAccounts;
use App\Models\CashFlowOut;
use App\Models\CashSubmission;
use App\Models\Transaction;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
class CashFlowRepository
{
    public function getAll($data = [])
    {
        $perPage = $data['per_page'] ?? 20;
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;
        $page = $data['page'] ?? 1;

        $result = Transaction::leftJoin('cash_in_categories', 'transactions.category_id', '=', 'cash_in_categories.id')
            ->leftJoin('cash_in_sub_categories', 'transactions.sub_category_id', '=', 'cash_in_sub_categories.id')
            ->leftJoin('cash_in_sub_sub_categories', 'transactions.sub_sub_category_id', '=', 'cash_in_sub_sub_categories.id')
            ->leftJoin('cash_out_categories', 'transactions.category_id', '=', 'cash_out_categories.id')
            ->leftJoin('cash_out_sub_categories', 'transactions.sub_category_id', '=', 'cash_out_sub_categories.id')
            ->join('bank_accounts', 'transactions.bank_account_id', '=', 'bank_accounts.id')
            ->select(
                'transactions.*',
                'cash_in_categories.id as in_category_id',
                'cash_in_categories.name as in_category_name',
                'cash_in_sub_categories.id as in_sub_category_id',
                'cash_in_sub_categories.name as in_sub_category_name',
                'cash_in_sub_sub_categories.id as in_sub_sub_category_id',
                'cash_in_sub_sub_categories.name as in_sub_sub_category_name',
                'cash_out_categories.id as out_category_id',
                'cash_out_categories.name as out_category_name',
                'cash_out_sub_categories.id as out_sub_category_id',
                'cash_out_sub_categories.name as out_sub_category_name',
                'bank_accounts.name as bank_name',
                'bank_accounts.id as bank_id'
            )
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('transactions.created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('transactions.created_at', '<=', $endDate);
            })
            ->orderBy('transactions.created_at', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);


        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function export($data = [])
    {
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;

        $result = Transaction::leftJoin('cash_in_categories', 'transactions.category_id', '=', 'cash_in_categories.id')
            ->leftJoin('cash_in_sub_categories', 'transactions.sub_category_id', '=', 'cash_in_sub_categories.id')
            ->leftJoin('cash_in_sub_sub_categories', 'transactions.sub_sub_category_id', '=', 'cash_in_sub_sub_categories.id')
            ->leftJoin('cash_out_categories', 'transactions.category_id', '=', 'cash_out_categories.id')
            ->leftJoin('cash_out_sub_categories', 'transactions.sub_category_id', '=', 'cash_out_sub_categories.id')
            ->join('bank_accounts', 'transactions.bank_account_id', '=', 'bank_accounts.id')
            ->select(
                'transactions.*',
                'cash_in_categories.name as in_category_name',
                'cash_in_sub_categories.name as in_sub_category_name',
                'cash_in_sub_sub_categories.name as in_sub_sub_category_name',
                'cash_out_categories.name as out_category_name',
                'cash_out_sub_categories.name as out_sub_category_name',
                'bank_accounts.name as bank_name',
                'bank_accounts.id as bank_id'
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

    // getBankSaldo
    public function getBankSaldoAwal()
    {
        $result = BankAccounts::select('id', 'name', 'opening_balance as saldo')->get()->groupBy('id')->map(function ($item) {
            return $item[0]->saldo;
        });
        
        return $result;
    }
}