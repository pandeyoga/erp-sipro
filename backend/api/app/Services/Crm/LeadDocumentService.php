<?php
        
namespace App\Services\Crm;

use App\Repositories\Crm\LeadDocumentRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadDocumentService
{
    public function __construct(
        protected LeadDocumentRepository $repository,
        protected LeadService $leadService
        ) {}

    public function getSummary()
    {
        $leadsGroup = $this->repository->getLeadsGroup();
        if ($leadsGroup['error']) {
            return $leadsGroup;
        }
        $leadsGroup = $leadsGroup['result'];
        
        // mapping agar statusnya jadi key
        $mapping = collect($leadsGroup)->mapWithKeys(function ($item) {
            return [$item['status'] => $item['total']];
        });

        // input, verification, completed
        $data =  [
            [
                'status' => 'input',
                'total' => isset($mapping['input']) ? $mapping['input'] : 0
            ],
            [
                'status' => 'verification',
                'total' => isset($mapping['verification']) ? $mapping['verification'] : 0
            ],
            [
                'status' => 'completed',
                'total' => isset($mapping['completed']) ? $mapping['completed'] : 0
            ]
        ];

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function getAll($request)
    {
        $search = $request['search'] ?? null;
        $status = $request['status'] ?? null;
        $page = $request['page'] ?? 1;
        $per_page = $request['per_page'] ?? 20;
        $orderBy = $request['sortKey'] ?? null;
        $order = $request['sortDir'] ?? null;

        $data = $this->repository->getAll($search, $status, $orderBy, $order, $page, $per_page);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];

        $items = collect($data->items())->map(function ($item) {
            if ($item->stop_date) {
                $startDate = Carbon::parse($item->created_at);
                $endDate = Carbon::parse($item->stop_date);
                $duration = (int) $startDate->diffInDays($endDate, true) . ' days';
            } else {
                $startDate = Carbon::parse($item->created_at);
                $duration = (int) Carbon::now()->startOfDay()->diffInDays($startDate, true) . ' days';
            }
            return [
                'id' => $item->doc_id,
                'lead_id' => $item->id,
                'name' => $item->contact_name,
                'phone' => $item->contact_phone,
                'notes' => $item->doc_notes,
                'status' => $item->doc_status,
                'property_unit_id' => $item->reservation_property_unit_id,
                'property_unit' => $item->project_name . ' ' . $item->cluster_name . ' ' . $item->block_code . '-' . $item->unit_number,
                'due_date' => $item->due_date,
                'duration' => $duration,
                'created_at' => date('Y-m-d', strtotime($item->created_at)),
            ];
        });
        
        $data->setCollection($items);

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function create($fill, $documents, $checklist)
    {
        // check if lead has document
        $hasDocument = $this->repository->hasDocument($fill['lead_id']);
        if ($hasDocument['error']) {
            return $hasDocument;
        }

        $hasDocument = $hasDocument['result'];
        if ($hasDocument) {
            return [
                'error' => 'Lead Document already exist',
                'code' => 400,
                'result' => null
            ];
        }

        // check if lead is reserved
        $isReserved = $this->leadService->isLeadReserved($fill['lead_id']);
        if ($isReserved['error']) {
            return $isReserved;
        }

        $isReserved = $isReserved['result'];

        if (!$isReserved) {
            return [
                'error' => 'Lead is not reserved',
                'code' => 400,
                'result' => null
            ];
        }
        
        try {
            DB::beginTransaction();
            $leadDocument = [
                "lead_id" => $fill['lead_id'],
                "status" => 'input',
                "notes" => $fill['notes'] ?? "",
            ];

            $collectionDocumentsId = $this->repository->create($leadDocument);
            if ($collectionDocumentsId['error']) {
                DB::rollBack();
                return $collectionDocumentsId;
            }
            $collectionDocumentsId = $collectionDocumentsId['result'];
            
            if (!$collectionDocumentsId) {
                DB::rollBack();
                return [
                    'error' => 'Error when create lead document',
                    'code' => 400,
                    'result' => null
                ];
            }

            $create = $this->repository->createDocuments($collectionDocumentsId, $fill['lead_id'], $documents);
            if ($create['error']) {
                DB::rollBack();
                return $create;
            }

            $createChecklist = $this->repository->createChecklist($collectionDocumentsId, $checklist);
            if ($createChecklist['error'] != null) {
                DB::rollBack();
                return $createChecklist;
            }

            $updateLeadStatus = $this->leadService->updateStatus($fill['lead_id'], 'document_and_legal_process');
            if ($updateLeadStatus['error']) {
                DB::rollBack();
                return $updateLeadStatus;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
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

    public function show($id)
    {
        return $this->repository->getLeadById($id);
    }

    public function update($id, $fill, $documents, $checklist)
    {
        try {
            DB::beginTransaction();

            $leadDocument = [
                "notes" => $fill['notes'] ?? "",
            ];

            $collectionDocumentsId = $this->repository->update($id, $leadDocument);
            if ($collectionDocumentsId['error']) {
                DB::rollBack();
                return $collectionDocumentsId;
            }
            $collectionDocumentsId = $collectionDocumentsId['result'];

            $updateDocuments = $this->repository->updateDocuments($collectionDocumentsId, $documents);
            if ($updateDocuments['error']) {
                DB::rollBack();
                return $updateDocuments;
            }

            $updateChecklist = $this->repository->updateChecklist($collectionDocumentsId, $checklist);
            if ($updateChecklist['error']) {
                DB::rollBack();
                return $updateChecklist;
            }

            // check jika dokumen dan checklist sudah lengkap
            $isComplete = $this->isComplete($collectionDocumentsId);
            if ($isComplete['error']) {
                DB::rollBack();
                return $isComplete;
            }
            $isComplete = $isComplete['result'];
            
            if ($isComplete) {
                $updateLeadStatus = $this->repository->updateStatus($collectionDocumentsId, 'completed');
                if ($updateLeadStatus['error']) {
                    DB::rollBack();
                    return $updateLeadStatus;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
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

    // getById
    public function getById($id)
    {
        $result = $this->repository->getById($id);
        if ($result['error']) {
            return $result;
        }
        $result = $result['result'];
        
        $documentGroup = collect($result->documents)->groupBy('type');
        $documents = collect(config('setting.buyer_document_types'))->map(function ($item) use ($documentGroup) {
            if (isset($documentGroup[$item['code']])) {
                $document = $documentGroup[$item['code']]->first();
                return [
                    'type' => $item['code'],
                    'is_uploaded' => true,
                    'is_validated' => $document->status == 'validated',
                    'file_url' => url($document->file_url),
                ];
            } else {
                return [
                    'type' => $item['code'],
                    'is_uploaded' => false,
                    'is_validated' => false,
                    'file_url' => null,
                ];
            }
        });

        $checklist = collect($result->checkList)->map(function ($item) {
            return [
                'key' => $item->name,
                'name' => ucfirst(str_replace('_', ' ', $item->name)),
                'checked' => $item->checked,
            ];
        });


        $data = [
            'id' => $result->id,
            'lead_id' => $result->lead_id,
            'name' => $result->lead->contact->name,
            'phone' => $result->lead->contact->phone,
            'email' => $result->lead->contact->email,
            'status' => $result->status,
            'notes' => $result->notes,
            'documents' => $documents,
            'checklist' => $checklist
        ];

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function reservedLeads($search)
    {
        $leads = $this->leadService->reservedLeads($search);
        if ($leads['error']) {
            return $leads;
        }
        $leads = $leads['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $leads
        ];
    }

    public function updateStatusDocument($collectionDocumentsId, $type, $status) : array 
    {
        try {
            DB::beginTransaction();
            
            $updateDocuments = $this->repository->updateStatusDocument($collectionDocumentsId, $type, $status);
            if ($updateDocuments['error']) {
                DB::rollBack();
                return $updateDocuments;
            }

            $isComplete = $this->isComplete($collectionDocumentsId);
            if ($isComplete['error']) {
                DB::rollBack();
                return $isComplete;
            }

            $isComplete = $isComplete['result'];
            if ($isComplete) {
                $updateLeadStatus = $this->repository->updateStatus($collectionDocumentsId, 'completed');
                if ($updateLeadStatus['error']) {
                    DB::rollBack();
                    return $updateLeadStatus;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }
        return [
            'error' => null,
            'code' => 200,
            'result' => $updateDocuments['result']
        ];
    }

    public function isComplete($collectionDocumentId)
    {
        $getCheklistAndDocuments = $this->repository->getCheklistAndDocuments($collectionDocumentId);
        if ($getCheklistAndDocuments['error']) {
            return $getCheklistAndDocuments;
        }
        $cheklistAndDocuments = $getCheklistAndDocuments['result'];

        $documents = $cheklistAndDocuments['documents'];
        $checklist = $cheklistAndDocuments['checklist'];

        $groupDocuments = $documents->groupBy('type');

        $optional = ['ktp_partner', 'spr_bank'];

        $documents = collect(config('setting.buyer_document_types'))->map(function ($item) use ($groupDocuments, $optional) {
            if (isset($groupDocuments[$item['code']])) {
                $document = $groupDocuments[$item['code']]->first();
                return [
                    'type' => $item['code'],
                    'is_optional' => in_array($item['code'], $optional),
                    'is_uploaded' => true,
                    'is_validated' => $document->status == 'validated',
                    'file_url' => url($document->file_url),
                ];
            } else {
                return [
                    'type' => $item['code'],
                    'is_optional' => in_array($item['code'], $optional),
                    'is_uploaded' => false,
                    'is_validated' => false,
                    'file_url' => null,
                ];
            }
        });

        $groupedChecklis['pekerja'] = [];
        $groupedChecklis['wirausaha'] = [];
        $bypass = false;
        foreach ($checklist as $item) {
            if (Str::startsWith($item->name, 'wirausaha_')) {
                $groupedChecklis['wirausaha'][] = $item->toArray();
            } else if (Str::startsWith($item->name, 'pekerja_')) {
                $groupedChecklis['pekerja'][] = $item->toArray();
            } else if ($item->name == 'check_cash') {
                $bypass = true;
            }
        }

        $checklist = $groupedChecklis;

        // loop documents
        $isCompleteDoc = true;
        foreach ($documents as $item) {
            if (!$item['is_validated'] && !$item['is_optional']) {
                $isCompleteDoc = false;
                break;
            }
        }

        // loop checklist pekerja
        $isCompleteChecklistPekerja = true;
        foreach ($checklist['pekerja'] as $item) {
            if (!$item['checked'] && ($item['name'] != 'pekerja_materai_60_lembar')) {
                $isCompleteChecklistPekerja = false;
                break;
            }
        }

        // loop checklist wirausaha
        $isCompleteChecklistWirausaha = true;
        foreach ($checklist['wirausaha'] as $item) {
            if (!$item['checked'] && ($item['name'] != 'wirausaha_materai_60_lembar')) {
                $isCompleteChecklistWirausaha = false;
                break;
            }
        }

        $isComplete = $isCompleteDoc && ($isCompleteChecklistPekerja || $isCompleteChecklistWirausaha || $bypass);

        return [
            'error' => null,
            'code' => 200,
            'result' => $isComplete,
        ];
    }

    public function delete($id)
    {
        $document = $this->repository->getById($id);
        if ($document['error']) {
            return $document;
        }
        $document = $document['result'];

        $delete = $this->leadService->delete($document->lead_id);
        if ($delete['error']) {
            return $delete;
        }
        $delete = $delete['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $delete,
        ];
    }
}