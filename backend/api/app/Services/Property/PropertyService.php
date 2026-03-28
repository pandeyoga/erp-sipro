<?php
        
namespace App\Services\Property;

use App\Repositories\Property\PropertyRepository;
use App\Repositories\Property\UnitRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class PropertyService
{
    public function __construct(
        protected PropertyRepository $repository
    ) {}

    public function index($request)
    {
        $request = [
            'page' => $request['page'] ?? 1,
            'per_page' => $request['per_page'] ?? 10,
            'search' => $request['search'] ?? null,
            'project' => $request['project'] ?? null,
            'cluster' => $request['cluster'] ?? null,
            'unit_type' => $request['unit_type'] ?? null,
            'sortKey' => $request['sortKey'] ?? 'created_at',
            'sortDir' => $request['sortDir'] ?? 'desc'
        ];

        return $this->repository->index($request);
    }

    public function projectOptionLists()
    {
        return $this->repository->projectOptionLists();
    }

    public function clusterOptionLists($projectId = null)
    {
        return $this->repository->clusterOptionLists($projectId);
    }

    public function unitTypeOptionLists()
    {
        return $this->repository->unitTypeOptionLists();
    }

    public function store($request)
    {
        $data = [
            "project_id" => $request['project_id'],
            "cluster_id" => $request['cluster_id'],
            "unit_type_id" => $request['unit_type_id'],
            "unit_number" => $request['unit_number'],
            "notes" => $request['notes'] ?? null
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
            "project_id" => $request['project_id'],
            "cluster_id" => $request['cluster_id'],
            "unit_type_id" => $request['unit_type_id'],
            "unit_number" => $request['unit_number'],
            'notes' => $request['notes'] ?? null
        ];

        return $this->repository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }

    public function createQcItem($id, $data)
    {
        $data = [
            'name' => $data['name'],
            'is_passed' => $data['is_passed'] ?? null,
            'evidence' => $data['evidence'] ?? null,
            'comment' => $data['comment'] ?? null
        ];

        return $this->repository->createQcItem($id, $data);
    }

    public function getQcItems($id)
    {
        return $this->repository->getQcItems($id);
    }

    public function getQcItem($propertyId, $id)
    {
        return $this->repository->getQcItem($propertyId, $id);
    }

    public function updateQcItem($propertyId, $id, $data)
    {
        $data = [
            'name' => $data['name'],
            'is_passed' => $data['is_passed'] ?? null,
            'evidence' => $data['evidence'] ?? null,
            'comment' => $data['comment'] ?? null
        ];

        return $this->repository->updateQcItem($propertyId, $id, $data);
    }

    public function destroyQcItem($propertyId, $id)
    {
        return $this->repository->destroyQcItem($propertyId, $id);
    }

    public function importQcItems($propertyId, $file)
    {
        // load dengan fast Excel
        $data =  (new FastExcel)->import($file);

        return $this->repository->importQcItems($propertyId, $data);
    }
    
    
}