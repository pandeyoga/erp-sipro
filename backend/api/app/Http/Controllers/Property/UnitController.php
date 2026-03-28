<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StoreUnitRequest;
use App\Services\Property\UnitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function __construct(protected UnitService $service) {}

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
            'search' => 'nullable|string|max:255',
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

        return $this->paginatedResponse($data, 'Unit retrieved successfully', 200);
    }

    public function store(StoreUnitRequest $request)
    {
        $request = $request->validated();
        $store = $this->service->store($request);
        if ($store['error']) {
            return $this->errorResponse($store['error'], null, $store['code']);
        }

        return $this->successResponse(null, 'Unit created successfully', 201);
    }

    public function getById($id)
    {
        $getById = $this->service->getById($id);
        if ($getById['error']) {
            return $this->errorResponse($getById['error'], null, $getById['code']);
        }

        return $this->successResponse($getById['result'], 'Unit retrieved successfully', 200);
    }

    public function update(StoreUnitRequest $request, $id)
    {
        $request = $request->validated();

        $update = $this->service->update($request, $id);
        if ($update['error']) {
            return $this->errorResponse($update['error'], null, $update['code']);
        }

        return $this->successResponse(null, 'Unit updated successfully', 200);
    }

    public function destroy($id)
    {
        $destroy = $this->service->destroy($id);
        if ($destroy['error']) {
            return $this->errorResponse($destroy['error'], null, $destroy['code']);
        }

        return $this->successResponse(null, 'Unit deleted successfully', 200);
    }

}
