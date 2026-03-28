<?php
        
namespace App\Repositories\Property;

use App\Models\Cluster;
use App\Models\Project;
use App\Models\UnitProperty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class ProjectRepository
{
    public function index($request)
    {
        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 10;

        $search = $request->search ?? null;
        $projects = Project::orderBy('created_at', 'desc')
            ->select(
                'id',
                'name',
                'location',
                'developer',
                'area_total_sqm',
                'start_date',
                'status'
            )
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'ilike', '%' . $search . '%');
            })
            ->paginate($perPage, ['*'], 'page', $page);
        return [
            'error' => null,
            'result' => $projects,
            'code' => 200
        ];
    }
    public function store($data)
    {
        try {
            DB::beginTransaction();
            if ($data['site_plan_image']) {
                $data['site_plan_image'] = uploadFile('property/siteplan', $data['site_plan_image'], Str::slug($data['name']));
                if (!$data['site_plan_image']) {
                    DB::rollBack();
                    return [
                        'error' => 'Failed to upload file',
                        'result' => null,
                        'code' => 500
                    ];
                }
            } else {
                $data['site_plan_image'] = null;
            }
            $project = Project::create($data);
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

    public function getById($id)
    {
        $project = Project::where('id', $id)
            ->select(
                'id',
                'name',
                'location',
                'developer',
                'area_total_sqm',
                'start_date',
                'status',
                'created_by',
                'site_plan_image',
            )
            ->first();
        $project->site_plan_image = $project->site_plan_image ? url($project->site_plan_image) : null;
        if (!$project) {
            return [
                'error' => 'Project not found',
                'result' => null,
                'code' => 404
            ];
        }
        return [
            'error' => null,
            'result' => $project,
            'code' => 200
        ];
    }

    public function update($data, $id)
    {
        $project = Project::where('id', $id)->first();
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
            if ($data['site_plan_image']) {
                $filenameWithExtension = Str::uuid() . '.' . $data['site_plan_image']->getClientOriginalExtension();
                $data['site_plan_image'] = uploadFile('property/siteplan', $data['site_plan_image'], $filenameWithExtension);
                if (!$data['site_plan_image']) {
                    DB::rollBack();
                    return [
                        'error' => 'Failed to upload file',
                        'result' => null,
                        'code' => 500
                    ];
                }
            } else {
                unset($data['site_plan_image']);
            }

            $project->update($data);

            if (isset($data['site_plan_image'])) {
                if ($oldSiteplan) {
                    $path = explode('api/file/', $oldSiteplan);
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

    public function destroy($id)
    {
        // cek apah ada cluster
        $cluster = Cluster::where('project_id', $id)->first();
        if ($cluster) {
            return [
                'error' => 'Project has cluster',
                'result' => null,
                'code' => 400
            ];
        }
        
        $project = Project::where('id', $id)->first();
        if (!$project) {
            return [
                'error' => 'Project not found',
                'result' => null,
                'code' => 404
            ];
        }

        $unit = UnitProperty::where('project_id', $id)->first();
        if ($unit) {
            return [
                'error' => 'Project has unit',
                'result' => null,
                'code' => 400
            ];
        }

        try {
            DB::beginTransaction();
            $path = explode('api/file/', $project->site_plan_image);
            if (isset($path[1])) {
                deleteFile(isset($path[1]) ? $path[1] : null);
            }
            
            $project->delete();
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