<?php
        
namespace App\Repositories\Crm;

use App\Models\BankAccounts;
use App\Models\CashFlowIn;
use App\Models\FinalLegality;
use App\Models\Lead;
use App\Models\LeadPayment;
use App\Models\MarketingTask;
use App\Models\PaymentCheklist;
use App\Models\PaymentSelectedBank;
use App\Models\Reservation;
use App\Models\UnitProperty;
use Illuminate\Support\Facades\DB;

class LeadPaymentRepository
{
    public function getLeadCompletedDocument()
    {
        $leads = Lead::join('collection_documents', 'leads.id', '=', 'collection_documents.lead_id')
            ->join('contacts', 'leads.contact_id', '=', 'contacts.id')
            // ->where('collection_documents.status', 'completed')
            ->where('leads.status', 'document_and_legal_process')
            ->whereDoesntHave('payment')
            ->select(
                'leads.id',
                'collection_documents.id as collection_document_id',
                'contacts.name as name',
                'contacts.phone as phone',
                'contacts.email as email',
            )
            ->get();

        return [
            'error' => null,
            'code' => 200,
            'result' => $leads
        ];
    }
    public function getLeadPaymentGroup()
    {
        $data = LeadPayment::select(
                            'status', 'payment_type',
                            DB::raw('count(*) as total')
                        )->groupBy('status', 'payment_type')->get()->toArray();

        foreach ($data as $key => $value) {
            if ($value['payment_type'] == 'cash_keras' || $value['payment_type'] == 'cash_bertahap') {
                $data[$key] = [
                    'status' => $value['payment_type'],
                    'total' => $value['total']
                ];
            }
        };

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function getBankList()
    {
        return [
            'error' => null,
            'code' => 200,
            'result' => BankAccounts::select('id as code', 'name')->get()->toArray()
        ];
    }

    // checkValid Lead
    public function checkValid($lead_id)
    {
        $lead = Lead::with('collectionDocuments')
            ->whereHas('collectionDocuments')
            ->where('id', $lead_id)->first();
        if (!$lead) {
            return [
                'error' => 'Lead Not Found',
                'code' => 404,
                'result' => false
            ];
        }

        if ($lead->status != 'document_and_legal_process') {
            return [
                'error' => "Lead status must be document_and_legal_process",
                'code' => 400,
                'result' => false
            ];
        }

        if (!$lead->collectionDocuments->status == 'completed') {
            return [
                'error' => "Document status must be completed",
                'code' => 400,
                'result' => false
            ];
        }

        $hasPayment = LeadPayment::where('lead_id', $lead_id)->first();
        if ($hasPayment) {
            return [
                'error' => "Lead already has payment",
                'code' => 400,
                'result' => false
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => true
        ];
    }
    // createCash
    public function createCash($request)
    {
        try {
            DB::beginTransaction();
            LeadPayment::create([
                "lead_id" => $request['lead_id'],
                "status" => "cash",
                "payment_type" => $request['payment_type'],
                "notes" => $request['notes']
            ]);

            $dueDate = now()->addDays(config('setting.lead_status_durations')['payment']);
            $lead = Lead::where('id', $request['lead_id'])->first();
            $lead->due_date = $dueDate;
            $lead->save();
            
            $hasTask = MarketingTask::where('lead_id', $request['lead_id'])->where('task', 'lead_to_done_payment')->first();
            if (!$hasTask) {
                $oldLead = Lead::where('id', $request['lead_id'])->first();
                MarketingTask::create([
                    'user_id' => auth()->user()->id,
                    'lead_id' => $request['lead_id'],
                    'task' => 'lead_to_done_payment',
                    'description' => 'Lead to done payment',
                    'is_ontime' => $oldLead->due_date < now() ? 0 : 1,
                    'due_date' => $oldLead->due_date,
                    'completed_at' => now(),
                ]);
            }
            $reservation = Reservation::where('lead_id', $request['lead_id'])->orderBy('id', 'desc')
                ->select(
                    'id',
                    'hook_additional_fee',
                    'additional_land_area_fee',
                    'additional_building_specifications_fee',
                    'all_in_fee',
                    'property_unit_id',
                    )
                ->first();

            if (!$reservation) {
                DB::rollBack();
                throw new \Exception('Reservation not found');
            }

            $cashInData = [
                'lead_id' => $request['lead_id'],
                'payment_type' => $request['payment_type'],
                'property_unit_id' => $reservation->property_unit_id,
                'hook_additional_fee' => $reservation->hook_additional_fee,
                'additional_land_area_fee' => $reservation->additional_land_area_fee,
                'additional_building_specifications_fee' => $reservation->additional_building_specifications_fee,
                'all_in_fee' => $reservation->all_in_fee,
            ];

            $cashIn = $this->createCashInForCash($cashInData);

            if ($cashIn['error']) {
                return $cashIn;
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => null
        ];
    }

    public function createCashInForCash($cashInData)
    {
        try {
            DB::beginTransaction();
            
            $propertyUnit = UnitProperty::select('price','clusters.block_code', 'unit_number')
                ->join('clusters', 'unit_properties.cluster_id', '=', 'clusters.id')
                ->where('unit_properties.id', $cashInData['property_unit_id'])->first();

            if (!$propertyUnit) {
                throw new \Exception('Property unit not found');
            }

            $unitPrice = $propertyUnit->price;
            // create reservation
            $lead = Lead::join('contacts', 'leads.contact_id', '=', 'contacts.id')
                ->where('leads.id', $cashInData['lead_id'])
                ->select(
                    'leads.id as lead_id',
                    'contacts.name as name',
                )
                ->first();


            if (!$lead) {
                throw new \Exception('Lead not found');
            }

            $categoryId = DB::table('cash_in_categories')->where('code', 'penjualan-rumah')->first()->id;


            if ($cashInData['payment_type'] == 'cash_keras') {
                $codeSub = 'penjualan-rumah.cash-keras';
                $allInCode = 'penjualan-rumah.cash-keras.general.all-in';
                $hookAdditionalFeeCode = "penjualan-rumah.cash-keras.penambahan-spek.hook";
                $additionalLandAreaFeeCode = "penjualan-rumah.cash-keras.penambahan-spek.penambahan-tanah";
                $additionalBuildingSpecificationsFeeCode = "penjualan-rumah.cash-keras.penambahan-spek.penambahan-spek-bangunan";
                $dpCode = 'penjualan-rumah.cash-keras.pembayaran-bertahap.dp';
                $pelunasanCode = 'penjualan-rumah.cash-keras.pembayaran-bertahap.pelunasan';
            } else {
                $codeSub = 'penjualan-rumah.cash-bertahap';
                $allInCode = 'penjualan-rumah.cash-bertahap.general.all-in';
                $hookAdditionalFeeCode = "penjualan-rumah.cash-bertahap.penambahan-spek.hook";
                $additionalLandAreaFeeCode = "penjualan-rumah.cash-bertahap.penambahan-spek.penambahan-tanah";
                $additionalBuildingSpecificationsFeeCode = "penjualan-rumah.cash-bertahap.penambahan-spek.penambahan-spek-bangunan";
                $dpCode = 'penjualan-rumah.cash-bertahap.pembayaran-bertahap.dp';
                $pelunasanCode = 'penjualan-rumah.cash-bertahap.pembayaran-bertahap.cicilan-pelunasan';
            }

            $subCategoryId = DB::table('cash_in_sub_categories')->where('code', $codeSub)->first()->id;

            $allIn = DB::table('cash_in_sub_sub_categories')->where('code', $allInCode)->first();
            $hookAdditionalFee = DB::table('cash_in_sub_sub_categories')->where('code', $hookAdditionalFeeCode)->first();
            $additionalLandAreaFee = DB::table('cash_in_sub_sub_categories')->where('code', $additionalLandAreaFeeCode)->first();
            $additionalBuildingSpecificationsFee = DB::table('cash_in_sub_sub_categories')->where('code', $additionalBuildingSpecificationsFeeCode)->first();
            $dp = DB::table('cash_in_sub_sub_categories')->where('code', $dpCode)->first();
            $pelunasan = DB::table('cash_in_sub_sub_categories')->where('code', $pelunasanCode)->first();

            if (!$allIn || !$hookAdditionalFee || !$additionalLandAreaFee || !$additionalBuildingSpecificationsFee) {
                throw new \Exception('Sub sub category not found');
            }

            $parent = CashFlowIn::create([
                'property_id' => $cashInData['property_unit_id'],
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'description' => 'Penjualan Rumah',
                'total_amount' => $unitPrice + $cashInData['hook_additional_fee'] + $cashInData['additional_land_area_fee'] + $cashInData['additional_building_specifications_fee'] + $cashInData['all_in_fee'],
                'paid_amount' => 0,
                'notes' => 'Atas Nama : ' . $lead->name . ', unit ' . $propertyUnit->block_code . '-' . $propertyUnit->unit_number
            ]);

            // allIn
            CashFlowIn::create([
                'property_id' => $cashInData['property_unit_id'],
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'sub_sub_category_id' => $allIn->id,
                'description' => 'All In',
                'parent_id' => $parent->id,
                'total_amount' => $cashInData['all_in_fee'] ?? 0,
                'paid_amount' => 0,
                'notes' => 'All In Atas Nama: ' . $lead->name . ', unit ' . $propertyUnit->block_code . '-' . $propertyUnit->unit_number
            ]);

            // hookAdditionalFee
            CashFlowIn::create([
                'property_id' => $cashInData['property_unit_id'],
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'sub_sub_category_id' => $hookAdditionalFee->id,
                'description' => 'Penambahan Hook',
                'parent_id' => $parent->id,
                'total_amount' => $cashInData['hook_additional_fee'] ?? 0,
                'paid_amount' => 0,
                'notes' => 'Penambahan Hook Atas Nama: ' . $lead->name . ', unit ' . $propertyUnit->block_code . '-' . $propertyUnit->unit_number
            ]);

            // additionalLandAreaFee
            CashFlowIn::create([
                'property_id' => $cashInData['property_unit_id'],
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'sub_sub_category_id' => $additionalLandAreaFee->id,
                'description' => 'Penambahan Tanah',
                'parent_id' => $parent->id,
                'total_amount' => $cashInData['additional_land_area_fee'] ?? 0,
                'paid_amount' => 0,
                'notes' => 'Penambahan Tanah Atas Nama: ' . $lead->name . ', unit ' . $propertyUnit->block_code . '-' . $propertyUnit->unit_number
            ]);

            // additionalBuildingSpecificationsFee
            CashFlowIn::create([
                'property_id' => $cashInData['property_unit_id'],
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'sub_sub_category_id' => $additionalBuildingSpecificationsFee->id,
                'description' => 'Penambahan Spesifikasi Bangunan',
                'parent_id' => $parent->id,
                'total_amount' => $cashInData['additional_building_specifications_fee'] ?? 0,
                'paid_amount' => 0,
                'notes' => 'Penambahan Spesifikasi Bangunan Atas Nama: ' . $lead->name . ', unit ' . $propertyUnit->block_code . '-' . $propertyUnit->unit_number
            ]);

            // dp = 30% dari unit price
            CashFlowIn::create([
                'property_id' => $cashInData['property_unit_id'],
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'sub_sub_category_id' => $dp->id,
                'description' => 'DP',
                'parent_id' => $parent->id,
                'total_amount' => $unitPrice * 0.3,
                'paid_amount' => 0,
                'notes' => 'DP Atas Nama: ' . $lead->name . ', unit ' . $propertyUnit->block_code . '-' . $propertyUnit->unit_number
            ]);

            // pelunasan = 70% dari unit price
            CashFlowIn::create([
                'property_id' => $cashInData['property_unit_id'],
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'sub_sub_category_id' => $pelunasan->id,
                'description' => 'Pelunasan',
                'parent_id' => $parent->id,
                'total_amount' => $unitPrice * 0.7,
                'paid_amount' => 0,
                'notes' => 'Pelunasan Atas Nama: ' . $lead->name . ', unit ' . $propertyUnit->block_code . '-' . $propertyUnit->unit_number
            ]);

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

    // createKpr
    public function createKpr($request)
    {
        try {
            DB::beginTransaction();
            $leadPayment = LeadPayment::create([
                "status" => "proses_bank",
                "sp3k_status" => "pending",
                "akad_kredit_status" => "pending",
                "lead_id" => $request['lead_id'],
                "payment_type" => $request['payment_type'],
                "notes" => $request['notes']
            ]);

            foreach ($request['selected_banks'] as $bank) {
                PaymentSelectedBank::create([
                    "payment_id" => $leadPayment->id,
                    "bank_code" => $bank,
                ]);
            }

            $dueDate = now()->addDays(config('setting.lead_status_durations')['payment']);
            $lead = Lead::where('id', $request['lead_id'])->first();
            $lead->due_date = $dueDate;
            $lead->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => null
        ];
    }

    // getAll
    public function getAll($request)
    {
        $page = $request['page'] ?? 1;
        $perPage = $request['per_page'] ?? 10;
        $search = $request['search'] ?? null;
        $status = $request['status'] ?? null;
        $sortKey = $request['sortKey'] ?? 'duration';
        $sortDir = $request['sortDir'] ?? 'asc';

        $data = Lead::join('reservations', 'leads.id', '=', 'reservations.lead_id')
            ->join('lead_payments as payment', 'leads.id', '=', 'payment.lead_id')
            ->join('contacts', 'leads.contact_id', '=', 'contacts.id')
            ->leftJoin('final_legalities', 'leads.id', '=', 'final_legalities.lead_id')
            ->when($search, function ($query) use ($search) {
                $query->where('contacts.name', 'ilike', '%' . $search . '%')
                    ->orWhere('contacts.email', 'ilike', '%' . $search . '%')
                    ->orWhere('contacts.phone', 'ilike', '%' . $search . '%');
            })
            ->when($status, function ($query) use ($status) {
                $query->where('payment.status', $status);
            })
            ->when($sortKey == "name", function ($query) use ($sortKey, $sortDir) {
                $query->orderByRaw('LOWER(contacts.name) ' . $sortDir);
            })
            ->when($sortKey == "duration", function ($query) use ($sortKey, $sortDir) {
                $query->orderBy('payment.created_at', $sortDir == 'asc' ? 'desc' : 'asc');
            })
            ->select(
                'payment.id',
                'payment.lead_id',
                'payment.created_at',
                'payment.status',
                'payment.payment_type',
                'payment.notes',
                'contacts.name as name',
                'contacts.phone as phone',
                'reservations.property_unit_id',
                'final_legalities.created_at as stop_date'
                )
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    // getById
    public function getById($id)
    {
        $data = LeadPayment::join('leads', 'lead_payments.lead_id', '=', 'leads.id')
            ->join('contacts', 'leads.contact_id', '=', 'contacts.id')
            ->with('checklists')
            ->where('lead_payments.id', $id)
            ->select(
                'lead_payments.id',
                'lead_payments.lead_id',
                'contacts.name as name',
                'contacts.phone as phone',
                'contacts.email as email',
                'lead_payments.sp3k_status',
                'lead_payments.sp3k_document',
                'lead_payments.sp3k_bank',
                'lead_payments.sp3k_code',
                'lead_payments.sp3k_date',
                'lead_payments.sp3k_number',
                'lead_payments.akad_kredit_status',
                'lead_payments.akad_kredit_penandatanganan_document',
                'lead_payments.created_at',
                'lead_payments.updated_at',
                'lead_payments.status',
                'lead_payments.payment_type',
                'lead_payments.notes',
                'lead_payments.proposed_name_1',
                'lead_payments.proposed_name_2'
            )
            ->first();

        if (!$data) {
            return [
                'error' => "Data not found",
                'code' => 404,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    // delete payment
    public function delete($id)
    {
        $payment = LeadPayment::find($id);
        if ($payment) {
            $payment->delete();
            return [
                'error' => null,
                'code' => 200,
                'result' => null
            ];
        }

        // delete final legality by lead id
        $finalLegality = FinalLegality::where('lead_id', $payment->lead_id)->first();
        if ($finalLegality) {
            $finalLegality->delete();
        }

        return [
            "error" => "Data not found",
            "code" => 404,
            "result" => null
        ];
    }

    // update
    public function update($request, $checklists, $id)
    {
        $sp3kStatus = $request['sp3k_status'] ?? null;
        $sp3kDocument = $request['sp3k_document'] ?? null;
        $sp3kBank = $request['sp3k_bank'] ?? null;
        $sp3kCode = $request['sp3k_code'] ?? null;
        $sp3kDate = $request['sp3k_date'] ?? null;
        $sp3kNumber = $request['sp3k_number'] ?? null;
        $akadKreditStatus = $request['akad_kredit_status'] ?? null;
        $akadKreditPenandatangananDocument = $request['akad_kredit_penandatanganan_document'] ?? null;

        $oldData = LeadPayment::where('id', $id)->first();
        if (!$oldData) {
            return [
                'error' => "Data not found",
                'code' => 404,
                'result' => null
            ];
        }

        try {
            DB::beginTransaction();
            
            $sp3kDocumentFilename = $oldData->sp3k_document;
            if ($sp3kDocument) {
                $sp3kDocumentFilename = uploadFile('crm/lead_payments/sp3k', $sp3kDocument);
                if ($sp3kDocument == false) {
                    DB::rollBack();
                    return [
                        'error' => "Failed to upload file",
                        'code' => 500,
                        'result' => null
                    ];
                }
            }

            $akadKreditPenandatangananDocumentFilename = $oldData->akad_kredit_penandatanganan_document;
            if ($akadKreditPenandatangananDocument) {
                $akadKreditPenandatangananDocumentFilename = uploadFile('crm/lead_payments/tanda_tangan_akad_kredit', $akadKreditPenandatangananDocument);
                if ($akadKreditPenandatangananDocument == false) {
                    DB::rollBack();
                    return [
                        'error' => "Failed to upload file",
                        'code' => 500,
                        'result' => null
                    ];
                }
            }

            if ($sp3kStatus == "approved") {
                $status = "sp3k";
                if ($akadKreditStatus == "approved") {
                    $status = "akad_kredit";
                }
            } else {
                $status = "proses_bank";
            }

            LeadPayment::where('id', $id)->update([
                "sp3k_status" => $sp3kStatus,
                "sp3k_document" => $sp3kDocumentFilename,
                "sp3k_bank" => $sp3kBank ?? $oldData->sp3k_bank,
                "sp3k_code" => $sp3kCode ?? $oldData->sp3k_code,
                "sp3k_date" => $sp3kDate ?? $oldData->sp3k_date,
                "sp3k_number" => $sp3kNumber ?? $oldData->sp3k_number,
                "akad_kredit_status" => $akadKreditStatus ?? $oldData->akad_kredit_status,
                "akad_kredit_penandatanganan_document" => $akadKreditPenandatangananDocumentFilename,
                "notes" => $request['notes'],
                "status" => $status,
                "proposed_name_1" => $request['proposed_name_1'] ?? $oldData->proposed_name_1,
                "proposed_name_2" => $request['proposed_name_2'] ?? $oldData->proposed_name_2,
            ]);

            $existingCheckList = PaymentCheklist::where('payment_id', $id)->get();
            if ($existingCheckList->isEmpty()) {
                foreach ($checklists as $key => $value) {
                    $key = str_replace('checklist_', '', $key);
                    PaymentCheklist::create([
                        "payment_id" => $id,
                        "code" => $key,
                        "checked" => (int) $value ? 1 : 0
                    ]);
                }
            } else {
                foreach ($checklists as $key => $value) {
                    $key = str_replace('checklist_', '', $key);
                    PaymentCheklist::where('payment_id', $id)->where('code', $key)->update([
                        "checked" => (int) $value ? 1 : 0
                    ]);
                }
            }
            
            if ($sp3kDocument) {
                $path = explode('api/file/', $oldData->sp3k_document);
                $path = $path[1] ?? null;
                if ($path) {
                    deleteFile($path);
                }
            }

            if ($akadKreditPenandatangananDocument) {
                $path = explode('api/file/', $oldData->akad_kredit_penandatanganan_document);
                $path = $path[1] ?? null;
                if ($path) {
                    deleteFile($path);
                }
            }

            if ($oldData->status == "proses_bank" && $status == "sp3k") {
                $hasTask = MarketingTask::where('lead_id', $oldData->lead_id)->where('task', 'lead_to_sp3k')->first();
                if (!$hasTask) {
                    $oldLead = Lead::where('id', $oldData->lead_id)->first();
                    MarketingTask::create([
                        'user_id' => auth()->user()->id,
                        'lead_id' => $oldData->lead_id,
                        'task' => 'lead_to_sp3k',
                        'description' => 'Lead to SP3K',
                        'is_ontime' => $oldLead->due_date < now() ? 0 : 1,
                        'due_date' => $oldLead->due_date,
                        'completed_at' => now(),
                    ]);
                }
            } else if ($oldData->status !== "akad_kredit" && $status == "akad_kredit") {
                $hasTask = MarketingTask::where('lead_id', $oldData->lead_id)->where('task', 'lead_to_sp3k')->first();
                if (!$hasTask) {
                    $oldLead = Lead::where('id', $oldData->lead_id)->first();
                    MarketingTask::create([
                        'user_id' => auth()->user()->id,
                        'lead_id' => $oldData->lead_id,
                        'task' => 'lead_to_sp3k',
                        'description' => 'Lead to SP3K',
                        'is_ontime' => $oldLead->due_date < now() ? 0 : 1,
                        'due_date' => $oldLead->due_date,
                        'completed_at' => now(),
                    ]);
                }
                $hasTask = MarketingTask::where('lead_id', $oldData->lead_id)->where('task', 'lead_to_done_payment')->first();
                if (!$hasTask) {
                    $oldLead = Lead::where('id', $oldData->lead_id)->first();
                    MarketingTask::create([
                        'user_id' => auth()->user()->id,
                        'lead_id' => $oldData->lead_id,
                        'task' => 'lead_to_done_payment',
                        'description' => 'Lead to done payment',
                        'is_ontime' => $oldLead->due_date < now() ? 0 : 1,
                        'due_date' => $oldLead->due_date,
                        'completed_at' => now(),
                    ]);
                }

                $reservation = Reservation::where('lead_id', $oldData->lead_id)->first();

                $cashInData = [
                    'lead_id' => $oldData->lead_id,
                    'property_unit_id' => $reservation->property_unit_id,
                    'bank_id' => $oldData->sp3k_bank,
                    'hook_additional_fee' => $reservation->hook_additional_fee,
                    'additional_land_area_fee' => $reservation->additional_land_area_fee,
                    'additional_building_specifications_fee' => $reservation->additional_building_specifications_fee,
                    'all_in_fee' => $reservation->all_in_fee,
                ];

                $cashIn = $this->createCashInForKPR($cashInData);

                if ($cashIn['error']) {
                    return $cashIn;
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => null
        ];
    }

    private function createCashInForKPR($cashInData) {
        try {
            DB::beginTransaction();
            
            $propertyUnit = UnitProperty::select('price','clusters.block_code', 'unit_number')
                ->join('clusters', 'unit_properties.cluster_id', '=', 'clusters.id')
                ->where('unit_properties.id', $cashInData['property_unit_id'])->first();

            if (!$propertyUnit) {
                DB::rollBack();
                throw new \Exception('Property unit not found');
            }

            $unitPrice = $propertyUnit->price;
            // create reservation
            $lead = Lead::join('contacts', 'leads.contact_id', '=', 'contacts.id')
                ->where('leads.id', $cashInData['lead_id'])
                ->select(
                    'leads.id as lead_id',
                    'contacts.name as name',
                )
                ->first();


            if (!$lead) {
                throw new \Exception('Lead not found');
            }

            $categoryId = DB::table('cash_in_categories')->where('code', 'penjualan-rumah')->first()->id;
            $subCategoryId = DB::table('cash_in_sub_categories')->where('code', 'penjualan-rumah.kpr')->first()->id;

            $subSubCategories = DB::table('cash_in_sub_sub_categories')->where('code', 'ilike', "penjualan-rumah.kpr.%")->select('id', 'name', 'code')->get();

            $parent = CashFlowIn::create([
                'property_id' => $cashInData['property_unit_id'],
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'description' => 'Penjualan Rumah',
                'total_amount' => $unitPrice + $cashInData['hook_additional_fee'] + $cashInData['additional_land_area_fee'] + $cashInData['additional_building_specifications_fee'] + $cashInData['all_in_fee'],
                'paid_amount' => 0,
                'bank_account_id' => $cashInData['bank_id'],
                'notes' => 'Atas Nama : ' . $lead->name . ', unit ' . $propertyUnit->block_code . '-' . $propertyUnit->unit_number
            ]);

            foreach ($subSubCategories as $subSubCategory) {
                $nominal = 0;
                if ($subSubCategory->code == 'penjualan-rumah.kpr.general.all-in') {
                    $nominal = $cashInData['all_in_fee'];
                } else if ($subSubCategory->code == 'penjualan-rumah.kpr.penambahan-spek.hook') {
                    $nominal = $cashInData['hook_additional_fee'];
                } else if ($subSubCategory->code == 'penjualan-rumah.kpr.penambahan-spek.penambahan-tanah') {
                    $nominal = $cashInData['additional_land_area_fee'];
                } else if ($subSubCategory->code == 'penjualan-rumah.kpr.penambahan-spek.penambahan-spek-bangunan') {
                    $nominal = $cashInData['additional_building_specifications_fee'];
                }
                CashFlowIn::create([
                    'property_id' => $cashInData['property_unit_id'],
                    'category_id' => $categoryId,
                    'sub_category_id' => $subCategoryId,
                    'sub_sub_category_id' => $subSubCategory->id,
                    'description' => $subSubCategory->name,
                    'parent_id' => $parent->id,
                    'total_amount' => $nominal,
                    'paid_amount' => 0,
                    'bank_account_id' => $cashInData['bank_id'],
                    'notes' => $subSubCategory->name . '- Atas Nama : ' . $lead->name . ', unit ' . $propertyUnit->block_code . '-' . $propertyUnit->unit_number
                ]);
            }

            DB::commit();
            return [
                'error' => null,
                'code' => 200,
                'result' => null
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }
    }
}