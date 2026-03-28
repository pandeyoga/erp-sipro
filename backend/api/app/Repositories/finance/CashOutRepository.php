<?php
        
namespace App\Repositories\finance;

use App\Models\BankAccounts;
use App\Models\CashFlowOut;
use App\Models\Transaction;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
class CashOutRepository
{
    public function categories()
    {
        $result = DB::table('cash_out_categories')
            ->select('id', 'name')
            ->whereNot('code', 'pembayaran-piutang')
            ->whereNot('code', 'bank-transfer')
            ->orderBy('name')
            ->get();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function subCategories($categoryId)
    {
        $result = DB::table('cash_out_sub_categories')
            ->where('category_id', $categoryId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }


    public function getCategory($id)
    {
        $result = DB::table('cash_out_categories')
            ->where('id', $id)
            ->select('id', 'name')
            ->first();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getSubCategory($id, $categoryId)
    {
        $result = DB::table('cash_out_sub_categories')
            ->where('id', $id)
            ->where('category_id', $categoryId)
            ->select('id', 'name')
            ->first();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getBankList()
    {
        return [
            'error' => null,
            'code' => 200,
            'result' => BankAccounts::select('id', 'name')->get()->toArray()
        ];
    }

    public function create($data)
    {
        try {
            DB::beginTransaction();
            $result = CashFlowOut::create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'status' => 500,
                'result' => null
            ];
        }
        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getAll($data = [])
    {
        $perpage = $data['per_page'] ?? 10;
        $page = $data['page'] ?? 1;
        $search = $data['search'] ?? null;

        $status = $data['status'] ?? null;

        $category_id = $data['category_id'] ?? null;
        $sub_category_id = $data['sub_category_id'] ?? null;

        $sort_key = $data['sortKey'] ?? 'created_at';
        $sort_dir = $data['sortDir'] ?? 'desc';

        $result = CashFlowOut::join('cash_out_categories as c', 'cash_flow_outs.category_id', '=', 'c.id')
            ->join('cash_out_sub_categories as s', 'cash_flow_outs.sub_category_id', '=', 's.id')
            ->select(
                'cash_flow_outs.id',
                'c.name as category',
                's.name as sub_category',
                'cash_flow_outs.category_id',
                'cash_flow_outs.sub_category_id',
                'cash_flow_outs.total_amount',
                'cash_flow_outs.paid_amount',
                'cash_flow_outs.description',
                'cash_flow_outs.notes',
                'cash_flow_outs.created_at',
                )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('cash_flow_outs.description', 'ilike', '%' . $search . '%')
                        ->orWhere('cash_flow_outs.notes', 'ilike', '%' . $search . '%')
                        ->orWhere('c.name', 'ilike', '%' . $search . '%')
                        ->orWhere('s.name', 'ilike', '%' . $search . '%');
                });
            })
            ->when($status, function ($query) use ($status) {
                if ($status == 'lunas') {
                    $query->whereColumn('cash_flow_outs.paid_amount', '>=', 'cash_flow_outs.total_amount');
                } elseif ($status == 'belum-lunas') {
                    $query->whereColumn('cash_flow_outs.paid_amount', '<', 'cash_flow_outs.total_amount');
                }
            })
            ->when($category_id, function ($query) use ($category_id) {
                $query->where('cash_flow_outs.category_id', $category_id);
            })
            ->when($sub_category_id, function ($query) use ($sub_category_id) {
                $query->where('cash_flow_outs.sub_category_id', $sub_category_id);
            })
            ->orderBy('cash_flow_outs.'.$sort_key, $sort_dir)
            ->paginate($perpage, ['*'], 'page', $page);

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getCategoryType($id)
    {
        $result = CashFlowOut::join('cash_out_categories as c', 'cash_flow_outs.category_id', '=', 'c.id')
            ->join('cash_out_sub_categories as s', 'cash_flow_outs.sub_category_id', '=', 's.id')
            ->leftJoin('bank_accounts as b', 'cash_flow_outs.bank_account_id', '=', 'b.id')
            ->select(
                'cash_flow_outs.id',
                'c.id as category_id',
                's.id as sub_category_id',
                'c.name as category',
                's.name as sub_category',
                'cash_flow_outs.total_amount',
                'cash_flow_outs.paid_amount',
                'cash_flow_outs.description',
                'cash_flow_outs.notes',
                'b.name as bank_account',
                'b.id as bank_account_id',
                )
            ->where('cash_flow_outs.id', $id)
            ->first();

        return [
            'error' => $result ? null : 'Data not found',
            'status' => $result ? 200 : 404,
            'result' => $result
        ];
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $cashFlowOut = CashFlowOut::where('id', $id)->get();
            if (count($cashFlowOut) == 0) {
                DB::rollBack();
                return [
                    "error" => "Cash Out not found",
                    "status" => 404,
                    "result" => null
                ];
            }
            // cek apakah sudah ada transaksi
            $transaction = Transaction::whereIn('reference_id', $cashFlowOut->pluck('id'))->first();
            if ($transaction) {
                DB::rollBack();
                return [
                    'error' => 'Cash Out has transaction, Please delete transaction first',
                    'status' => 400,
                    'result' => null
                ];
            }
            foreach ($cashFlowOut as $item) {
                $item->delete();
            }
            DB::commit();
            return [
                'error' => null,
                'status' => 200,
                'result' => null
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'status' => 500,
                'result' => null
            ];
        }
    }

    public function export($startDate, $endDate)
    {
        $result = DB::table('transactions')
        ->leftJoin('cash_flow_outs as cashout', 'transactions.reference_id', '=', 'cashout.id')
        ->join('cash_out_categories as c', 'cashout.category_id', '=', 'c.id')
        ->join('cash_out_sub_categories as s', 'cashout.sub_category_id', '=', 's.id')
        ->leftJoin('bank_accounts as ba', 'transactions.bank_account_id', '=', 'ba.id')
        ->select(
            'cashout.id as cashout_id',
            'cashout.description as cashout_description',
            'cashout.notes as cashout_notes',
            'cashout.created_at as cashout_dates',
            'cashout.total_amount as cashout_total_amount',
            'cashout.paid_amount as cashout_paid_amount',
            'transactions.id as transaction_id',
            'transactions.property_id',
            'transactions.reference_id',
            'c.name as category',
            's.name as sub_category',
            'transactions.amount',
            'transactions.notes',
            'ba.id as bank_account_id',
            'ba.name as bank_account',
            'transactions.created_at'
        )
        ->orderBy('transactions.reference_id', 'desc')
        ->whereBetween('cashout.created_at', [$startDate, $endDate])
        ->get();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];

    }

    public function deleteTransaction($id)
    {
        try {
            DB::beginTransaction();
            $transaction = Transaction::where('id', $id)->first();
            if (!$transaction) {
                DB::rollBack();
                return [
                    'error' => 'Transaction not found',
                    'status' => 404,
                    'result' => null
                ];
            }
            // kurangi paid amount
            $cashFlowOut = CashFlowOut::where('id', $transaction->reference_id)->first();
            if (!$cashFlowOut) {
                DB::rollBack();
                return [
                    'error' => 'Cash out not found',
                    'status' => 404,
                    'result' => null
                ];
            }
            $cashFlowOut->paid_amount -= $transaction->amount;
            $cashFlowOut->save();


            $transaction->delete();
            DB::commit();
            return [
                'error' => null,
                'status' => 200,
                'result' => null
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'status' => 500,
                'result' => null
            ];
        }
    }

    public function createTransaction($data): array
    {
        try {
            DB::beginTransaction();

            // cek child
            $cashFlowOut = CashFlowOut::find($data['cash_out_id']);
            if (!$cashFlowOut) {
                DB::rollBack();
                return [
                    'error' => 'Cash out not found',
                    'status' => 404,
                    'result' => null
                ];
            }

            // cek $data[bank_account_id] sama dengan $cashFlowOut->bank_account_id
            if ($data['bank_account_id'] != $cashFlowOut->bank_account_id && $cashFlowOut->bank_account_id != null) {
                if (Transaction::where('reference_id', $cashFlowOut->id)->where('type', 'out')->first()) {
                    DB::rollBack();
                    return [
                        'error' => 'Cannot change bank account. Cash out has transaction',
                        'status' => 400,
                        'result' => null
                    ];
                }
            }

            // cek apakah sudah ada bank
            if (!$cashFlowOut->bank_account_id) {
                $cashFlowOut->bank_account_id = $data['bank_account_id'];
            }

            $cashFlowOut->paid_amount += $data['amount'];

            // Validasi: paid tidak boleh lebih besar dari total
            if ($cashFlowOut->total_amount < $cashFlowOut->paid_amount) {
                DB::rollBack();
                return [
                    'error' => 'Paid Amount is greater than the total amount',
                    'status' => 400,
                    'result' => null
                ];
            }

            // Validasi: paid tidak boleh lebih besar dari total
            if ($cashFlowOut->paid_amount > $cashFlowOut->total_amount) {
                DB::rollBack();
                return [
                    'error' => 'Paid Amount is greater than the total amount',
                    'status' => 400,
                    'result' => null
                ];
            }

            $cashFlowOut->save();

            // Buat transaksi
            if ($data['amount'] > 0) {
                $transaction = Transaction::create([
                    'reference_id' => $cashFlowOut->id,
                    'type' => 'out',
                    'category_id' => $cashFlowOut->category_id,
                    'sub_category_id' => $cashFlowOut->sub_category_id,
                    'amount' => $data['amount'],
                    'notes' => $data['notes'],
                    'bank_account_id' => $cashFlowOut->bank_account_id,
                    'date' => $data['date'] ?? now()
                ]);
            }

            DB::commit();

            return [
                'error' => null,
                'status' => 201,
                'result' => [
                    'transaction_id' => isset($transaction) ? $transaction->id : null
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack(); // rollback jika terjadi exception
            return [
                'error' => $e->getMessage(),
                'status' => 500,
                'result' => null
            ];
        }
    }

    public function getTransactionByParentId($id) {
        $transactions = DB::table('transactions')
            ->join('cash_flow_outs', 'transactions.reference_id', '=', 'cash_flow_outs.id')
            ->join('cash_out_categories as c', 'cash_flow_outs.category_id', '=', 'c.id')
            ->join('cash_out_sub_categories as s', 'cash_flow_outs.sub_category_id', '=', 's.id')
            ->where('cash_flow_outs.id', $id)
            ->where('transactions.type', 'out')
            ->select(
                'transactions.id as transaction_id',
                'c.name as category',
                's.name as sub_category',
                'cash_flow_outs.description as description',
                'transactions.amount',
                'transactions.notes',
                'transactions.created_at'
                )
            ->orderBy('transactions.created_at', 'desc')
            ->paginate(20);

        return [
            'error' => null,
            'status' => 200,
            'result' => $transactions
        ];
    }

    public function update($id, $data) {
        $cashFlowOut = CashFlowOut::where('id', $id)->first();
        if (!$cashFlowOut) {
            return [
                'error' => 'Cash in not found',
                'status' => 404,
                'result' => null
            ];
        }
        if ($cashFlowOut->paid_amount > $data['total_amount']) {
            return [
                'error' => 'total_amount cannot be less than paid_amount',
                'status' => 400,
                'result' => null
            ];
        }

        $cashFlowOut->total_amount = $data['total_amount'];
        $cashFlowOut->description = $data['description'] ?? null;
        $cashFlowOut->notes = $data['notes'] ?? null;
        $cashFlowOut->bank_account_id = $data['bank_account_id'];
        $cashFlowOut->save();

        return [
            'error' => null,
            'status' => 200,
            'result' => $cashFlowOut
        ];
    }
}