<?php
        
namespace App\Repositories\Property;

use App\Models\CashFlowIn;
use App\Models\Cluster;
use App\Models\Construction;
use App\Models\ConstructionPhase;
use App\Models\Project;
use App\Models\PropertyLoc;
use App\Models\PropertyQC;
use App\Models\RetentionCase;
use App\Models\Unit;
use App\Models\UnitProperty;
use App\Models\UnitPropertyHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class PropertyRepository
{
    public function index($request)
    {
        $page = $request['page'];
        $perPage = $request['per_page'];
        $search = $request['search'];
        $project = $request['project'];
        $cluster = $request['cluster'];
        $unit_type = $request['unit_type'];
        $sort_key = $request['sortKey'] ?? 'created_at';
        $sort_dir = $request['sortDir'] ?? 'desc';

        $unitProperty = UnitProperty::join('projects', 'projects.id', '=', 'unit_properties.project_id')
            ->join('clusters', 'clusters.id', '=', 'unit_properties.cluster_id')
            ->join('units', 'units.id', '=', 'unit_properties.unit_type_id')
            ->select(
                'unit_properties.id',
                'projects.name as project_name',
                'clusters.name as cluster_name',
                'units.type as unit_type',
                'unit_properties.unit_number as unit_number',
                'unit_properties.price',
                'unit_properties.status',
                'unit_properties.dev_substatus as construction_status',
                'unit_properties.notes'
            )
            ->orderBy('unit_properties.'.$sort_key, $sort_dir)
            ->when($search, function ($query) use ($search) {
                return $query->where('unit_properties.unit_number', 'ilike', '%' . $search . '%')
                    ->orWhere('projects.name', 'ilike', '%' . $search . '%')
                    ->orWhere('clusters.name', 'ilike', '%' . $search . '%')
                    ->orWhere('units.type', 'ilike', '%' . $search . '%');
            })
            ->when($project, function ($query) use ($project) {
                return $query->where('unit_properties.project_id', $project);
            })
            ->when($cluster, function ($query) use ($cluster) {
                return $query->where('unit_properties.cluster_id', $cluster);
            })
            ->when($unit_type, function ($query) use ($unit_type) {
                return $query->where('unit_properties.unit_type_id', $unit_type);
            })
            ->paginate($perPage, ['*'], 'page', $page);
            
        return [
            'error' => null,
            'result' => $unitProperty,
            'code' => 200
        ];
    }

    public function projectOptionLists()
    {
        $projects = Project::select('id', 'name')->get();

        return [
            'error' => null,
            'result' => $projects,
            'code' => 200
        ];
    }

    public function clusterOptionLists($projectId = null)
    {
        $clusters = Cluster::when($projectId, function ($query) use ($projectId) {
            return $query->where('project_id', $projectId);
        })->select('id', 'name')->get();

        return [
            'error' => null,
            'result' => $clusters,
            'code' => 200
        ];
    }

    public function unitTypeOptionLists()
    {
        $units = Unit::select('id', 'type')->get();

        return [
            'error' => null,
            'result' => $units,
            'code' => 200
        ];
    }

    public function store($data)
    {
        try {
            DB::beginTransaction();
            UnitProperty::create($data);
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
        $unit = UnitProperty::where('unit_properties.id', $id)
            ->join('projects', 'projects.id', '=', 'unit_properties.project_id')
            ->join('clusters', 'clusters.id', '=', 'unit_properties.cluster_id')
            ->join('units', 'units.id', '=', 'unit_properties.unit_type_id')
            ->leftjoin('reservations', 'reservations.property_unit_id', '=', 'unit_properties.id')
            ->leftjoin('leads', 'reservations.lead_id', '=', 'leads.id')
            ->leftjoin('contacts', 'leads.contact_id', '=', 'contacts.id')
            ->leftJoin('constructions', 'constructions.unit_property_id', '=', 'unit_properties.id')
            ->leftJoin('sub_contractors', 'sub_contractors.id', '=', 'constructions.sub_contractor_id')
            ->select(
                'unit_properties.id',
                'projects.id as project_id',
                'projects.name as project_name',
                'clusters.id as cluster_id',
                'clusters.name as cluster_name',
                'units.id as unit_type_id',
                'units.type as unit_type',
                'unit_properties.unit_number as unit_number',
                'unit_properties.price',
                'unit_properties.status',
                'unit_properties.dev_substatus as construction_status',
                'contacts.name as lead_name',
                'sub_contractors.name as sub_contractor',
                'constructions.id as construction_id',
                'unit_properties.notes',
            )
            ->first();

        if (!$unit) {
            return [
                'error' => 'Unit not found',
                'result' => null,
                'code' => 404
            ];
        }

        if ($unit->construction_id) {
            $constructionDocs = ConstructionPhase::where('construction_id', $unit->construction_id)
                ->select(
                    'construction_phase',
                    'documentation',
                    'created_at'
                )
                ->where('documentation', '!=', null)
                ->orderBy('created_at', 'desc')
                ->get()->makeHidden(['created_at']);
            
        } else {
            $constructionDocs = [];
        }


        $retention = RetentionCase::where('property_id', $unit->id)
                ->join('sub_contractors', 'sub_contractors.id', '=', 'retention_cases.sub_contractor_id')
                ->select(
                    'opened_at',
                    'description',
                    'status',
                    'resolved_at',
                    'estimated_resolved_at',
                    'case_pictures',
                    'case_documentations',
                    'sub_contractors.name as sub_contractor_name',
                    'notes',
                )
                ->orderBy('retention_cases.opened_at', 'desc')
                ->get()->map(function ($retention) {
                    $retention->case_pictures = $retention->case_pictures ?? [];
                    $retention->case_documentations = $retention->case_documentations ?? [];
                    return $retention;
                });

        $parent = CashFlowIn::where('property_id', $unit->id)
            ->select(
                'id',
                'total_amount',
                'paid_amount',
            )
            ->where('parent_id', null)
            ->first();

        if ($parent) {
            $payment = CashFlowIn::where('parent_id', $parent->id)
                ->selectRaw('description, total_amount, paid_amount, (total_amount - paid_amount) as remaining_amount')
                ->get();
        } else {
            $payment = [];
        }


        $percetageConstruction =[
            'pondasi' => 0,
            'naik_bata' => 5,
            'naik_atap' => 15,
            'plester_aci' => 30,
            'keramik_cat' => 60,
            'finishing' => 80,
            'done' => 100
        ];

        $unit = [
            'id' => $unit->id,
            'project_id' => $unit->project_id,
            'project_name' => $unit->project_name,
            'cluster_id' => $unit->cluster_id,
            'cluster_name' => $unit->cluster_name,
            'unit_type_id' => $unit->unit_type_id,
            'unit_type' => $unit->unit_type,
            'unit_number' => $unit->unit_number,
            'price' => $unit->price,
            'status' => $unit->status,
            'construction_status' => $unit->construction_status,
            'is_booked' => $unit->lead_name ? true : false,
            'customer' => $unit->lead_name,
            'sub_contractor' => $unit->sub_contractor,
            'notes' => $unit->notes,
            'construction_progress' => isset($percetageConstruction[$unit->construction_status]) ? $percetageConstruction[$unit->construction_status] . '%' : null,
            'construction_documentation' => $constructionDocs,
            'retention_cases' => $retention,
            'payment' => [
                'total_amount' => $parent !== null ? $parent->total_amount : null,
                'paid_amount' => $parent !== null ? $parent->paid_amount : null,
                'remaining_amount' => $parent !== null ? $parent->total_amount - $parent->paid_amount : null,
                'details' => $payment
            ]
        ];

        
        if (!$unit) {
            return [
                'error' => 'Unit not found',
                'result' => null,
                'code' => 404
            ];
        }
        return [
            'error' => null,
            'result' => $unit,
            'code' => 200
        ];
    }

    public function update($data, $id)
    {
        $property = UnitProperty::where('id', $id)->first();
        if (!$property) {
            return [
                'error' => 'Property not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();
            $property->update($data);
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

    public function updateStatus($status, $devSubStatus, $id)
    {
        try {
            DB::beginTransaction();
            $unitProperty = UnitProperty::where('id', $id)->first();

            $oldStatus = $unitProperty->status;
            $oldDevSubStatus = $unitProperty->dev_substatus;

            if ($oldStatus != $status || $oldDevSubStatus != $devSubStatus) {
                $unitProperty->status = $status;
                $unitProperty->dev_substatus = $devSubStatus;
                $unitProperty->save();
            }

            if ($oldStatus !== $status) {
                UnitPropertyHistory::create([
                    'unit_property_id' => $unitProperty->id,
                    'action_by' => auth()->user()->id,
                    'old_status' => $oldStatus,
                    'new_status' => $status,
                    'changed_at' => Carbon::now(),
                    'notes' => 'Update status from ' . $oldStatus . ' to ' . $status,
                ]);
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

    public function destroy($id)
    {   
        $unitProperty = UnitProperty::where('id', $id)->first();
        if (!$unitProperty) {
            return [
                'error' => 'Unit not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();
            $unitProperty->delete();
            // delete property siteplan
            $propertyLoc = PropertyLoc::where('property_id', $id)->first();
            if ($propertyLoc) {
                $propertyLoc->delete();
            }
            // delete construction
            $construction = Construction::where('unit_property_id', $id)->get();
            if ($construction) {
                foreach ($construction as $item) {
                    $item->delete();
                    $constructionPhase = ConstructionPhase::where('construction_id', $item->id)->get();
                    if ($constructionPhase) {
                        foreach ($constructionPhase as $item) {
                            $item->delete();
                        }
                    }
                }
            }
            
            $cashflow = CashFlowIn::where('property_id', $id)->get();
            if ($cashflow) {
                foreach ($cashflow as $item) {
                    $item->delete();
                }
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

    public function createQcItem($id, $data)
    {
        $property = UnitProperty::where('id', $id)->first();
        if (!$property) {
            return [
                'error' => 'Property not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();
            $data['property_id'] = $id;
            if ($data['evidence']) {
                $data['evidence'] = uploadFile('property/qc-evidence', $data['evidence']);
            }

            $qc = PropertyQC::create($data);
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
            'result' => [
                'id' => $qc->id
            ],
            'code' => 200
        ];
    }

    public function getQcItems($id)
    {
        $data = PropertyQC::where('property_id', $id)
            ->orderBy('created_at', 'desc')
            ->select('id', 'name', 'is_passed', 'comment', 'evidence', 'created_at')
            ->get();

        return [
            'error' => null,
            'result' => $data,
            'code' => 200
        ];
    }

    public function getQcItem($propertyId, $id)
    {
        $property = UnitProperty::where('id', $propertyId)->first();
        if (!$property) {
            return [
                'error' => 'Property not found',
                'result' => null,
                'code' => 404
            ];
        }

        $qc = PropertyQC::where('id', $id)
            ->select('id', 'name', 'is_passed', 'comment', 'evidence', 'created_at')
            ->orderBy('created_at', 'desc')
            ->first();
            
        if (!$qc) {
            return [
                'error' => 'Quality control item not found',
                'result' => null,
                'code' => 404
            ];
        }

        return [
            'error' => null,
            'result' => $qc,
            'code' => 200
        ];
    }

    public function updateQcItem($propertyId, $id, $data)
    {
        $property = UnitProperty::where('id', $propertyId)->first();
        if (!$property) {
            return [
                'error' => 'Property not found',
                'result' => null,
                'code' => 404
            ];
        }

        $qc = PropertyQC::where('id', $id)->first();
        if (!$qc) {
            return [
                'error' => 'Quality control item not found',
                'result' => null,
                'code' => 404
            ];
        }

        $oldEvidence = $qc->evidence;

        try {
            DB::beginTransaction();
            if ($data['evidence']) {
                $data['evidence'] = uploadFile('property/qc-evidence', $data['evidence']);
            }

            $qc->update($data);

            if ($data['evidence'] && $oldEvidence) {
                $path = explode('api/file/', $oldEvidence);
                if (isset($path[1])) {
                    deleteFile($path[1]);
                }
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

    public function destroyQcItem($propertyId, $id)
    {
        $property = UnitProperty::where('id', $propertyId)->first();
        if (!$property) {
            return [
                'error' => 'Property not found',
                'result' => null,
                'code' => 404
            ];
        }

        $qc = PropertyQC::where('id', $id)->first();
        if (!$qc) {
            return [
                'error' => 'Quality control item not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();
            $qc->delete();
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

    public function importQcItems($propertyId, $data)
    {
        $property = UnitProperty::where('id', $propertyId)->first();
        if (!$property) {
            return [
                'error' => 'Property not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();
            // hapus data lama
            PropertyQC::where('property_id', $propertyId)->delete();

            foreach ($data as $item) {
                PropertyQC::create([
                    'property_id' => $propertyId,
                    'name' => $item['ITEM QC PROPERTY'],
                    'is_passed' => null,
                ]);
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
}