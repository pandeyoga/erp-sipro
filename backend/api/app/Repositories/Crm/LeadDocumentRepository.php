<?php
        
namespace App\Repositories\Crm;

use App\Models\CheckListDocument;
use App\Models\CollectionDocument;
use App\Models\Document;
use App\Models\Lead;
use App\Models\MarketingTask;
use App\Models\User;
use Exception;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadDocumentRepository
{
    public function getLeadsGroup()
    {
        return [
            'error' => null,
            'code' => 200,
            'result' => CollectionDocument::select(
                            'status',
                            DB::raw('count(*) as total')
                        )->groupBy('status')->get()->toArray()
            ];
    }

    public function hasLead($contactId)
    {
        return Lead::where('contact_id', $contactId)->exists();
    }

    public function hasDocument($leadId)
    {
        return [
            'error' => null,
            'code' => 200,
            'result' => CollectionDocument::where('lead_id', $leadId)->exists()
        ];
    }

    public function getMarketingAgents()
    {
        return User::whereHas('role', function ($query) {
            $query->where('group', 'marketing_agent');
        })
        ->select('id', 'name')
        ->get();
    }

    public function create($data)
    {
        try {
            DB::beginTransaction();
            $data['id'] = (string) Str::uuid();
            CollectionDocument::create($data);

            $hasTask = MarketingTask::where('lead_id', $data['lead_id'])->where('task', 'lead_to_document')->first();
            if (!$hasTask) {
                $oldLead = Lead::where('id', $data['lead_id'])->first();
                MarketingTask::create([
                    'user_id' => auth()->user()->id,
                    'lead_id' => $data['lead_id'],
                    'task' => 'lead_to_document',
                    'description' => 'Lead to document and legal',
                    'is_ontime' => $oldLead->due_date < now() ? 0 : 1,
                    'due_date' => $oldLead->due_date,
                    'completed_at' => now(),
                ]);
            }

            $dueDate = now()->addDays(config('setting.lead_status_durations')['document_and_legal_process']);

            $lead = Lead::where('id', $data['lead_id'])->first();
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
            'result' => $data['id']
        ];
    }

    public function createDocuments($collectionDocumentId, $leadId, $documents)
    {
        // loop lalu upload file dan simpen namanya dalam array
        try {
            DB::beginTransaction();

            foreach ($documents as $key => $document) {
                if ($document !== null) {
                    $fileName = uploadFile('crm/lead_documents/'.$key, $document);
                    if ($fileName == false) {
                        DB::rollBack();
                        return [
                            'error' => "Failed to upload file",
                            'code' => 500,
                            'result' => null
                        ];
                    }
        
                    Document::create([
                        'collection_document_id' => $collectionDocumentId,
                        'lead_id' => $leadId,
                        'type' => $key,
                        'status' => 'uploaded',
                        'file_url' => $fileName,
                        'uploaded_at' => now(),
                    ]);
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

    // createChecklist
    public function createChecklist($collectionDocumentId, $checklist)
    {
        $checklist = collect($checklist)->map(function ($value,$key) {
            return [
                'name' => $key,
                'checked' => (int) $value
            ];
        });
        
        try {
            DB::beginTransaction();
            foreach ($checklist as $item) {
                CheckListDocument::create([
                    'lead_document_id' => $collectionDocumentId,
                    'name' => $item['name'],
                    'checked' => $item['checked']
                ]);
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

    public function getAll($search, $status, $orderBy, $order, $page, $per_page)
    {
        try {
            $order = $order ?? 'asc';

            $data = Lead::query()
                ->join('contacts', 'leads.contact_id', '=', 'contacts.id')
                ->join('collection_documents', 'leads.id', '=', 'collection_documents.lead_id')
                ->leftJoin('reservations', 'leads.id', '=', 'reservations.lead_id')
                ->leftJoin('unit_properties', 'reservations.property_unit_id', '=', 'unit_properties.id')
                ->leftJoin('projects', 'unit_properties.project_id', '=', 'projects.id')
                ->leftJoin('clusters', 'unit_properties.cluster_id', '=', 'clusters.id')
                ->leftJoin('lead_payments', 'leads.id', '=', 'lead_payments.lead_id')
                ->whereNotNull('collection_documents.id')
                ->when($status, function ($query) use ($status) {
                    $query->where('collection_documents.status', $status);
                })
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('contacts.name', 'ilike', '%' . $search . '%')
                        ->orWhere('contacts.phone', 'ilike', '%' . $search . '%')
                        ->orWhere('contacts.email', 'ilike', '%' . $search . '%');
                    });
                })
                ->when($orderBy == 'name', function ($query) use ($order) {
                    $query->orderByRaw('LOWER(contacts.name) ' . $order);
                })
                ->when($orderBy == 'duration', function ($query) use ($order) {
                    // kalau duration = created_at collection_documents
                    $query->orderBy('collection_documents.created_at', $order == 'asc' ? 'desc' : 'asc');
                })
                ->when(!$orderBy, function ($query) {
                    $query->orderBy('leads.due_date', 'asc');
                })
                ->select(
                    'leads.id',
                    'leads.order_number',
                    'leads.contact_id',
                    'leads.status',
                    'leads.due_date',
                    'leads.created_at',
                    'contacts.name as contact_name',
                    'contacts.phone as contact_phone',
                    'contacts.email as contact_email',
                    'collection_documents.id as doc_id',
                    'collection_documents.status as doc_status',
                    'collection_documents.notes as doc_notes',
                    'reservations.property_unit_id as reservation_property_unit_id',
                    'projects.name as project_name',
                    'clusters.name as cluster_name',
                    'clusters.block_code as block_code',
                    'unit_properties.unit_number as unit_number',
                    'lead_payments.created_at as stop_date'

                )
                ->paginate($per_page, ['*'], 'page', $page);
        } catch (\Throwable $th) {
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function getLeadById($id)
    {
        return Lead::with('contact:id,name,phone,email', 'assignTo:id,name')->where('id', $id)->first();
    }

    public function update($id,$data)
    {
        $currentCollectionDocument = CollectionDocument::where('id', $id)->first();
        if (!$currentCollectionDocument) {
            return [
                'error' => "Document not found",
                'code' => 404,
                'result' => null
            ];
        }
        
        try {
            DB::beginTransaction();
            $data['id'] = $id;
            $currentCollectionDocument->update($data);
            DB::commit();
        } catch (\Throwable $th) {
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $data['id']
        ];
    }
    
    // updateDocuments
    public function updateDocuments($collectionDocumentId, $documents)
    {
        try {
            DB::beginTransaction();
            $currentDocuments = Document::where('collection_document_id', $collectionDocumentId)->get();
            $collectionDocument = CollectionDocument::where('id', $collectionDocumentId)->first();
            $leadId = $collectionDocument->lead_id;
            foreach ($documents as $key => $document) {
                if ($document !== null) {
                    $currentDocument = $currentDocuments->where('type', $key)->first();
                    if ($currentDocument) {
                        $path = explode('api/file', $currentDocument->file_url)[1];
                        try {
                            deleteFile($path);
                        } catch (\Exception $e) {
                            DB::rollBack();
                            return [
                                'error' => $e->getMessage(),
                                'code' => 500,
                                'result' => null
                            ];
                        }
                        $currentDocument->delete();
                    }
                    $fileName = uploadFile('crm/lead_documents/'.$key, $document);
    
                    if ($fileName == false) {
                        DB::rollBack();
                        return [
                            'error' => "Failed to upload file",
                            'code' => 500,
                            'result' => null
                        ];
                    }
                    Document::create([
                        'collection_document_id' => $collectionDocumentId,
                        'lead_id' => $leadId,
                        'type' => $key,
                        'status' => $currentDocument ? $currentDocument->status : 'uploaded',
                        'file_url' => $fileName,
                        'uploaded_at' => now(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
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

    // updateChecklist
    public function updateChecklist($collectionDocumentId, $checklist)
    {
        $collectionDocument = CollectionDocument::where('id', $collectionDocumentId)->first();
        if (!$collectionDocument) {
            return [
                'error' => null,
                'code' => 404,
                'result' => null
            ];
        }
        $checklist = collect($checklist)->map(function ($value,$key) {
            return [
                'name' => $key,
                'checked' => (int) $value
            ];
        });
        try {
            DB::beginTransaction();

            $updateStatusCollectionDocument = false;
            foreach ($checklist as $item) {
                $checklist = CheckListDocument::where('lead_document_id', $collectionDocumentId)
                    ->where('name', $item['name'])
                    ->first();
                if ($checklist) {
                    $checklist->checked = $item['checked'];
                    $checklist->save();
                }

                if ($item['checked'] && $collectionDocument->status == "input") {
                    $updateStatusCollectionDocument = true;
                    $collectionDocument->status = 'verification';
                }
            }

            if ($updateStatusCollectionDocument) {
                $collectionDocument->save();
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

    public function updateStatus($collectionDocumentId, $status) {
        try {
            DB::beginTransaction();
            $collectionDocument = CollectionDocument::where('id', $collectionDocumentId)->first();
            if (!$collectionDocument) {
                DB::rollBack();
                return [
                    'error' => "Document not found",
                    'code' => 404,
                    'result' => null
                ];
            }
            $collectionDocument->status = $status;
            $collectionDocument->save();

            if ($status == 'completed') {
                $leadId = $collectionDocument->lead_id;
                $hasTask = MarketingTask::where('lead_id', $leadId)->where('task', 'lead_completed_document')->first();
                if (!$hasTask) {
                    $oldLead = Lead::where('id', $leadId)->first();
                    MarketingTask::create([
                        'user_id' => auth()->user()->id,
                        'lead_id' => $leadId,
                        'task' => 'lead_to_completed_document',
                        'description' => 'Lead to completed document',
                        'is_ontime' => $oldLead->due_date < now() ? 0 : 1,
                        'due_date' => $oldLead->due_date,
                        'completed_at' => now(),
                    ]);
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

    // getById
    public function getById($id)
    {
        return [
            'error' => null,
            'code' => 200,
            'result' => CollectionDocument::where('id', $id)
                ->with(
                    'lead:id,contact_id',
                    'lead.contact:id,name,phone,email',
                    'documents:id,collection_document_id,type,status,file_url',
                    'checkList:id,lead_document_id,name,checked'
                )
                ->first()
        ];
    }

    public function updateStatusDocument($id, $type, $status) : array {
        try {
            DB::beginTransaction();
            $status = $status == 'verified' ? 'validated' : 'uploaded';
            
            $collectionDocument = CollectionDocument::where('id', $id)->first();
            if (!$collectionDocument) {
                DB::rollBack();
                return [
                    'error' => "Document not found",
                    'code' => 404,
                    'result' => null
                ];
            }

            $document = Document::where('collection_document_id', $id)->where('type', $type)->first();
            if (!$document) {
                DB::rollBack();
                return [
                    'error' => "Document not found",
                    'code' => 404,
                    'result' => null
                ];
            }
            $document->status = $status;
            $document->save();
            
            if ($status == 'validated' && $collectionDocument->status == 'input') {
                $collectionDocument->status = 'verification';
                $collectionDocument->save();
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
            'result' => $document
        ];
    }

    public function getCheklistAndDocuments($collectionDocumentId)
    {
        $collectionDocument = CollectionDocument::where('id', $collectionDocumentId)->first();
        if (!$collectionDocument) {
            return [
                'error' => null,
                'code' => 404,
                'result' => null
            ];
        }

        $documents = Document::where('collection_document_id', $collectionDocumentId)
            ->select(
                "id","collection_document_id","type","status","file_url"
            )
            ->get();
        
        $checklist = CheckListDocument::where('lead_document_id', $collectionDocumentId)
            ->select(
                "id","lead_document_id","name","checked"
            )
            ->get();

        return [
            'error' => null,
            'code' => 200,
            'result' => [
                'documents' => $documents,
                'checklist' => $checklist
            ]
        ];
    }
}