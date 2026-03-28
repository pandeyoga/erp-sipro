<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Services\finance\BankAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BankAccountController extends Controller
{
    public function __construct(protected BankAccountService $service) {}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:bank_accounts',
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'opening_balance' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $data = $this->service->createBankAccount($request->all());
        if ($data['error']) {
            return $this->errorResponse($data['error'], $data['result'], $data['code']);
        }

        return $this->successResponse($data['result'], "Bank account created successfully");
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|numeric|min:1',
            'page' => 'nullable|numeric|min:1',
            'search' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $data = $this->service->getBankAccounts($request->all());

        return $this->paginatedResponse($data, "Bank accounts fetched successfully");
    }

    public function show($id)
    {
        $data = $this->service->getBankAccount($id);
        if ($data['error']) {
            return $this->errorResponse($data['error'], $data['result'], $data['code']);
        }
        return $this->successResponse($data['result'], "Bank account fetched successfully");
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:bank_accounts,code,' . $id,
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'opening_balance' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $data = $this->service->update($validator->validated(), $id);
        if ($data['error']) {
            return $this->errorResponse($data['error'], $data['result'], $data['code']);
        }
        return $this->successResponse($data['result'], "Bank account updated successfully");
    }

    public function delete($id)
    {
        $data = $this->service->delete($id);
        if ($data['error']) {
            return $this->errorResponse($data['error'], $data['result'], $data['code']);
        }
        return $this->successResponse(null, "Bank account deleted successfully");
    }

    public function getTransaction($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|numeric|min:1',
            'page' => 'nullable|numeric|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $data = $this->service->detailTransaction($id, $validator->validated());
        if ($data['error']) {
            return $this->errorResponse($data['error'], $data['result'], $data['code']);
        }
        return $this->paginatedResponse($data['result'], "Bank account detail transaction fetched successfully");
    }

    public function transferSaldo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_bank_account_id' => 'required|exists:bank_accounts,id|different:to_bank_account_id',
            'to_bank_account_id'   => 'required|exists:bank_accounts,id|different:from_bank_account_id',
            'amount'               => 'required|numeric',
            'transfer_fee'         => 'nullable|numeric',
            'note'                 => 'nullable|string|max:255',
        ]);


        if ($validator->fails()) {
            return $this->errorResponse(
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $data = $this->service->transferSaldo($validator->validated());
        if ($data['error']) {
            return $this->errorResponse($data['error'], $data['result'], $data['code']);
        }
        return $this->successResponse($data['result'], "Bank account transfer saldo successfully");
    }

    public function listTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|numeric|min:1',
            'page' => 'nullable|numeric|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $data = $this->service->listTransfer($validator->validated());
        if ($data['error']) {
            return $this->errorResponse($data['error'], $data['result'], $data['code']);
        }
        return $this->paginatedResponse($data['result'], "Bank account list transfer fetched successfully");
    }
}
