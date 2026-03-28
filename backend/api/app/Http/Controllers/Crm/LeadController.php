<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\GetLeadRequest;
use App\Http\Requests\Crm\StoreLeadRequest;
use App\Http\Requests\Crm\UpdateLeadRequest;
use App\Services\Crm\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    public function __construct(protected LeadService $service) {}
    
    public function summary()
    {
        $summary = $this->service->getSummary();
        if ($summary['error']) {
            $errorMessage = (string) $summary['error'];
            $errorCode = (int) $summary['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $summary = $summary['result'];

        return $this->successResponse($summary);
    }

    public function index(GetLeadRequest $request)
    {
        $request = $request->validated();
        $all = $this->service->getAll($request);
        if ($all['error']) {
            $errorMessage = (string) $all['error'];
            $errorCode = (int) $all['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $all = $all['result'];

        return $this->paginatedResponse($all, 'Leads fetched successfully');
    }

    public function getAvailablestatus()
    {
        $getStatuses = $this->service->getStatus();
        if ($getStatuses['error']) {
            $errorMessage = (string) $getStatuses['error'];
            $errorCode = (int) $getStatuses['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $getStatuses = $getStatuses['result'];

        return $this->successResponse($getStatuses);
    }

    public function getNonLeadContacts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string', 
        ]);

        if ($validator->fails()) {
            $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $search = $request->search ?? null;

        $getNonLeadContacts = $this->service->getNonLeadContacts($search);
        if ($getNonLeadContacts['error']) {
            $errorMessage = (string) $getNonLeadContacts['error'];
            $errorCode = (int) $getNonLeadContacts['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $getNonLeadContacts = $getNonLeadContacts['result'];

        return $this->successResponse($getNonLeadContacts);
    }

    public function getMarketingAgents()
    {
        $marketingAgents = $this->service->getMarketingAgents();
        if ($marketingAgents['error']) {
            $errorMessage = (string) $marketingAgents['error'];
            $errorCode = (int) $marketingAgents['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $marketingAgents = $marketingAgents['result'];

        return $this->successResponse($marketingAgents);
    }

    public function getSurveyLocation()
    {
        $surveyLocation = $this->service->getSurveyLocation();
        if ($surveyLocation['error']) {
            $errorMessage = (string) $surveyLocation['error'];
            $errorCode = (int) $surveyLocation['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $surveyLocation = $surveyLocation['result'];

        return $this->successResponse($surveyLocation);
    }

    public function getUnitList()
    {
        $unitList = $this->service->getUnitList();
        if ($unitList['error']) {
            $errorMessage = (string) $unitList['error'];
            $errorCode = (int) $unitList['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $unitList = $unitList['result'];

        return $this->successResponse($unitList);
    }

    public function create(StoreLeadRequest $request)
    {
        $request = $request->validated();

        $contactId = $request['contact_id'];

        $hasLead = $this->service->hasLead($contactId);
        if ($hasLead['error']) {
            $errorMessage = (string) $hasLead['error'];
            $errorCode = (int) $hasLead['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $hasLead = $hasLead['result'];

        if ($hasLead) {
            return $this->errorResponse("The Contact is already lead", null, 400);
        }

        $data = $this->service->create($request);
        if ($data['error']) {
            $errorMessage = (string) $data['error'];
            $errorCode = (int) $data['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $data = $data['result'];

        return $this->successResponse($data, 'Lead created successfully', 201);
    }

    public function update(UpdateLeadRequest $request, string $id)
    {
        $request = $request->validated();

        $data = $this->service->update($id, $request);
        if ($data['error']) {
            $errorMessage = (string) $data['error'];
            $errorCode = (int) $data['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $data = $data['result'];

        return $this->successResponse($data, 'Lead updated successfully');
    }

    public function show(string $id)
    {
        $getById = $this->service->getById($id);
        if ($getById['error']) {
            $errorMessage = (string) $getById['error'];
            $errorCode = (int) $getById['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $getById = $getById['result'];
        
        return $this->successResponse($getById, 'Lead fetched successfully');
    }

    public function delete(string $id)
    {
        $delete = $this->service->delete($id);
        if ($delete['error']) {
            $errorMessage = (string) $delete['error'];
            $errorCode = (int) $delete['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $delete = $delete['result'];

        return $this->successResponse($delete, 'Lead deleted successfully');
    }
}
