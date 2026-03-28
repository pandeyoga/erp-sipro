<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Services\Property\PropertyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    public function __construct(protected PropertyService $service) {}

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
            'search' => 'nullable|string|max:255',
            'project' => 'nullable|uuid|exists:projects,id',
            'cluster' => 'nullable|uuid|exists:clusters,id',
            'unit_type' => 'nullable|uuid|exists:units,id',
            'sortKey' => 'nullable|in:created_at,unit_number,price',
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

        return $this->paginatedResponse($data, 'Property retrieved successfully', 200);
    }

    public function projectOptionLists()
    {
        $list = $this->service->projectOptionLists();
        if ($list['error']) {
            return $this->errorResponse($list['error'], null, $list['code']);
        }
        
        return $this->successResponse($list['result'], 'Project lists retrieved successfully', 200);
    }

    public function clusterOptionLists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project' => 'nullable|uuid|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $projectId = $request->project;

        $list = $this->service->clusterOptionLists($projectId);
        if ($list['error']) {
            return $this->errorResponse($list['error'], null, $list['code']);
        }
        
        return $this->successResponse($list['result'], 'Cluster lists retrieved successfully', 200);
    }

    public function unitTypeOptionLists()
    {
        $list = $this->service->unitTypeOptionLists();
        if ($list['error']) {
            return $this->errorResponse($list['error'], null, $list['code']);
        }
        
        return $this->successResponse($list['result'], 'Unit type lists retrieved successfully', 200);
    }

    public function store(StorePropertyRequest $request)
    {
        $request = $request->validated();
        $store = $this->service->store($request);
        if ($store['error']) {
            return $this->errorResponse($store['error'], null, $store['code']);
        }
        
        return $this->successResponse(null, 'Property created successfully', 201);
    }

    public function getById($id)
    {
        $get = $this->service->getById($id);
        if ($get['error']) {
            return $this->errorResponse($get['error'], null, $get['code']);
        }
        
        return $this->successResponse($get['result'], 'Property retrieved successfully', 200);
    }

    public function update(StorePropertyRequest $request, $id)
    {
        $request = $request->validated();
        $update = $this->service->update($request, $id);
        if ($update['error']) {
            return $this->errorResponse($update['error'], null, $update['code']);
        }
        
        return $this->successResponse(null, 'Property updated successfully', 200);
    }

    public function destroy($id)
    {
        $destroy = $this->service->destroy($id);
        if ($destroy['error']) {
            return $this->errorResponse($destroy['error'], null, $destroy['code']);
        }
        
        return $this->successResponse(null, 'Property deleted successfully', 200);
    }

    public function createQcItem(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'is_passed' => 'nullable|boolean',
            'evidence' => 'nullable|file|mimetypes:image/jpeg,image/png|max:10800',
            'comment' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $data = $validator->validated();

        $create = $this->service->createQcItem($id, $data);
        if ($create['error']) {
            return $this->errorResponse($create['error'], null, $create['code']);
        }
        
        return $this->successResponse($create['result'], 'Quality control items created successfully', 201);
    }

    public function getQcItems($id)
    {
        $get = $this->service->getQcItems($id);
        if ($get['error']) {
            return $this->errorResponse($get['error'], null, $get['code']);
        }
        
        return $this->successResponse($get['result'], 'Quality control items retrieved successfully', 200);
    }

    public function getQcItem($propertyId, $id)
    {
        $get = $this->service->getQcItem($propertyId, $id);
        if ($get['error']) {
            return $this->errorResponse($get['error'], null, $get['code']);
        }
        
        return $this->successResponse($get['result'], 'Quality control item retrieved successfully', 200);
    }

    public function updateQcItem(Request $request, $propertyId, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'is_passed' => 'nullable|boolean',
            'evidence' => 'nullable|file|mimetypes:image/jpeg,image/png|max:10800',
            'comment' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $data = $validator->validated();

        $update = $this->service->updateQcItem($propertyId, $id, $data);
        if ($update['error']) {
            return $this->errorResponse($update['error'], null, $update['code']);
        }
        
        return $this->successResponse(null, 'Quality control items updated successfully', 200);
    }

    public function deleteQcItem($propertyId, $id)
    {
        $destroy = $this->service->destroyQcItem($propertyId, $id);
        if ($destroy['error']) {
            return $this->errorResponse($destroy['error'], null, $destroy['code']);
        }
        
        return $this->successResponse(null, 'Quality control items deleted successfully', 200);
    }

    public function importQcItems($propertyId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $file = $request->file('file');
        $import = $this->service->importQcItems($propertyId, $file);
        if ($import['error']) {
            return $this->errorResponse($import['error'], null, $import['code']);
        }
        
        return $this->successResponse(null, 'Quality control items imported successfully', 200);
    }
}
