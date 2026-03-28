<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\GetLeadFinalLegalityRequest;
use App\Http\Requests\Crm\StoreFinalLegalityRequest;
use App\Http\Requests\Crm\UpdateFinalLegalityRequest;
use App\Services\Crm\FinalLegalityService;
use Illuminate\Http\Request;

class FinalLegalityController extends Controller
{
    public function __construct(protected FinalLegalityService $service) {}

    public function summary()
    {
        $finalLegalities = $this->service->summary();
        if ($finalLegalities['error']) {
            return $this->errorResponse($finalLegalities['error'], null, $finalLegalities['code']);
        }
        $finalLegalities = $finalLegalities['result'];
        return $this->successResponse($finalLegalities, "Success Get Final Legalities", 200);
    }

    public function index(GetLeadFinalLegalityRequest $request)
    {
        $finalLegalities = $this->service->index($request->validated());
        if ($finalLegalities['error']) {
            return $this->errorResponse($finalLegalities['error'], null, $finalLegalities['code']);
        }
        $finalLegalities = $finalLegalities['result'];
        return $this->paginatedResponse($finalLegalities, "Success Get Final Legalities", 200);
    }

    public function create(StoreFinalLegalityRequest $request)
    {
        $create = $this->service->create($request->validated());
        if ($create['error']) {
            return $this->errorResponse($create['error'], null, $create['code']);
        }

        return $this->successResponse(null, "Success Create Final Legality", 201);
    }

    public function update(UpdateFinalLegalityRequest $request, $id)
    {
        $update = $this->service->update($request->validated(), $id);
        if ($update['error']) {
            return $this->errorResponse($update['error'], null, $update['code']);
        }

        return $this->successResponse(null, "Success Update Final Legality", 200);
    }

    public function getById($id)
    {
        $finalLegality = $this->service->getById($id);
        if ($finalLegality['error']) {
            return $this->errorResponse($finalLegality['error'], null, $finalLegality['code']);
        }
        $finalLegality = $finalLegality['result'];
        return $this->successResponse($finalLegality, "Success Get Final Legality", 200);
    }

    public function getLeadCompletedPayment()
    {
        $finalLegality = $this->service->getLeadCompletedPayment();
        if ($finalLegality['error']) {
            return $this->errorResponse($finalLegality['error'], null, $finalLegality['code']);
        }
        $finalLegality = $finalLegality['result'];
        return $this->successResponse($finalLegality, "Success Get Final Legality", 200);
    }

    public function delete($id)
    {
        $delete = $this->service->delete($id);
        if ($delete['error']) {
            return $this->errorResponse($delete['error'], null, $delete['code']);
        }

        return $this->successResponse(null, "Success Delete Final Legality");
    }
}
