<?php
        
namespace App\Repositories\finance;

use App\Models\BankAccounts;
use App\Models\CashFlowIn;
use App\Models\Lead;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\UnitProperty;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class CashInRepository
{
    public function categories()
    {
        $result = DB::table('cash_in_categories')
            ->select('id', 'name')
            ->orderBy('name')
            ->whereNot('code', 'pinjaman')
            ->whereNot('code', 'bank-transfer')
            ->get();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function subCategories($categoryId)
    {
        $result = DB::table('cash_in_sub_categories')
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

    public function subSubCategories($subCategoryId)
    {
        $result = DB::table('cash_in_sub_sub_categories as ssc')
            ->join('cash_in_sub_sub_groups as ssg', 'ssc.sub_sub_group_id', '=', 'ssg.id')
            ->where('ssc.sub_category_id', $subCategoryId)
            ->select(
                'ssc.id',
                'ssc.name',
                'ssc.is_custom_input',
                'ssg.id as group_id',
                'ssg.name as group_name'
            )
            ->orderBy('ssg.name')
            ->orderBy('ssc.name')
            ->get()->groupBy('group_name')->map(function ($group) {
                return $group->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'custom_input_description' => $item->is_custom_input,
                    ];
                });
            });

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

    public function getCategory($id)
    {
        $result = DB::table('cash_in_categories')
            ->where('id', $id)
            ->select('id', 'name', 'is_property_related')
            ->first();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getSubCategory($id, $categoryId)
    {
        $result = DB::table('cash_in_sub_categories')
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

    

    public function create($data, $autoPayment = false)
    {
        try {
            DB::beginTransaction();
            $result = CashFlowIn::create($data);

            $this->createChilds($result->id, $data['sub_category_id'], $data['category_id'], $data['property_id'], $data['description'], $data['total_amount'], $data['bank_account_id'], $autoPayment, $data['custom_date'] ?? null);
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

    private function createChilds($parentId, $subCategoryId, $categoryId, $propertyId = null, $description = null, $parentAmount = null, $bankAccountId = null, $autoPayment = false, $customDate = null)
    {
        $subSub = DB::table('cash_in_sub_sub_categories')
            ->where('sub_category_id', $subCategoryId)
            ->select(
                'id',
                'name as sub_sub_category'
            )
            ->get();

        foreach ($subSub as $sub) {
            $cashFlowIn = new CashFlowIn();
            $cashFlowIn->parent_id = $parentId;
            $cashFlowIn->property_id = $propertyId;
            $cashFlowIn->category_id = $categoryId;
            $cashFlowIn->sub_category_id = $subCategoryId;
            $cashFlowIn->sub_sub_category_id = $sub->id;
            $cashFlowIn->total_amount = $propertyId == null ? $parentAmount : 0;
            $cashFlowIn->description = $propertyId == null ? $description : $sub->sub_sub_category ?? $description;
            $cashFlowIn->bank_account_id = $bankAccountId;
            $cashFlowIn->save();

            if ($autoPayment) {
                $tc = $this->createTransaction([
                    'cash_in_id' => $cashFlowIn->id,
                    'amount' => $parentAmount,
                    'bank_account_id' => $bankAccountId,
                    'property_id' => $propertyId,
                    'total_amount' => $parentAmount,
                    'amount' => $parentAmount,
                    'bank_account_id' => $bankAccountId,
                    'date' => $customDate,
                    'parent_id' => $parentId,
                    'notes' => $description
                ]);
            }
        }

    }

    public function getAll($data = [])
    {
        $perpage = $data['per_page'] ?? 10;
        $search = $data['search'] ?? null;
        $page = $data['page'] ?? 1;

        $status = $data['status'] ?? null;

        $category_id = $data['category_id'] ?? null;
        $sub_category_id = $data['sub_category_id'] ?? null;

        $sort_key = $data['sortKey'] ?? 'created_at';
        $sort_dir = $data['sortDir'] ?? 'desc';

        $result = CashFlowIn::join('cash_in_categories as c', 'cash_flow_ins.category_id', '=', 'c.id')
            ->join('cash_in_sub_categories as s', 'cash_flow_ins.sub_category_id', '=', 's.id')
            ->where('cash_flow_ins.parent_id', null)
            ->select(
                'cash_flow_ins.id',
                'cash_flow_ins.property_id',
                'c.name as category',
                's.name as sub_category',
                'cash_flow_ins.category_id',
                'cash_flow_ins.sub_category_id',
                'cash_flow_ins.total_amount',
                'cash_flow_ins.paid_amount',
                'cash_flow_ins.description',
                'cash_flow_ins.notes',
                'cash_flow_ins.created_at',
                )
            ->orderBy('cash_flow_ins.'.$sort_key, $sort_dir)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('cash_flow_ins.description', 'ilike', '%' . $search . '%')
                        ->orWhere('cash_flow_ins.notes', 'ilike', '%' . $search . '%')
                        ->orWhere('c.name', 'ilike', '%' . $search . '%')
                        ->orWhere('s.name', 'ilike', '%' . $search . '%');
                });
            })
            ->when($status, function ($query) use ($status) {
                if ($status == 'lunas') {
                    $query->whereColumn('cash_flow_ins.paid_amount', '>=', 'cash_flow_ins.total_amount');
                } elseif ($status == 'belum-lunas') {
                    $query->whereColumn('cash_flow_ins.paid_amount', '<', 'cash_flow_ins.total_amount');
                }
            })
            ->when($category_id, function ($query) use ($category_id) {
                $query->where('cash_flow_ins.category_id', $category_id);
            })
            ->when($sub_category_id, function ($query) use ($sub_category_id) {
                $query->where('cash_flow_ins.sub_category_id', $sub_category_id);
            })
            ->paginate($perpage, ['*'], 'page', $page);
        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getCategoryType($id)
    {
        $result = CashFlowIn::join('cash_in_categories as c', 'cash_flow_ins.category_id', '=', 'c.id')
            ->join('cash_in_sub_categories as s', 'cash_flow_ins.sub_category_id', '=', 's.id')
            ->select(
                'cash_flow_ins.id',
                'cash_flow_ins.property_id',
                'c.id as category_id',
                's.id as sub_category_id',
                'c.name as category',
                's.name as sub_category',
                'c.is_property_related',
                )
            ->where('cash_flow_ins.id', $id)
            ->first();

        return [
            'error' => $result ? null : 'Data not found',
            'status' => $result ? 200 : 404,
            'result' => $result
        ];
    }

    public function getPropertyList()
    {
        $propertyList = DB::table('unit_properties')
            ->leftJoin('projects', 'unit_properties.project_id', '=', 'projects.id')
            ->leftJoin('clusters', 'unit_properties.cluster_id', '=', 'clusters.id')
            ->leftJoin('units', 'unit_properties.unit_type_id', '=', 'units.id')
            ->leftJoin('cash_flow_ins', 'unit_properties.id', '=', 'cash_flow_ins.property_id')
            ->whereNull('cash_flow_ins.id')
            ->select(
                'unit_properties.id',
                'unit_properties.unit_number as unit_number',
                'clusters.name as cluster',
                'projects.name as project',
                'units.type as unit_type',
            )
            ->get();
        return [
            'error' => null,
            'status' => 200,
            'result' => $propertyList
        ];
    }

    public function getDetail($id)
    {
        $result = CashFlowIn::join('cash_in_categories as c', 'cash_flow_ins.category_id', '=', 'c.id')
            ->join('cash_in_sub_categories as s', 'cash_flow_ins.sub_category_id', '=', 's.id')
            ->leftJoin('unit_properties', 'cash_flow_ins.property_id', '=', 'unit_properties.id')
            ->leftJoin('projects', 'unit_properties.project_id', '=', 'projects.id')
            ->leftJoin('clusters', 'unit_properties.cluster_id', '=', 'clusters.id')
            ->leftJoin('units', 'unit_properties.unit_type_id', '=', 'units.id')
            ->leftJoin('bank_accounts', 'cash_flow_ins.bank_account_id', '=', 'bank_accounts.id')
            ->select(
                'cash_flow_ins.id',
                'cash_flow_ins.property_id',
                'unit_properties.unit_number as unit_number',
                'clusters.name as cluster',
                'projects.name as project',
                'units.type as unit_type',
                'cash_flow_ins.category_id',
                'cash_flow_ins.sub_category_id',
                'c.name as category',
                's.name as sub_category',
                'cash_flow_ins.total_amount',
                'cash_flow_ins.paid_amount',
                'cash_flow_ins.description',
                'cash_flow_ins.notes',
                'cash_flow_ins.created_at',
                'bank_accounts.name as bank_account',
                'bank_accounts.id as bank_account_id'
                )
            ->where('cash_flow_ins.id', $id)
            ->first();
        
        $result->property_name = null;
        if ($result->property_id) {
            $result->property_name = $result->unit_number . ' - ' . $result->cluster . ' - ' . $result->project . ' - ' . $result->unit_type;
        }

        // remove unit_number, cluster, project, unit_type
        unset($result->unit_number);
        unset($result->cluster);
        unset($result->project);
        unset($result->unit_type);

        $child = DB::table('cash_flow_ins')
            ->where('parent_id', $id)
            ->join('cash_in_sub_sub_categories as ssc', 'cash_flow_ins.sub_sub_category_id', '=', 'ssc.id')
            ->join('cash_in_sub_sub_groups as ssg', 'ssc.sub_sub_group_id', '=', 'ssg.id')
            ->leftJoin('bank_accounts', 'cash_flow_ins.bank_account_id', '=', 'bank_accounts.id')
            ->select(
                'cash_flow_ins.id',
                'cash_flow_ins.total_amount',
                'cash_flow_ins.paid_amount',
                'cash_flow_ins.description',
                'ssg.name as sub_sub_group',
                'bank_accounts.name as bank_account',
                'bank_accounts.id as bank_account_id'
                )
            ->orderBy('ssg.name', 'desc')
            ->orderBy('cash_flow_ins.created_at', 'desc')
            ->get()->groupBy('sub_sub_group')->map(function ($group) {
                return $group->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->description,
                        'total_amount' => $item->total_amount,
                        'paid_amount' => $item->paid_amount,
                        'bank_account_id' => $item->bank_account_id,
                        'bank_account' => $item->bank_account,
                    ];
                });
            })->sortKeys();

        $result->child = $child;

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $cashFlowIn = CashFlowIn::where('id', $id)
                ->orWhere('parent_id', $id)
                ->get();

            if ($cashFlowIn->isEmpty()) {
                DB::rollBack();
                return [
                    'error' => 'Cash in not found',
                    'status' => 404,
                    'result' => null
                ];
            }

            $cashFlowInIds = $cashFlowIn->pluck('id')->toArray();

            $transaction = Transaction::whereIn('reference_id', $cashFlowInIds)
                ->where('type', 'in')
                ->exists(); // lebih cepat daripada get()

            if ($transaction) {
                DB::rollBack();
                return [
                    'error' => 'Cash in has transaction, Please delete transaction first',
                    'status' => 400,
                    'result' => null
                ];
            }

            CashFlowIn::whereIn('id', $cashFlowInIds)->delete();

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
        ->leftJoin('cash_flow_ins as ci', 'transactions.reference_id', '=', 'ci.id')
        ->join('cash_in_categories as c', 'ci.category_id', '=', 'c.id')
        ->join('cash_in_sub_categories as s', 'ci.sub_category_id', '=', 's.id')
        ->join('cash_in_sub_sub_categories as ssc', 'ci.sub_sub_category_id', '=', 'ssc.id')
        ->leftJoin('bank_accounts as ba', 'transactions.bank_account_id', '=', 'ba.id')
        ->select(
            'ci.id as cashin_id',
            'ci.description as cashin_description',
            'ci.notes as cashin_notes',
            'ci.created_at as cashin_dates',
            'ci.total_amount as cashin_total_amount',
            'ci.paid_amount as cashin_paid_amount',
            'transactions.id as transaction_id',
            'transactions.property_id',
            'transactions.reference_id',
            'c.name as category',
            's.name as sub_category',
            'ssc.name as description',
            'transactions.amount',
            'transactions.notes',
            'ba.id as bank_account_id',
            'ba.name as bank_account',
            'transactions.created_at'
        )
        ->orderBy('transactions.reference_id', 'desc')
        ->whereBetween('ci.created_at', [$startDate, $endDate])
        ->get();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];

    }

    public function createCashinForReservation($cashInData)
    {
        try {
            DB::beginTransaction();
            
            $lead = Lead::join('contacts', 'leads.contact_id', '=', 'contacts.id')
                ->where('leads.id', $cashInData['lead_id'])
                ->select(
                    'leads.id as lead_id',
                    'contacts.name',
                )
                ->first();
                
            $categoryId = DB::table('cash_in_categories')->where('name', 'Booking')->first()->id;
            $subCategoryId = DB::table('cash_in_sub_categories')->where('name', 'biaya booking')->first()->id;
            $subSubCategoryId = DB::table('cash_in_sub_sub_categories')->where('name', 'biaya booking')->first()->id;

            $propertyUnit = UnitProperty::select('price','clusters.block_code', 'unit_number')
                ->join('clusters', 'unit_properties.cluster_id', '=', 'clusters.id')
                ->where('unit_properties.id', $cashInData['property_unit_id'])->first();

            if ($propertyUnit) {
                $notes = 'Booking Atas Nama : ' . $lead->name . ' ,unit ' . $propertyUnit->block_code . '-' . $propertyUnit->unit_number;
            } else {
                $notes = 'Booking Atas Nama : ' . $lead->name;
            }

            $parent = CashFlowIn::create([
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'description' => 'Biaya Booking',
                'total_amount' => $cashInData['reservation_fee'],
                'paid_amount' => 0,
                'notes' => $notes
            ]);

            $cashFlowIn = CashFlowIn::create([
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'sub_sub_category_id' => $subSubCategoryId,
                'description' => 'Biaya Booking',
                'parent_id' => $parent->id,
                'total_amount' => $cashInData['reservation_fee'],
                'paid_amount' => 0,
                'notes' => $notes,
            ]);

            DB::commit();
            return [
                'error' => null,
                'status' => 200,
                'result' => null
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return [
                'error' => $e->getMessage(),
                'status' => 500,
                'result' => null
            ];
        }
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
            $cashFlowIn = CashFlowIn::where('id', $transaction->reference_id)->whereNotNull('parent_id')->first();
            if (!$cashFlowIn) {
                DB::rollBack();
                return [
                    'error' => 'Cash in not found',
                    'status' => 404,
                    'result' => null
                ];
            }
            $cashFlowIn->paid_amount -= $transaction->amount;
            $cashFlowIn->save();

            // parent paid amount
            if ($cashFlowIn->parent_id) {
                $parent = CashFlowIn::find($cashFlowIn->parent_id);
                if (!$parent) {
                    DB::rollBack();
                    return [
                        'error' => 'Cash in not found',
                        'status' => 404,
                        'result' => null
                    ];
                }
                $parent->paid_amount -= $transaction->amount;
                $parent->save();
            }

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

    public function check($cashInId, $bankAccountId)
    {
        $cashFlowIn = CashFlowIn::where('id', $cashInId)->first();
        if ($cashFlowIn->bank_account_id != $bankAccountId) {
            $transaction = Transaction::where('reference_id', $cashInId)->first();
            if ($transaction) {
                return [
                    'error' => 'You cannot change bank because this cash in has transaction',
                    'status' => 400,
                    'result' => null
                ];
            }
        }

        return [
            'error' => null,
            'status' => 200,
            'result' => null
        ];
    }

    public function createTransaction($data): array
    {
        try {
            DB::beginTransaction();

            // cek child
            $cashFlowIn = CashFlowIn::find($data['cash_in_id']);
            if (!$cashFlowIn) {
                DB::rollBack();
                return [
                    'error' => 'Cash in not found',
                    'status' => 404,
                    'result' => null
                ];
            }

            // tambahkan paid amount dan update total
            $selisih = $data['total_amount'] - $cashFlowIn->total_amount;
            $cashFlowIn->total_amount += $selisih;
            $cashFlowIn->paid_amount += $data['amount'];
            if (isset($data['bank_account_id'])) {
                $cashFlowIn->bank_account_id = $data['bank_account_id'];
            }

            // cek bank
            if (!$cashFlowIn->bank_account_id) {
                DB::rollBack();
                return [
                    'error' => 'Bank account not found',
                    'status' => 404,
                    'result' => null
                ];
            }

            // Validasi: paid tidak boleh lebih besar dari total
            if ($cashFlowIn->total_amount < $cashFlowIn->paid_amount) {
                DB::rollBack();
                return [
                    'error' => 'Paid Amount is greater than the total amount',
                    'status' => 400,
                    'result' => null
                ];
            }

            // Validasi: paid tidak boleh lebih besar dari total
            if ($cashFlowIn->paid_amount > $cashFlowIn->total_amount) {
                DB::rollBack();
                return [
                    'error' => 'Paid Amount is greater than the total amount',
                    'status' => 400,
                    'result' => null
                ];
            }

            $cashFlowIn->save();

            // Validasi parent
            if ($data['parent_id']) {
                $parent = CashFlowIn::where('id', $data['parent_id'])->whereNull('parent_id')->first();
                if (!$parent) {
                    DB::rollBack();
                    return [
                        'error' => 'Parent Cash in not found',
                        'status' => 404,
                        'result' => null
                    ];
                }

                // Jika ada perubahan total_amount anak, pastikan total semua anak tidak melebihi parent
                if ($selisih !== 0) {
                    $totalChildAmount = CashFlowIn::where('parent_id', $data['parent_id'])->sum('total_amount');
                    if ($totalChildAmount > $parent->total_amount) {
                        DB::rollBack();
                        return [
                            'error' => 'Total of child total_amount exceeds parent total_amount',
                            'status' => 400,
                            'result' => null
                        ];
                    }
                }

                // Tambah paid_amount ke parent
                $parent->paid_amount += $data['amount'];
                if ($parent->paid_amount > $parent->total_amount) {
                    DB::rollBack();
                    return [
                        'error' => 'Parent paid_amount exceeds parent total_amount',
                        'status' => 400,
                        'result' => null
                    ];
                }

                if(!$parent->bank_account_id){
                    $parent->bank_account_id = $cashFlowIn->bank_account_id;
                }

                $parent->save();
            } else {
                DB::rollBack();
                return [
                    'error' => 'Cash in parent not found',
                    'status' => 404,
                    'result' => null
                ];
            }

            // Buat transaksi
            if ($data['amount'] > 0) {
                $transaction = Transaction::create([
                    'property_id' => $cashFlowIn->property_id,
                    'reference_id' => $cashFlowIn->id,
                    'type' => 'in',
                    'category_id' => $cashFlowIn->category_id,
                    'sub_category_id' => $cashFlowIn->sub_category_id,
                    'sub_sub_category_id' => $cashFlowIn->sub_sub_category_id,
                    'amount' => $data['amount'],
                    'notes' => $data['notes'],
                    'bank_account_id' => $cashFlowIn->bank_account_id,
                    'created_at' => $data['date'] ?? now()
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
        $cashFlowIns = CashFlowIn::where('id', $id)->orWhere('parent_id', $id)->pluck('id');
        if (!$cashFlowIns) {
            return [
                'error' => 'Cash in not found',
                'status' => 404,
                'result' => null
            ];
        }

        $transactions = DB::table('transactions')
            ->join('cash_flow_ins', 'transactions.reference_id', '=', 'cash_flow_ins.id')
            ->join('cash_in_categories as c', 'cash_flow_ins.category_id', '=', 'c.id')
            ->join('cash_in_sub_categories as s', 'cash_flow_ins.sub_category_id', '=', 's.id')
            ->join('cash_in_sub_sub_categories as ssc', 'cash_flow_ins.sub_sub_category_id', '=', 'ssc.id')
            ->leftJoin('bank_accounts as ba', 'transactions.bank_account_id', '=', 'ba.id')
            ->whereIn('transactions.reference_id', $cashFlowIns)
            ->select(
                'transactions.id as transaction_id',
                'transactions.property_id',
                'c.name as category',
                's.name as sub_category',
                'ssc.name as description',
                'transactions.amount',
                'transactions.notes',
                'ba.id as bank_account_id',
                'ba.name as bank_account',
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
        $cashFlowIn = CashFlowIn::where('id', $id)->whereNull('parent_id')->first();
        if (!$cashFlowIn) {
            return [
                'error' => 'Cash in not found',
                'status' => 404,
                'result' => null
            ];
        }
        if ($cashFlowIn->paid_amount > $data['total_amount']) {
            return [
                'error' => 'total_amount cannot be less than paid_amount',
                'status' => 400,
                'result' => null
            ];
        }

        $cashFlowIn->total_amount = $data['total_amount'];
        $cashFlowIn->description = $data['description'] ?? null;
        $cashFlowIn->notes = $data['notes'] ?? null;
        $cashFlowIn->bank_account_id = $data['bank_account_id'] ?? null;
        $cashFlowIn->save();

        return [
            'error' => null,
            'status' => 200,
            'result' => $cashFlowIn
        ];
    }
}