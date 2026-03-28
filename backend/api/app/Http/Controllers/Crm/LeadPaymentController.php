<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\GetLeadPaymentRequest;
use App\Http\Requests\Crm\StoreLeadPaymentRequest;
use App\Http\Requests\Crm\UpdateLeadPaymentRequest;
use App\Services\Crm\LeadPaymentService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class LeadPaymentController extends Controller
{
    // TODO = Auto Bikin Cash In sesuai dengan data di reservasi kalo ada penambahan dll (lakukan saat create)
    public function __construct(
        protected LeadPaymentService $service,
    ) {}

    // get list lead document
    public function getLeadCompletedDocument()
    {
        $data = $this->service->getLeadCompletedDocument();
        if ($data['error']) {
            return $this->errorResponse($data['error'], null, $data['code']);
        }
        $data = $data['result'];

        return $this->successResponse($data, 'Leads Payments document fetched successfully');
    }

    public function summary()
    {
        $summary = $this->service->getSummary();
        if ($summary['error']) {
            return $this->errorResponse($summary['error'], null, $summary['code']);
        }
        $summary = $summary['result'];

        return $this->successResponse($summary, 'Leads Payments summary fetched successfully');
    }

    public function bankList()
    {
        $bankList = $this->service->getBankList();
        if ($bankList['error']) {
            return $this->errorResponse($bankList['error'], null, $bankList['code']);
        }
        $bankList = $bankList['result'];

        return $this->successResponse($bankList, 'Bank list fetched successfully');
    }

    public function create(StoreLeadPaymentRequest $request)
    {
        $create = $this->service->create($request->validated());
        if ($create['error']) {
            return $this->errorResponse($create['error'], null, $create['code']);
        }

        return $this->successResponse(null, "Success Create Lead Payment", 201);
    }

    public function index(GetLeadPaymentRequest $request)
    {
        $data = $this->service->getAll($request->validated());
        if ($data['error']) {
            return $this->errorResponse($data['error'], null, $data['code']);
        }
        $data = $data['result'];

        return $this->paginatedResponse($data, "Success Get All Lead Payment");
    }

    public function update(UpdateLeadPaymentRequest $request, $id)
    {
        $update = $this->service->update($request->validated(), $id);
        if ($update['error']) {
            return $this->errorResponse($update['error'], null, $update['code']);
        }

        return $this->successResponse(null, "Success Update Lead Payment");
    }

    public function getById($id)
    {
        $data = $this->service->getById($id);
        if ($data['error']) {
            return $this->errorResponse($data['error'], null, $data['code']);
        }
        $data = $data['result'];

        return $this->successResponse($data, "Success Get Lead Payment By Id");
    }

    public function delete($id)
    {
        $delete = $this->service->delete($id);
        if ($delete['error']) {
            return $this->errorResponse($delete['error'], null, $delete['code']);
        }

        return $this->successResponse(null, "Success Delete Lead Payment");
    }
}
