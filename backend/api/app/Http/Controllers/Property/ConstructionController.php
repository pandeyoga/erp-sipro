<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StoreConstructionRequest;
use App\Http\Requests\Property\StoreUnitRequest;
use App\Http\Requests\Property\UpdateConstructionRequest;
use App\Services\Property\ConstructionService;
use App\Services\Property\UnitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConstructionController extends Controller
{
    public function __construct(protected ConstructionService $service) {}

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pondasi,naik_bata,naik_atap,plester_aci,keramik_cat,finishing,done',
            'cluster_id' => 'nullable|uuid|exists:clusters,id',
            'sub_contractor_id' => 'nullable|uuid|exists:sub_contractors,id',
            'sortKey' => 'nullable|in:name,duration',
            'sortDir' => 'nullable|in:asc,desc',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $index = $this->service->index($request);
        if ($index['error']) {
            return $this->errorResponse($index['error'], null, $index['code']);
        }
        $data = $index['result'];

        return $this->paginatedResponse($data, 'Constructions retrieved successfully', 200);
    }

    public function summary()
    {
        $summary = $this->service->summary();
        if ($summary['error']) {
            return $this->errorResponse($summary['error'], null, $summary['code']);
        }

        return $this->successResponse($summary['result'], 'Construction retrieved successfully', 200);
    }

    public function getProjects()
    {
        $getProjects = $this->service->getProjects();
        if ($getProjects['error']) {
            return $this->errorResponse($getProjects['error'], null, $getProjects['code']);
        }

        return $this->successResponse($getProjects['result'], 'List Project retrieved successfully', 200);
    }

    public function getClusters($projectId)
    {
        $getClusters = $this->service->getClusters($projectId);
        if ($getClusters['error']) {
            return $this->errorResponse($getClusters['error'], null, $getClusters['code']);
        }

        return $this->successResponse($getClusters['result'], 'List Cluster retrieved successfully', 200);
    }

    public function getUnitTypes()
    {
        $getUnitTypes = $this->service->getUnitTypes();
        if ($getUnitTypes['error']) {
            return $this->errorResponse($getUnitTypes['error'], null, $getUnitTypes['code']);
        }

        return $this->successResponse($getUnitTypes['result'], 'List Unit Type retrieved successfully', 200);
    }



    public function getProperties($projectId, $clusterId, $unitTypeId)
    {
        $getProperties = $this->service->getProperties($projectId, $clusterId, $unitTypeId);
        if ($getProperties['error']) {
            return $this->errorResponse($getProperties['error'], null, $getProperties['code']);
        }

        return $this->successResponse($getProperties['result'], 'Construction retrieved successfully', 200);
    }

    public function getAvailableSubCon()
    {
        $getAvailableSubCon = $this->service->getAvailableSubCon();
        if ($getAvailableSubCon['error']) {
            return $this->errorResponse($getAvailableSubCon['error'], null, $getAvailableSubCon['code']);
        }

        return $this->successResponse($getAvailableSubCon['result'], 'Construction retrieved successfully', 200);
    }

    public function store(StoreConstructionRequest $request)
    {
        $request = $request->validated();
        $store = $this->service->store($request);
        if ($store['error']) {
            return $this->errorResponse($store['error'], null, $store['code']);
        }

        return $this->successResponse(null, 'Construction created successfully', 201);
    }

    public function getById($id)
    {
        $getById = $this->service->getById($id);
        if ($getById['error']) {
            return $this->errorResponse($getById['error'], null, $getById['code']);
        }

        return $this->successResponse($getById['result'], 'Construction retrieved successfully', 200);
    }

    public function update(UpdateConstructionRequest $request, $id)
    {
        $request = $request->validated();

        $update = $this->service->update($request, $id);
        if ($update['error']) {
            return $this->errorResponse($update['error'], null, $update['code']);
        }

        return $this->successResponse(null, 'Construction updated successfully', 200);
    }

    public function destroy($id)
    {
        $destroy = $this->service->destroy($id);
        if ($destroy['error']) {
            return $this->errorResponse($destroy['error'], null, $destroy['code']);
        }

        return $this->successResponse(null, 'Construction deleted successfully', 200);
    }

}
