<?php
        
namespace App\Services\Crm;

use App\Repositories\Crm\LeadRepository;
use App\Repositories\Property\UnitRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LeadService
{
    public function __construct(
        protected LeadRepository $repository,
        protected UnitRepository $unitRepository
    ) {}

    public function getSummary() : array
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

        return  [
            'error' => null,
            'code' => 200,
            'result' => [
                [
                    'status' => 'new',
                    'total' => isset($mapping['new']) ? $mapping['new'] : 0
                ],
                [
                    'status' => 'prospect',
                    'total' => isset($mapping['prospect']) ? $mapping['prospect'] : 0
                ],
                [
                    'status' => 'reserve',
                    'total' => isset($mapping['reserve']) ? $mapping['reserve'] : 0
                ],
                [
                    'status' => 'document_and_legal_process',
                    'total' => isset($mapping['document_and_legal_process']) ? $mapping['document_and_legal_process'] : 0
                ],
                [
                    'status' => 'complete',
                    'total' => isset($mapping['complete']) ? $mapping['complete'] : 0
                ],
                [
                    'status' => 'cancel',
                    'total' => isset($mapping['cancel']) ? $mapping['cancel'] : 0
                ],
            ]
        ];
    }

    public function getMarketingAgents() : array
    {
        $marketingAgents = $this->repository->getMarketingAgents();
        if ($marketingAgents['error']) {
            return $marketingAgents;
        }
        $marketingAgents = $marketingAgents['result'];
        
        return  [
            'error' => null,
            'code' => 200,
            'result' => $marketingAgents
        ];
    }

    // getSurveyLocation
    public function getSurveyLocation() : array
    {
        $surveyLocation = $this->repository->getSurveyLocation();
        if ($surveyLocation['error']) {
            return $surveyLocation;
        }
        $surveyLocation = $surveyLocation['result'];
        
        return  [
            'error' => null,
            'code' => 200,
            'result' => $surveyLocation
        ];
    }

    public function getUnitList() : array
    {
        $unitList = $this->unitRepository->getAll();
        if ($unitList['error']) {
            return $unitList;
        }
        $unitList = $unitList['result'];
        
        return  [
            'error' => null,
            'code' => 200,
            'result' => $unitList
        ];
    }

    public function getAll($request) : array
    {
        try {
            $search = $request['search'] ?? null;
            $marketing_id = $request['marketing_id'] ?? null;
            $status = $request['status'] ?? null;
            $source = $request['source'] ?? null;
            $page = $request['page'] ?? 1;
            $per_page = $request['per_page'] ?? 20;

            $sort = [
                'by' => $request['sortKey'] ?? 'order_number',
                'sortDir' => $request['sortDir'] ?? 'desc',
            ];
    
            $data = $this->repository->getAll($search, $marketing_id, $status, $page, $per_page, $source,$sort);
            if ($data['error']) {
                return $data;
            }
            $data = $data['result'];
    
            $items = collect($data->items())->map(function ($item) {
                // formatted status ubah _ menjadi spasi dan uppercase awal
                $status = ucfirst(str_replace('_', ' ', $item->status));
                if ($item->stop_date) {
                    $startDate = Carbon::parse($item->created_at);
                    $endDate = Carbon::parse($item->stop_date);
                    $duration = (int) $startDate->diffInDays($endDate, true) . ' days';
                } else {
                    $startDate = Carbon::parse($item->created_at);
                    $duration = (int) Carbon::now()->startOfDay()->diffInDays($startDate, true) . ' days';
                }
                return [
                    'id' => $item->id,
                    'status' => $status,
                    'order_number' => $item->order_number,
                    'source' => $item->contact_source,
                    'name' => $item->contact_name,
                    'phone' => $item->contact_phone,
                    'notes' => $item->note,
                    'due_date' => $item->due_date,
                    'marketing_agent' => $item->marketing_agent ?? '-',
                    'duration' => $duration,
                    'created_at' => date('Y-m-d', strtotime($item->created_at)),
                ];
            });
            
            $data->setCollection($items);
        } catch (\Exception $e) {
            $data = [
                'error' => $e->getMessage(),
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

    public function reservedLeads($search = null) : array
    {
        $lead = $this->repository->getLeadReserve($search);
        if ($lead['error']) {
            return $lead;
        }
        $lead = $lead['result'];

        $reservedLeads = $lead->map(function ($item, $key) {
            return [
                'id' => $item->id,
                'reservation_id' => $item->reservation_id,
                'name' => $item->contact->name,
                'phone' => $item->contact->phone,
                'email' => $item->contact->email,
                'survey_location_id' => $item->survey_location_id,
                'survey_date' => date('Y-m-d', strtotime($item->survey_date)),
                'marketing_agent_id' => $item->assign_to,
                'marketing_agent' => $item->assignTo?->name,
            ];
        });

        return [
            'error' => null,
            'code' => 200,
            'result' => $reservedLeads
        ];
    }

    public function hasLead($contactId) : array
    {
        $isHaslead = $this->repository->hasLead($contactId);
        if ($isHaslead['error']) {
            return $isHaslead;
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $isHaslead['result']
        ];
    }

    public function create($request) : array
    {
        $now = Carbon::now();
        $dueDate = $now->addDays(config('setting.lead_status_durations.new'));
        $data = [
            "id" => Str::uuid()->toString(),
            "contact_id" => $request['contact_id'],
            "assign_to" => $request['marketing_id'],
            "status" => 'new',
            "survey_location_id" => null,
            "survey_date" => null,
            "due_date" => $dueDate,
            "note" => $request['notes'] ?? null,
            "pic" => null,
            "created_at" => now(),
        ];

        $source = $request['source'];

        $create = $this->repository->create($data, $source);
        if ($create['error']) {
            return $create;
        }
        $create = $create['result'];

        $create['source'] = $source;

        return [
            'error' => null,
            'code' => 200,
            'result' => $create
        ];
    }

    public function update($id, $data) : array
    {
        $sourceUpdate = $data['source'];
        $data = [
            'note' => $data['notes'] ?? null,
        ];

        if (isset($data['marketing_id'])) {
            $data['assign_to'] = $data['marketing_id'];
        }

        $update = $this->repository->update($id, $data, $sourceUpdate);
        if ($update['error']) {
            return $update;
        }

        $data =  $this->repository->getLeadById($id);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => [
                'id' => $data->id,
                'contact_id' => $data->contact_id,
                'assign_to' => $data->assign_to,
                'status' => $data->status,
                'survey_location_id' => $data->survey_location_id,
                'survey_date' => $data->survey_date,
                'due_date' => $data->due_date,
                'source' => $data->contact->source,
                'actual_survey_date' => $data->actual_survey_date,
                'survey_documentation' => $data->survey_documentation ? url($data->survey_documentation) : null,
                'unit_preference_id' => $data->unit_preference_id,
                'updated_at' => $data->updated_at,
                'created_at' => $data->created_at
            ]
        ];
    }

    public function getNonLeadContacts($search = null) : array
    {
        $data = $this->repository->getNonLeadContacts($search);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];
        
        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function getStatus() : array
    {
        return [
            'error' => null,
            'code' => 200,
            'result' => config('setting.lead_statuses')
        ];
    }

    public function getById($id) : array
    {
        $data = $this->repository->getLeadById($id);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];

        $history = $this->repository->getLeadHistory($id);
        if ($history['error']) {
            return $history;
        }
        $history = $history['result'];
        
        $history = collect($history->history)->map(function ($item) {
            return [
                'action_by' => $item->action_by,
                'action_by_name' => $item->actionBy->name,
                'old_status' => $item->old_status,
                'new_status' => $item->new_status,
                'changed_at' => $item->changed_at,
            ];
        });

        return [
            'error' => null,
            'code' => 200,
            'result' => [
                'id' => $data->id,
                'name' => $data->contact->name,
                'phone' => $data->contact->phone,
                'email' => $data->contact->email,
                'marketing_agent_id' => $data->assign_to,
                'marketing_agent_name' => $data->assignTo?->name,
                'status' => $data->status,
                'survey_location_id' => $data->survey_location_id,
                'survey_location' => $data->survey_location,
                'survey_date' => $data->survey_date,
                'due_date' => $data->due_date,
                'source' => $data->contact->source,
                'pic' => $data->pic,
                'actual_survey_date' => $data->actual_survey_date,
                'survey_documentation' => $data->survey_documentation ? url($data->survey_documentation) : null,
                'unit_preference_id' => $data->unit_preference_id,
                'unit_preference_type' => $data->unit_preferences,
                'history' => $history,
                'notes' => $data->note,
                'created_at' => $data->created_at,
            ]
        ];
    }

    public function isLeadReserved($id) : array
    {
        $isReserved = $this->repository->isLeadReserved($id);
        if ($isReserved['error']) {
            return $isReserved;
        }
        $isReserved = $isReserved['result'];
        return [
            'error' => null,
            'code' => 200,
            'result' => $isReserved
        ];
    }

    public function updateStatus($id, $status) : array
    {
        return $this->repository->updateStatus($id, $status);
    }

    public function delete($id) : array
    {
        $data = $this->repository->delete($id);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => null
        ];
    }
}