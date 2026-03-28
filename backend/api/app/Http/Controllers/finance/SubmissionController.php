<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Services\finance\SubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    public function __construct(protected SubmissionService $service) {}

    public function categories()
    {
        $categories = $this->service->categories();
        return $this->successResponse($categories['result'], 'Categories retrieved successfully');
    }

    public function subCategories($categoryId)
    {
        $subCategories = $this->service->subCategories($categoryId);
        return $this->successResponse($subCategories['result'], 'Sub Categories retrieved successfully');
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:submission,reimbursement',
            'category_id' => 'required|uuid|exists:cash_out_categories,id',
            'sub_category_id' => 'required|uuid|exists:cash_out_sub_categories,id',
            'amount' => 'required|integer|min:1000',
            'description' => 'required|string|max:255',
            'notes' => 'nullable',
            'file_proof' => 'nullable|mimes:jpg,png,jpeg,pdf|max:10800'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $validated = $validator->validated();
        
        $result = $this->service->create($validated);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Submission created successfully', 201);
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10',
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $submissions = $this->service->getAll($validator->validated());
        return $this->paginatedResponse($submissions['result'], 'Submission retrieved successfully');
    }

    public function show($id)
    {
        $cashIn = $this->service->getById($id);
        if ($cashIn['error']) {
            return $this->errorResponse($cashIn['error'], $cashIn['result'], $cashIn['status']);
        }

        return $this->successResponse($cashIn['result'], 'Submission retrieved successfully');
    }

    public function delete($id)
    {
        $result = $this->service->delete($id);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Submission deleted successfully');
    }

    public function approve($id)
    {
        $result = $this->service->approve($id);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Transaction approved successfully');
    }

    public function reject($id)
    {
        $result = $this->service->reject($id);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Transaction rejected successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1000',
            'description' => 'required|string|max:255',
            'notes' => 'nullable'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $validated = $validator->validated();
        
        $result = $this->service->update($id, $validated);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Submission updated successfully');
    }
}
