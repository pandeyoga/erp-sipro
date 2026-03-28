<?php
        
namespace App\Services\Property;

use App\Repositories\Property\SubContractorRepository;
use App\Repositories\Property\UnitRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SubContractorService
{
    public function __construct(
        protected SubContractorRepository $repository
    ) {}

    public function index($request)
    {
        return $this->repository->index($request);
    }

    public function store($request)
    {
        
        $data = [
            'name' => $request['name'],
            'created_at' => $request['added_at']
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
            'created_at' => $request['added_at']
        ];

        return $this->repository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }
    
    
}