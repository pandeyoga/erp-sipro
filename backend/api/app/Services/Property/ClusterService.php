<?php
        
namespace App\Services\Property;

use App\Repositories\Property\ClusterRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ClusterService
{
    public function __construct(
        protected ClusterRepository $repository
    ) {}

    public function index($request)
    {
        return $this->repository->index($request);
    }

    public function getProject()
    {
        return $this->repository->getProject();
    }

    public function store($request)
    {
        $data = [
            'project_id' => $request['project'],
            'name' => $request['name'],
            'block_code' => $request['block_code'],
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
            'project_id' => $request['project'],
            'name' => $request['name'],
            'block_code' => $request['block_code'],
            'notes' => $request['notes'] ?? null
        ];

        return $this->repository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }
    
    
}