<?php
        
namespace App\Repositories\Property;

use App\Models\Cluster;
use App\Models\Construction;
use App\Models\ConstructionPhase;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\SubContractor;
use App\Models\Unit;
use App\Models\UnitProperty;
use Illuminate\Support\Facades\DB;
class ConstructionRepository
{
    public function index($request)
    {
        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 10;

        $search = $request->search ?? null;


        $subContractorId = $request->sub_contractor_id ?? null;
        $sortKey = $request->sortKey ?? null;
        $sortDir = $request->sortDir ?? "asc";
        $status = $request->status ?? null;
        $clusterId = $request->cluster_id ?? null;

        $units = Construction::join('unit_properties', 'unit_properties.id', '=', 'constructions.unit_property_id')
            ->join('units', 'units.id', '=', 'unit_properties.unit_type_id')
            ->join('projects', 'projects.id', '=', 'unit_properties.project_id')
            ->join('clusters', 'clusters.id', '=', 'unit_properties.cluster_id')
            ->join('sub_contractors', 'sub_contractors.id', '=', 'constructions.sub_contractor_id')
            ->leftJoin('reservations', 'reservations.property_unit_id', '=', 'unit_properties.id')
            ->leftJoin('leads', 'leads.id', '=', 'reservations.lead_id')
            ->leftjoin('contacts', 'contacts.id', '=', 'leads.contact_id')
            ->when($subContractorId, function ($query) use ($subContractorId) {
                return $query->where('constructions.sub_contractor_id', $subContractorId);
            })
            ->when($sortKey == 'name', function ($query) use ($sortKey, $sortDir) {
                $query->orderBy('contacts.name', $sortDir);
            })
            ->when($sortKey == 'duration', function ($query) use ($sortKey, $sortDir) {
                $query->orderBy(DB::raw("DATE_PART('day', NOW()::timestamp - constructions.start_date::timestamp)"), $sortDir);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('constructions.status', $status);
            })
            ->when($clusterId, function ($query) use ($clusterId) {
                return $query->where('clusters.id', $clusterId);
            })
            ->select(
                'constructions.id',
                'contacts.name as lead_name',
                'constructions.status',
                'constructions.start_date',
                'constructions.estimated_end_date',
                'projects.name as project_name',
                'clusters.name as cluster_name',
                'units.type as unit_type',
                'sub_contractors.name as sub_contractor_name',
                'sub_contractors.id as sub_contractor_id',
                'unit_properties.unit_number as unit_number',
                DB::raw("DATE_PART('day', NOW()::timestamp - constructions.start_date::timestamp) AS duration")
            )
            ->when($search, function ($query) use ($search) {
                return $query->where('contacts.name', 'ilike', '%' . $search . '%')
                    ->orWhere('projects.name', 'ilike', '%' . $search . '%')
                    ->orWhere('clusters.name', 'ilike', '%' . $search . '%')
                    ->orWhere('units.type', 'ilike', '%' . $search . '%')
                    ->orWhere('sub_contractors.name', 'ilike', '%' . $search . '%')
                    ->orWhere('unit_properties.unit_number', 'ilike', '%' . $search . '%');
            })
            ->paginate($perPage, ['*'], 'page', $page);
        return [
            'error' => null,
            'result' => $units,
            'code' => 200
        ];
    }

    public function summary()
    {
        $data = Construction::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        return [
            'error' => false,
            'code' => 200,
            'result' => $data
        ];
    }

    public function getProjects()
    {
        $projects = Project::select('id', 'name')->get();
        return [
            'error' => false,
            'code' => 200,
            'result' => $projects
        ];
    }

    public function getClusters($project_id)
    {
        $clusters = Cluster::where('project_id', $project_id)->select('id', 'name')->get();
        return [
            'error' => false,
            'code' => 200,
            'result' => $clusters
        ];
    }

    public function getUnitTypes()
    {
        $unitType = Unit::select('id', 'type')->get();
        return [
            'error' => false,
            'code' => 200,
            'result' => $unitType
        ];
    }


    public function getProperties($projectId, $clusterId, $unitTypeId)
    {
        $unitProperties = UnitProperty::where('unit_properties.project_id', $projectId)
            ->where('unit_properties.cluster_id', $clusterId)
            ->where('unit_properties.unit_type_id', $unitTypeId)
            ->leftJoin('constructions', 'constructions.unit_property_id', '=', 'unit_properties.id')
            ->whereNull('constructions.id')
            ->select('unit_properties.id', 'unit_number')
            ->get();

        return [
            'error' => null,
            'result' => $unitProperties,
            'code' => 200
        ];
    }

    public function getAvailableSubCon()
    {
        $data = SubContractor::select('id', 'name')->get();

        return [
            'error' => null,
            'result' => $data,
            'code' => 200
        ];
    }

    public function store($data, $spk)
    {

        $propertyUnit = UnitProperty::where('id', $data['property_unit_id'])->first();
        if (!$propertyUnit) {
            return [
                'error' => 'Property unit not found',
                'result' => null,
                'code' => 404
            ];
        }

        // cek apakah unit sudah ada construction
        $construction = Construction::where('unit_property_id', $propertyUnit->id)->first();
        if ($construction) {
            return [
                'error' => 'Unit already has construction',
                'result' => null,
                'code' => 400
            ];
        }

        $subContractor = SubContractor::where('id', $data['sub_contractor_id'])->first();
        if (!$subContractor) {
            return [
                'error' => 'Sub contractor not found',
                'result' => null,
                'code' => 404
            ];
        }

        $data = [
            'project_id' => $propertyUnit->project_id,
            'unit_property_id' => $propertyUnit->id,
            'start_date' => $data['start_date'],
            'estimated_end_date' => $data['estimated_end_date'],
            'actual_end_date' => null,
            'sub_contractor_id' => $data['sub_contractor_id'],
            'status' => 'pondasi',
            'notes' => $data['notes'],
        ];

        try {
            DB::beginTransaction();

            if ($spk) {
                $filename = uploadFile('property/construction/spk-' . $propertyUnit->id, $spk);
                if ($filename === false) {
                    DB::rollBack();
                    return [
                        'error' => 'Failed to upload file',
                        'result' => null,
                        'code' => 500
                    ];
                }

                $data['spk'] = $filename;
            }

            $construction = Construction::create($data);

            foreach (config('setting.construction_phases') as $phase) {
                $data = [
                    'construction_id' => $construction->id,
                    'construction_phase' => $phase,
                ];
                ConstructionPhase::create($data);
            }

            $propertyRepository = new PropertyRepository();
            $updateStatus = $propertyRepository->updateStatus('under_development', 'pondasi', $propertyUnit->id);
            if ($updateStatus['error']) {
                DB::rollBack();
                return $updateStatus;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }

        return [
            'error' => null,
            'result' => null,
            'code' => 200
        ];
    }

    public function getById($id)
    {
        $construction = Construction::where('constructions.id', $id)
            ->join('unit_properties', 'unit_properties.id', '=', 'constructions.unit_property_id')
            ->join('units', 'units.id', '=', 'unit_properties.unit_type_id')
            ->join('projects', 'projects.id', '=', 'unit_properties.project_id')
            ->join('clusters', 'clusters.id', '=', 'unit_properties.cluster_id')
            ->join('sub_contractors', 'sub_contractors.id', '=', 'constructions.sub_contractor_id')
            ->select(
                'constructions.id',
                'constructions.project_id',
                'constructions.unit_property_id',
                'constructions.start_date',
                'constructions.estimated_end_date',
                'constructions.actual_end_date',
                'constructions.status',
                'constructions.notes',
                'projects.name as project_name',
                'clusters.name as cluster_name',
                'units.type as unit_type',
                'unit_properties.unit_number as unit_number',
                'unit_properties.price as unit_price',
                'unit_properties.construction_notes',
                'sub_contractors.id as sub_contractor_id',
                'sub_contractors.name as sub_contractor_name'
            )
            ->first();

        $constructionPhases = ConstructionPhase::where('construction_id', $id)
            ->select('construction_phase', 'status', 'documentation')
            ->get();

        if (!$construction || !$constructionPhases) {
            return [
                'error' => 'Construction not found',
                'result' => null,
                'code' => 404
            ];
        }
    
        $construction->construction_phases = $constructionPhases;

        return [
            'error' => null,
            'result' => $construction,
            'code' => 200
        ];
    }

    public function update($data, $id)
    {
        $construction = Construction::where('id', $id)->first();
        if (!$construction) {
            return [
                'error' => 'Construction not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();
            
            $status = $construction->status;
            $propertyStatus = "under_development";

            foreach (config('setting.construction_phases') as $phase) {
                $constructionPhase = ConstructionPhase::where('construction_id', $construction->id)->where('construction_phase', $phase)->first();
                $oldDoc = $constructionPhase->documentation;
                if ($data['dokumentasi_' . $phase]) {
                    $filename = uploadFile('property/construction/documentation-' . $phase , $data['dokumentasi_' . $phase]);
                    if ($filename === false) {
                        DB::rollBack();
                        return [
                            'error' => 'Failed to upload file',
                            'result' => null,
                            'code' => 500
                        ];
                    }

                    if ($filename) {
                        if ($oldDoc) {
                            $path = explode('api/file/', $oldDoc);
                            if (isset($path[1])) {
                                deleteFile($path[1]);
                            }
                        }
                    }

                    $constructionPhase->documentation = $filename ?? null;
                }

                if ($data['status_' . $phase]) {
                    $constructionPhase->status = $data['status_' . $phase];
                    if ($data['status_' . $phase] == 'in_progress' || $data['status_' . $phase] == 'completed') {
                        $status = $phase;
                    }
                }
                
                $constructionPhase->save();
            }
            
            if ($data['status_finishing'] == 'completed') {
                $status = 'done';
                $propertyStatus = "available";
            }

            $construction->status = $status;
            $construction->notes = $data['notes'] ?? null;
            $construction->save();

            $propertyRepository = new PropertyRepository();
            $propertyRepository->updateStatus($propertyStatus, $status, $construction->unit_property_id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }

        return [
            'error' => null,
            'result' => null,
            'code' => 200
        ];
    }

    public function destroy($id)
    {   
        $construction = Construction::where('id', $id)->first();
        if (!$construction) {
            return [
                'error' => 'Construction not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();

            ConstructionPhase::where('construction_id', $construction->id)->delete();
            $construction->delete();

            // update property status
            $propertyRepository = new PropertyRepository();
            $propertyRepository->updateStatus('belum_dibangun', null, $construction->unit_property_id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }

        return [
            'error' => null,
            'result' => null,
            'code' => 200
        ];
    }
}