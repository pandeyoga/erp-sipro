<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StoreRetentionCase;
use App\Http\Requests\Property\StoreUnitRequest;
use App\Http\Requests\Property\UpdateRetentionCase;
use App\Services\Property\RetentionCaseService;
use App\Services\Property\UnitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RetentionCaseController extends Controller
{
    public function __construct(protected RetentionCaseService $service) {}

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:open,in_progress,resolved',
            'sub_contractor_id' => 'nullable|integer|exists:sub_contractors,id',
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

        return $this->paginatedResponse($data, 'Retention Cases retrieved successfully', 200);
    }

    public function summary()
    {
        $summary = $this->service->summary();
        if ($summary['error']) {
            return $this->errorResponse($summary['error'], null, $summary['code']);
        }

        return $this->successResponse($summary['result'], 'Retention Cases retrieved successfully', 200);
    }

    public function getReservedLead()
    {
        $getReservedLead = $this->service->getReservedLead();
        if ($getReservedLead['error']) {
            return $this->errorResponse($getReservedLead['error'], null, $getReservedLead['code']);
        }

        return $this->successResponse($getReservedLead['result'], 'Lead retrieved successfully', 200);
    }

    public function getAvailableSubCon()
    {
        $getAvailableSubCon = $this->service->getAvailableSubCon();
        if ($getAvailableSubCon['error']) {
            return $this->errorResponse($getAvailableSubCon['error'], null, $getAvailableSubCon['code']);
        }

        return $this->successResponse($getAvailableSubCon['result'], 'Sub Contractor retrieved successfully', 200);
    }

    public function store(StoreRetentionCase $request)
    {
        $request = $request->validated();
        $store = $this->service->store($request);
        if ($store['error']) {
            return $this->errorResponse($store['error'], null, $store['code']);
        }

        return $this->successResponse(null, 'Retention Case created successfully', 201);
    }

    public function getById($id)
    {
        $getById = $this->service->getById($id);
        if ($getById['error']) {
            return $this->errorResponse($getById['error'], null, $getById['code']);
        }

        return $this->successResponse($getById['result'], 'Retention Case retrieved successfully', 200);
    }

    public function update(UpdateRetentionCase $request, $id)
    {
        $request = $request->validated();

        $update = $this->service->update($request, $id);
        if ($update['error']) {
            return $this->errorResponse($update['error'], null, $update['code']);
        }

        return $this->successResponse(null, 'Retention Case updated successfully', 200);
    }

    public function destroy($id)
    {
        $destroy = $this->service->destroy($id);
        if ($destroy['error']) {
            return $this->errorResponse($destroy['error'], null, $destroy['code']);
        }

        return $this->successResponse(null, 'Retention Case deleted successfully', 200);
    }

}
