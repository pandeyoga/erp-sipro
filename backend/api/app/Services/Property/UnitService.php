<?php
        
namespace App\Services\Property;

use App\Repositories\Property\UnitRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UnitService
{
    public function __construct(
        protected UnitRepository $repository
    ) {}

    public function index($request)
    {
        return $this->repository->index($request);
    }

    public function store($request)
    {
        
        $data = [
            'type' => $request['type'],
            'land_area' => $request['land_area'],
            'building_area' => $request['building_area'],
            'notes' => $request['notes'] ?? null,
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
            'type' => $request['type'],
            'land_area' => $request['land_area'],
            'building_area' => $request['building_area'],
            'notes' => $request['notes'] ?? null
        ];

        return $this->repository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }
    
    
}