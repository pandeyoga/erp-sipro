<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StoreClusterRequest;
use App\Http\Requests\Property\UpdateClusterRequest;
use App\Services\Property\ClusterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClusterController extends Controller
{
    public function __construct(protected ClusterService $service) {}

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
            'search' => 'nullable|string|max:255',
            'project' => 'nullable|uuid|exists:projects,id',
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

        return $this->paginatedResponse($data, 'Cluster retrieved successfully', 200);
    }

    public function getProject()
    {
        $project = $this->service->getProject();
        return $this->successResponse($project['result'], 'List Project retrieved successfully', 200);
    }

    public function store(StoreClusterRequest $request)
    {
        $request = $request->validated();
        $store = $this->service->store($request);
        if ($store['error']) {
            return $this->errorResponse($store['error'], null, $store['code']);
        }

        return $this->successResponse(null, 'Cluster created successfully', 201);
    }

    public function getById($id)
    {
        $getById = $this->service->getById($id);
        if ($getById['error']) {
            return $this->errorResponse($getById['error'], null, $getById['code']);
        }

        return $this->successResponse($getById['result'], 'Cluster retrieved successfully', 200);
    }

    public function update(UpdateClusterRequest $request, $id)
    {
        $request = $request->validated();

        $update = $this->service->update($request, $id);
        if ($update['error']) {
            return $this->errorResponse($update['error'], null, $update['code']);
        }

        return $this->successResponse(null, 'Cluster updated successfully', 200);
    }

    public function destroy($id)
    {
        $destroy = $this->service->destroy($id);
        if ($destroy['error']) {
            return $this->errorResponse($destroy['error'], null, $destroy['code']);
        }

        return $this->successResponse(null, 'Cluster deleted successfully', 200);
    }

}
