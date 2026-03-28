<?php
        
namespace App\Repositories\Property;

use App\Models\Cluster;
use App\Models\Project;
use App\Models\UnitProperty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class ClusterRepository
{
    public function index($request)
    {
        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 10;
        $project = $request->project ?? null;
        $search = $request->search ?? null;

        $cluster = Cluster::orderBy('clusters.created_at', 'desc')
            ->orderBy('projects.updated_at', 'desc')
            ->join('projects', 'projects.id', '=', 'clusters.project_id')
            ->when($project, function ($query) use ($project) {
                return $query->where('projects.id', $project);
            })
            ->select(
                'clusters.id',
                'project_id',
                'clusters.name',
                'projects.name as project_name',
                'block_code'
            )
            ->when($search, function ($query) use ($search) {
                return $query->where('clusters.name', 'ilike', '%' . $search . '%');
            })
            ->paginate($perPage, ['*'], 'page', $page);
        
        return [
            'error' => null,
            'result' => $cluster,
            'code' => 200
        ];
    }

    // get list project
    public function getProject()
    {
        $project = Project::select('id', 'name')->get();
        return [
            'error' => null,
            'result' => $project,
            'code' => 200
        ];
    }

    public function store($data)
    {
        try {
            DB::beginTransaction();
            Cluster::create($data);
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
        $cluster = Cluster::where('clusters.id', $id)
            ->join('projects', 'projects.id', '=', 'project_id')
            ->select(
                'clusters.id',
                'project_id',
                'clusters.name',
                'projects.name as project_name',
                'block_code',
                'clusters.notes'
            )
            ->first();

        if (!$cluster) {
            return [
                'error' => 'Cluster not found',
                'result' => null,
                'code' => 404
            ];
        }
        return [
            'error' => null,
            'result' => $cluster,
            'code' => 200
        ];
    }

    public function update($data, $id)
    {
        $cluster = Cluster::where('id', $id)->first();
        if (!$cluster) {
            return [
                'error' => 'Cluster not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();
            
            $cluster->update($data);
            
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
        $cluster = Cluster::where('id', $id)->first();
        if (!$cluster) {
            return [
                'error' => 'Cluster not found',
                'result' => null,
                'code' => 404
            ];
        }

        $unit = UnitProperty::where('cluster_id', $id)->first();
        if ($unit) {
            return [
                'error' => 'Cluster has unit',
                'result' => null,
                'code' => 400
            ];
        }

        try {
            DB::beginTransaction();
            $cluster->delete();
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