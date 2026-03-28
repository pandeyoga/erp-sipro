<?php
        
namespace App\Services\Property;

use App\Repositories\Property\RetentionCaseRepository;
use App\Repositories\Property\UnitRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RetentionCaseService
{
    public function __construct(
        protected RetentionCaseRepository $repository
    ) {}

    public function index($request)
    {
        return $this->repository->index($request);
    }

    public function summary()
    {
        $summary = $this->repository->summary();
        if ($summary['error']) {
            return $summary;
        }
        $summary = $summary['result'];

        // open,in_progress,resolved
        $data = [
            [
                'status' => 'open',
                'total' => $summary->where('status', 'open')->first()->total ?? 0
            ],
            [
                'status' => 'in_progress',
                'total' => $summary->where('status', 'in_progress')->first()->total ?? 0
            ],
            [
                'status' => 'resolved',
                'total' => $summary->where('status', 'resolved')->first()->total ?? 0
            ]
        ];

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    // getReservedLead
    public function getReservedLead()
    {
        return $this->repository->getReservedLead();
    }

    // getAvailableSubCon
    public function getAvailableSubCon()
    {
        return $this->repository->getAvailableSubCon();
    }

    public function store($request)
    {
        $data = [
            'lead_id' => $request['lead_id'],
            'description' => $request['description'],
            'case_pictures' => $request['case_pictures'],
            'case_date' => $request['case_date'],
            'estimated_resolved_day' => $request['estimated_resolved_day'],
            'sub_contractor_id' => $request['sub_contractor_id'],
            'notes' => $request['notes'] ?? null
        ];

        return $this->repository->store($data);
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }

    public function update($request, $id)
    {
        $data = [
            'status' => $request['status'],
            'case_documentations' => $request['case_documentations'],
            'notes' => $request['notes']
        ];

        return $this->repository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }
    
    
}