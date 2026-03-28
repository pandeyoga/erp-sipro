<?php
        
namespace App\Services\Crm;

use App\Repositories\Crm\SurveyRepository;
use App\Repositories\Property\UnitRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SurveyService
{
    public function __construct(
        protected SurveyRepository $repository,
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
                    'status' => 'not_scheduled',
                    'total' => isset($mapping['not_scheduled']) ? $mapping['not_scheduled'] : 0
                ],
                [
                    'status' => 'scheduled',
                    'total' => isset($mapping['scheduled']) ? $mapping['scheduled'] : 0
                ],
                [
                    'status' => 'done',
                    'total' => isset($mapping['done']) ? $mapping['done'] : 0
                ]
            ]
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
    
            $data = $this->repository->getAll($search, $marketing_id, $status, $page, $per_page, $source, $sort);
            if ($data['error']) {
                return $data;
            }
            $data = $data['result'];
    
            $items = collect($data->items())->map(function ($item) {
                $status = ucfirst(str_replace('_', ' ', $item->status));
                if ($item->stop_date) {
                    $startDate = Carbon::parse($item->created_at);
                    $stopDate = Carbon::parse($item->stop_date);
                    $duration = (int) $startDate->diffInDays($stopDate, true);
                } else {
                    $startDate = Carbon::parse($item->created_at);
                    $duration = (int) Carbon::now()->startOfDay()->diffInDays($startDate, true) . ' days';
                }
                return [
                    "id" => $item->id,
                    "status" => $status,
                    "name" => $item->contact_name,
                    'phone' => $item->contact_phone,
                    "scheduled_date" => $item->scheduled_date ? date('Y-m-d', strtotime($item->scheduled_date)) : null,
                    "actual_survey_date" => $item->actual_survey_date ? date('Y-m-d', strtotime($item->actual_survey_date)) : null,
                    "duration" => $duration,
                    'notes' => $item->note,
                ];
                return $item;
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

    private function getDuration($status, $data)
    {
        if ($status == 'done') {
            return Carbon::now()->startOfDay()->diffInDays(Carbon::parse($data->actual_survey_date)->startOfDay(), true) . ' days';
        } else if ($status == 'scheduled') {
            return Carbon::now()->startOfDay()->diffInDays(Carbon::parse($data->survey_date)->startOfDay(), true) . ' days';
        } else {
            return Carbon::now()->startOfDay()->diffInDays(Carbon::parse($data->created_at)->startOfDay(), true) . ' days';
        }
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

    public function hasSurvey($contactId) : array
    {
        $isHaslead = $this->repository->hasSurvey($contactId);
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
    
        $data = [
            "survey_location_id" => $request['survey_location_id'],
        ];
        if (isset($request['survey_date'])) {
            $data['survey_date'] = $request['survey_date'];
        }
        if (isset($request['actual_survey_date'])) {
            $data['actual_survey_date'] = $request['actual_survey_date'];
        }
        if (isset($request['survey_documentation'])) {
            $data['survey_documentation'] = $request['survey_documentation'];
        }
        if (isset($request['unit_preference_id'])) {
            $data['unit_preference_id'] = $request['unit_preference_id'];
        }
        if (isset($request['notes'])) {
            $data['note'] = $request['notes'];
        }
        if (isset($request['pic'])) {
            $data['pic'] = $request['pic'];
        }

        $create = $this->repository->create($data, $request['lead_id']);
        if ($create['error']) {
            return $create;
        }
        $create = $create['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $create
        ];
    }

    public function update($id, $data) : array
    {
        if (isset($data['marketing_id'])) {
            $data['assign_to'] = $data['marketing_id'];
        }

        $update = $this->repository->update($id, $data);
        if ($update['error']) {
            return $update;
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => null
        ];
    }

    public function getNonSurveyLead($search = null) : array
    {
        $data = $this->repository->getNonSurveyLead($search);
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

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
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