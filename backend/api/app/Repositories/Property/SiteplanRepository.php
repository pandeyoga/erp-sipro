<?php
        
namespace App\Repositories\Property;

use App\Models\Cluster;
use App\Models\Project;
use App\Models\PropertyLoc;
use App\Models\UnitProperty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class SiteplanRepository
{
    public function index($projectId)
    {
        $project = Project::where('id', $projectId)->first();
        if (!$project) {
            return [
                'error' => 'Project not found',
                'result' => null,
                'code' => 404
            ];
        }

        $unitsSiteplan = PropertyLoc::join('unit_properties', 'property_locs.property_id', '=', 'unit_properties.id')
            ->join('clusters', 'unit_properties.cluster_id', '=', 'clusters.id')
            ->join('units', 'unit_properties.unit_type_id', '=', 'units.id')
            ->where('unit_properties.project_id', $projectId)
            ->select(
                'unit_properties.id',
                'clusters.name as cluster_name',
                'units.type as unit_type',
                'unit_properties.unit_number',
                'unit_properties.status',
                'property_locs.top',
                'property_locs.left',
                'property_locs.width',
                'property_locs.height',
                'property_locs.rotate'
            )
            ->get();
        
        $data = [
            'project_id' => $projectId,
            'site_plan_image' => url($project->site_plan_image),
            'units' => $unitsSiteplan
        ];
        
        return [
            'error' => null,
            'result' => $data,
            'code' => 200
        ];
    }


    public function changeSiteplanImage($image, $projectId)
    {
        $project = Project::where('id', $projectId)->first();
        if (!$project) {
            return [
                'error' => 'Project not found',
                'result' => null,
                'code' => 404
            ];
        }

        $oldSiteplan = $project->site_plan_image;

        try {
            DB::beginTransaction();

            $filenameWithExtension = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $filename = uploadFile('property/siteplan', $image, $filenameWithExtension);
            if (!$filename) {
                DB::rollBack();
                return [
                    'error' => 'Failed to upload file',
                    'result' => null,
                    'code' => 500
                ];
            }

            $project->update([
                'site_plan_image' => $filename
            ]);

            if ($oldSiteplan) {
                $path = explode('api/file/', $oldSiteplan);
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
            'result' => $project,
            'code' => 200
        ];

    }

    public function getUnitPropertyList($projectId)
    {
        $list = UnitProperty::where('unit_properties.project_id', $projectId)
            ->join('clusters', 'unit_properties.cluster_id', '=', 'clusters.id')
            ->join('units', 'unit_properties.unit_type_id', '=', 'units.id')
            ->select(
                'unit_properties.id',
                'clusters.name as cluster_name',
                'units.type as unit_type',
                'unit_properties.unit_number',
                'unit_properties.status'
            )
            ->whereDoesntHave('propertyLoc')
            ->whereIn('unit_properties.status', ['belum_dibangun', 'under_development', 'available'])
            ->get()->groupBy('cluster_name')->map(function ($item) {
                return $item->groupBy('unit_type')->map(function ($item) {
                    return $item->map(function ($item) {
                        return $item->only('id', 'unit_number', 'status');
                    });
                });
            });
        
        return [
            'error' => null,
            'result' => $list,
            'code' => 200
        ];
    }

    public function store($data, $projectId)
    {
        $project = Project::where('id', $projectId)->first();
        if (!$project) {
            return [
                'error' => 'Project not found',
                'result' => null,
                'code' => 404
            ];
        }

        $property = UnitProperty::where('id', $data['property_id'])
            ->where('project_id', $projectId)
            ->first();

        $propertyLoc = PropertyLoc::where('property_id', $data['property_id'])->first();
        if ($propertyLoc) {
            return [
                'error' => 'Property Unit already exist',
                'result' => null,
                'code' => 200
            ];
        }

        
        if (!$property) {
            return [
                'error' => 'Property not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();
            $project = PropertyLoc::create($data);
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
            'result' => $data['property_id'],
            'code' => 200
        ];
    }

    public function getById($projectId, $propertyId)
    {
        $project = Project::where('id', $projectId)->first();
        if (!$project) {
            return [
                'error' => 'Project not found',
                'result' => null,
                'code' => 404
            ];
        }

        $property = PropertyLoc::where('property_locs.property_id', $propertyId)
            ->where('unit_properties.project_id', $projectId)
            ->join('unit_properties', 'property_locs.property_id', '=', 'unit_properties.id')
            ->join('clusters', 'unit_properties.cluster_id', '=', 'clusters.id')
            ->join('units', 'unit_properties.unit_type_id', '=', 'units.id')
            ->select(
                'unit_properties.id',
                'clusters.name as cluster_name',
                'units.type as unit_type',
                'unit_properties.unit_number',
                'unit_properties.status',
                'unit_properties.dev_substatus as construction_status',
                'unit_properties.notes',
                'property_locs.top',
                'property_locs.left',
                'property_locs.width',
                'property_locs.height',
                'property_locs.rotate'
            )
            ->first();
        if (!$property) {
            return [
                'error' => 'Property Unit not found',
                'result' => null,
                'code' => 404
            ];
        }
        return [
            'error' => null,
            'result' => $property,
            'code' => 200
        ];
    }

    public function update($data, $projectId, $propertyId)
    {
        $project = Project::where('id', $projectId)->first();
        if (!$project) {
            return [
                'error' => 'Project not found',
                'result' => null,
                'code' => 404
            ];
        }

        $property = UnitProperty::where('id', $propertyId)
            ->where('project_id', $projectId)
            ->first();

        $propertyLoc = PropertyLoc::where('property_id', $propertyId)->first();
        if (!$propertyLoc) {
            return [
                'error' => 'Property Unit not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();
            $propertyLoc->update($data);
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
            'result' => $propertyLoc,
            'code' => 200
        ];
    }

    public function destroy($projectId, $propertyId)
    {
        $project = Project::where('id', $projectId)->first();
        if (!$project) {
            return [
                'error' => 'Project not found',
                'result' => null,
                'code' => 404
            ];
        }
        try {
            DB::beginTransaction();
            PropertyLoc::where('property_id', $propertyId)->delete();
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