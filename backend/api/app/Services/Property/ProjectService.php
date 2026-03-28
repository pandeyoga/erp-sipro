<?php
        
namespace App\Services\Property;

use App\Repositories\Property\ProjectRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProjectService
{
    public function __construct(
        protected ProjectRepository $repository
    ) {}

    public function index($request)
    {
        return $this->repository->index($request);
    }

    public function store($request)
    {
        $data = [
            'name' => $request['name'],
            'location' => $request['location'],
            'developer' => $request['developer'] ?? null,
            'area_total_sqm' => $request['area_total_sqm'],
            'start_date' => $request['start_date'],
            'status' => "active",
            'created_by' => auth()->user()->id,
            'site_plan_image' => $request['site_plan_image'] ?? null,
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
            'name' => $request['name'],
            'location' => $request['location'],
            'developer' => $request['developer'] ?? null,
            'area_total_sqm' => $request['area_total_sqm'],
            'start_date' => $request['start_date'],
            'status' => $request['status'],
            'site_plan_image' => $request['site_plan_image'] ?? null,
            'notes' => $request['notes'] ?? null
        ];

        return $this->repository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }
    
    
}