<?php

namespace App\Http\Controllers;

use App\Services\AssetService;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    use ApiResponse;
    public function __construct(protected AssetService $service) {}

    public function listCategory() {
        return $this->successResponse($this->service->listCategory());
    }

    public function listSubCategory($categoryId) {
        $validator = Validator::make(['id' => $categoryId], [
            'id' => 'required|uuid|exists:asset_categories,id'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        return $this->successResponse($this->service->listSubCategory($categoryId));
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|uuid|exists:asset_categories,id',
            'sub_category_id' => 'required|uuid|exists:asset_sub_categories,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'acquisition_date' => 'required|date|before_or_equal:today',
            'useful_life' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $this->service->create($request->all());

        return $this->successResponse(null, 'Asset created successfully', 201);
    }

    public function index(Request $request) {
        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|uuid|exists:asset_categories,id',
            'sub_category_id' => 'nullable|uuid|exists:asset_sub_categories,id',
            'start_date' => 'nullable|date|before_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'search' => 'nullable|string',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $data = $this->service->index($request->all());

        return $this->paginatedResponse($data, 'Asset list');
        
    }

    public function show($id) {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|uuid|exists:assets,id'
        ]); 

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        return $this->successResponse($this->service->show($id));
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|uuid|exists:asset_categories,id',
            'sub_category_id' => 'required|uuid|exists:asset_sub_categories,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'acquisition_date' => 'required|date|before_or_equal:today',
            'useful_life' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $this->service->update($id, $request->all());

        return $this->successResponse(null, 'Asset updated successfully');
    }

    public function delete($id) {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|uuid|exists:assets,id'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $this->service->delete($id);

        return $this->successResponse(null, 'Asset deleted successfully');
    }
}
