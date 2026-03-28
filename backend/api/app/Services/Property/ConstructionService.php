<?php
        
namespace App\Services\Property;

use App\Repositories\Property\ConstructionRepository;
use App\Repositories\Property\UnitRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ConstructionService
{
    public function __construct(
        protected ConstructionRepository $repository
    ) {}

    public function index($request)
    {
        return $this->repository->index($request);
    }

    // summary
    public function summary()
    {
        $summary = $this->repository->summary();
        if ($summary['error']) {
            return $summary;
        }
        $summary = $summary['result'];

        $data = [
            [
                'status' => 'pondasi',
                'total' => $summary->where('status', 'pondasi')->first()->total ?? 0
            ],
            [
                'status' => 'naik_bata',
                'total' => $summary->where('status', 'naik_bata')->first()->total ?? 0
            ],
            [
                'status' => 'naik_atap',
                'total' => $summary->where('status', 'naik_atap')->first()->total ?? 0
            ],
            [
                'status' => 'plester_aci',
                'total' => $summary->where('status', 'plester_aci')->first()->total ?? 0
            ],
            [
                'status' => 'keramik_cat',
                'total' => $summary->where('status', 'keramik_cat')->first()->total ?? 0
            ],
            [
                'status' => 'finishing',
                'total' => $summary->where('status', 'finishing')->first()->total ?? 0
            ],
            [
                'status' => 'done',
                'total' => $summary->where('status', 'done')->first()->total ?? 0
            ],
        ];

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function store($request)
    {
        $data = [
            'property_unit_id' => $request['property_unit_id'],
            'start_date' => $request['start_date'],
            'estimated_end_date' => $request['estimated_end_date'],
            'sub_contractor_id' => $request['sub_contractor_id'],
            'notes' => $request['notes'] ?? null,
        ];

        $spk = $request['spk'];

        return $this->repository->store($data, $spk);
    }

    public function getProjects()
    {
        return $this->repository->getProjects();
    }

    public function getClusters($projectId)
    {
        return $this->repository->getClusters($projectId);
    }

    public function getUnitTypes()
    {
        return $this->repository->getUnitTypes();
    }

    public function getProperties($projectId, $clusterId, $unitTypeId)
    {
        return $this->repository->getProperties($projectId, $clusterId, $unitTypeId);
    }

    // getAvailableSubCon
    public function getAvailableSubCon()
    {
        return $this->repository->getAvailableSubCon();
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }

    public function update($request, $id)
    {
        $data = [
            'status_pondasi' => $request['status_pondasi'],
            'dokumentasi_pondasi' => $request['dokumentasi_pondasi'] ?? null,
            'status_naik_bata' => $request['status_naik_bata'],
            'dokumentasi_naik_bata' => $request['dokumentasi_naik_bata'] ?? null,
            'status_naik_atap' => $request['status_naik_atap'],
            'dokumentasi_naik_atap' => $request['dokumentasi_naik_atap'] ?? null,
            'status_plester_aci' => $request['status_plester_aci'],
            'dokumentasi_plester_aci' => $request['dokumentasi_plester_aci'] ?? null,
            'status_keramik_cat' => $request['status_keramik_cat'],
            'dokumentasi_keramik_cat' => $request['dokumentasi_keramik_cat'] ?? null,
            'status_finishing' => $request['status_finishing'],
            'dokumentasi_finishing' => $request['dokumentasi_finishing'] ?? null,
            'notes' => $request['notes'] ?? null
        ];

        return $this->repository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }
    
    
}