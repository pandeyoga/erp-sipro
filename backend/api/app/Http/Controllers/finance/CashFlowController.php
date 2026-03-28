<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Services\finance\CashFlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashFlowController extends Controller
{
    public function __construct(protected CashFlowService $service) {}

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date|date_format:Y-m-d|before_or_equal:end_date',
            'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
            'page' => 'nullable',
            "per_page" => 'nullable',
            "search" => 'nullable',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $result = $this->service->getAll($validator->validated());

        return $this->paginatedResponse(
            $result['result'],
            "Success",
            $result['status']
        );
    }

    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|date_format:Y-m-d|before_or_equal:end_date',
            'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $result = $this->service->export($validator->validated());

        return $result;
    }
}
