<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Services\finance\DebtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DebtController extends Controller
{
    public function __construct(protected DebtService $service) {}

    public function categories()
    {
        $categories = $this->service->categories();
        return $this->successResponse($categories['result'], 'Categories retrieved successfully');
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|uuid',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'bank_account_id' => 'required|uuid|exists:bank_accounts,id',
            'amount' => 'required|integer|min:1000',
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
        return $this->successResponse($result['result'], 'Loan created successfully', 201);
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1',
            'search' => 'nullable|string',
            'status' => 'nullable|in:lunas,belum_lunas',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $submissions = $this->service->getAll();
        return $this->paginatedResponse($submissions['result'], 'Loan retrieved successfully');
    }

    public function show($id)
    {
        $cashIn = $this->service->getById($id);
        if ($cashIn['error']) {
            return $this->errorResponse($cashIn['error'], $cashIn['result'], $cashIn['status']);
        }

        return $this->successResponse($cashIn['result'], 'Loan retrieved successfully');
    }

    public function delete($id)
    {
        $result = $this->service->delete($id);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Submission deleted successfully');
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|uuid',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'bank_account_id' => 'required|uuid|exists:bank_accounts,id',
            'amount' => 'required|integer|min:1000',
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

    public function payment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1000',
            'bank_account_id' => 'required|uuid|exists:bank_accounts,id',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $result = $this->service->payment($validator->validated(), $id);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Submission updated successfully');
    }
}
