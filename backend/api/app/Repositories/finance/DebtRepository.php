<?php
        
namespace App\Repositories\finance;

use App\Models\CashFlowIn;
use App\Models\CashFlowOut;
use App\Models\CashSubmission;
use App\Models\Debts;
use App\Models\Transaction;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
class DebtRepository
{
    public function categories()
    {
        $result = DB::table('cash_in_sub_categories as ssc')
            ->select(
                'ssc.id',
                'ssc.name',
            )
            ->where('code', 'ilike', 'pinjaman.%')
            ->orderBy('ssc.name')
            ->get();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getCategoryPinjaman()
    {
        $result = DB::table('cash_in_categories')
            ->select('id', 'name')
            ->where('code', 'pinjaman')
            ->first();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getSubCategory($id)
    {
        $result = DB::table('cash_in_sub_categories')
            ->where('id', $id)
            ->select('id', 'name')
            ->first();

        if (!$result) {
            return [
                'error' => 'Data not found',
                'status' => 404,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function create($data, $cashInData)
    {
        try {
            DB::beginTransaction();

            $cashInRepository = new CashInRepository();
            $cashIn = $cashInRepository->create($cashInData, true);
            if ($cashIn['status'] !== 200) {
                return $cashIn;
            }
            $cashInId = $cashIn['result']->id;
            $data['cash_in_id'] = $cashInId;

            $result = Debts::create($data);
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
        
        $result = Debts::join('cash_in_sub_categories as s', 'debts.cash_in_sub_sub_category_id', '=', 's.id')
            ->join('cash_in_categories as c', 's.category_id', '=', 'c.id')
            ->leftjoin('users as u', 'debts.created_by', '=', 'u.id')
            ->select(
                'debts.id',
                'debts.name',
                'debts.description',
                'debts.total_amount',
                'debts.paid_amount',
                DB::raw("CASE WHEN debts.total_amount::numeric = debts.paid_amount::numeric 
                  THEN 'lunas' ELSE 'belum-lunas' END as status"),
                'debts.created_at',
                'u.name as created_by_name',
                's.name as category',
            );

        if ($search) {
            $result->where('debts.name', 'ilike', '%' . $search . '%');
        }

        if ($status) {
            $status = str_replace('_', '-', $status);
            $result->where('debts.status', $status);
        }

        $result = $result->orderBy('debts.created_at', 'desc')
            ->paginate($perpage, ['*'], 'page', $page);


        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getById($id)
    {
        $result = Debts::where('debts.id', $id)
            ->join('cash_in_sub_categories as s', 'debts.cash_in_sub_sub_category_id', '=', 's.id')
            ->join('cash_in_categories as c', 's.category_id', '=', 'c.id')
            ->leftjoin('users as u', 'debts.created_by', '=', 'u.id')
            ->select(
                'debts.id',
                'debts.name',
                'debts.description',
                'debts.total_amount',
                'debts.paid_amount',
                DB::raw("CASE WHEN debts.total_amount::numeric = debts.paid_amount::numeric 
                  THEN 'lunas' ELSE 'belum-lunas' END as status"),
                'debts.created_at',
                'u.name as created_by_name',
                's.name as category',
                'debts.cash_in_id'
            )
            ->first();

        return [
            'error' => $result ? null : 'Data not found',
            'status' => $result ? 200 : 404,
            'result' => $result
        ];
    }

    public function delete($id)
    {
        $debt = Debts::where('id', $id)->first();
        if (!$debt) {
            return [
                'error' => 'Data not found',
                'status' => 404,
                'result' => null
            ];
        }
        try {
            DB::beginTransaction();
            
            // delete cash in
            $cashIn = CashFlowIn::where('id', $debt->cash_in_id)->first();
            if (!$cashIn) {
                DB::rollBack();
                return [
                    'error' => 'Cash in not found',
                    'status' => 500,
                    'result' => null
                ];
            }
            $cashIn->delete();
            $childCashIn = CashFlowIn::where('parent_id', $debt->cash_in_id)->first();

            if ($childCashIn) {
                $childId = $childCashIn->id;
                $childCashIn->delete();
                Transaction::where('reference_id', $childId)->where('type', 'in')->delete();
            }

            // delete cash out
            $cashOut = CashFlowOut::where('id', $debt->cash_out_id)->first();
            if (!$cashOut) {
                DB::rollBack();
                return [
                    'error' => 'Cash out not found',
                    'status' => 500,
                    'result' => null
                ];
            }

            $cashOut->delete();
            $transaction = Transaction::where('reference_id', $debt->cash_out_id)->where('type', 'out')->first();
            if ($transaction) {
                $transaction->delete();
            }

            $debt->delete();

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

    public function update($id, $data)
    {
        
        $debt = Debts::where('id', $id)->first();
        if (!$debt) {
            return [
                'error' => 'Data not found',
                'status' => 404,
                'result' => null
            ];
        }

        try {
            DB::beginTransaction();

            $debt->name = $data['name'];
            $debt->total_amount = $data['amount'];
            if (isset($data['description'])) {
                $debt->description = $data['description'];
            }
            $debt->cash_in_sub_sub_category_id = $data['category_id'];
            $debt->bank_account_id = $data['bank_account_id'];
            $debt->save();

            $cashIn = CashFlowIn::where('id', $debt->cash_in_id)->first();
            $cashIn->total_amount = $data['amount'];
            $cashIn->paid_amount = $data['amount'];
            $cashIn->description = $data['name'];
            $cashIn->bank_account_id = $data['bank_account_id'];
            $cashIn->save();

            $child = CashFlowIn::where('parent_id', $cashIn->id)->first();
            $child->total_amount = $data['amount'];
            $child->paid_amount = $data['amount'];
            $child->description = $data['name'];
            $child->bank_account_id = $data['bank_account_id'];
            $child->save();

            $transaction = Transaction::where('reference_id', $child->id)
                ->where('type', 'in')
                ->first();

            $transaction->amount = $data['amount'];
            $transaction->notes = $data['name'];
            $transaction->bank_account_id = $data['bank_account_id'];
            $transaction->save();

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
            'result' => null
        ];
    }

    public function payment($data, $id)
    {
        $debt = Debts::where('id', $id)->first();
        if (!$debt) {
            return [
                'error' => 'Data not found',
                'status' => 404,
                'result' => null
            ];
        }

        if ($debt->paid_amount >= $debt->total_amount) {
            return [
                'error' => 'Debt already paid',
                'status' => 400,
                'result' => null
            ];
        }

        if ($data['amount'] > $debt->total_amount - $debt->paid_amount) {
            return [
                'error' => 'Payment amount exceeds remaining amount',
                'status' => 400,
                'result' => null
            ];
        }

        try {
            DB::beginTransaction();
            
            // cek jika cash_out_id kosong
            if (!$debt->cash_out_id) {
                $categoryCashOut = DB::table('cash_out_categories')
                    ->where('code', 'pembayaran-piutang')
                    ->select('id')
                    ->first();

                $subCategoryCashOut = DB::table('cash_out_sub_categories')
                    ->where('category_id', $categoryCashOut->id)
                    ->select('id')
                    ->first();
                    
                $cashOut = CashFlowOut::create([
                    'category_id' => $categoryCashOut->id,
                    'sub_category_id' => $subCategoryCashOut->id,
                    'description' => $debt->name,
                    'total_amount' => $debt->total_amount,
                    'paid_amount' => $data['amount'],
                    'bank_account_id' => $data['bank_account_id'],
                    'notes' => $debt->description,
                ]);


                $debt->cash_out_id = $cashOut->id;
            } else {

                // cek jika ada perbedaan bank dan belum ada transaksi
                if ($data['bank_account_id'] != $debt->bank_account_id) {
                    if (Transaction::where('reference_id', $debt->cash_out_id)->where('type', 'out')->first()) {
                        DB::rollBack();
                        return [
                            'error' => 'Cannot change bank account. Cash out has transaction',
                            'status' => 400,
                            'result' => null
                        ];
                    }
                }

                $cashOut = CashFlowOut::where('id', $debt->cash_out_id)->first();
                $cashOut->paid_amount += $data['amount'];
                $cashOut->bank_account_id = $data['bank_account_id'];
                $cashOut->save();
            }

            $debt->paid_amount += $data['amount'];
            $debt->save();

            $transaction = Transaction::create([
                'reference_id' => $cashOut->id,
                'type' => 'out',
                'amount' => $data['amount'],
                'notes' => $debt->description,
                'bank_account_id' => $data['bank_account_id'],
            ]);

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
            'result' => null
        ];
    }
}