<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\GetLeadDocumentRequest;
use App\Http\Requests\Crm\StoreLeadDocumentRequest;
use App\Http\Requests\Crm\UpdateLeadDocumentRequest;
use App\Services\Crm\LeadDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LeadDocumentController extends Controller
{
    public function __construct(
        protected LeadDocumentService $service,
    ) {}
    
    public function summary()
    {
        $summary = $this->service->getSummary();
        if ($summary['error']) {
            return $this->errorResponse($summary['error'], null, $summary['code']);
        }
        $summary = $summary['result'];

        return $this->successResponse($summary, 'Leads Documents summary fetched successfully');
    }

    public function index(GetLeadDocumentRequest $request)
    {
        $request = $request->validated();

        $all = $this->service->getAll($request);
        if ($all['error']) {
            return $this->errorResponse($all['error'], null, $all['code']);
        }
        $all = $all['result'];

        return $this->paginatedResponse($all, 'Leads Documents fetched successfully');
    }

    public function buyerDocumentTypes()
    {
        return $this->successResponse(config('setting.buyer_document_types'));
    }

    public function create(StoreLeadDocumentRequest $request)
    {
        $request = $request->validated();

        $documents = [];
        $checklist = [];
        $fill = [];
        foreach ($request as $key => $value) {
            if (Str::startsWith($key, 'doc_')) {
                $key = str_replace('doc_', '', $key);
                $documents[$key] = $value;
            } elseif (Str::startsWith($key, 'pekerja_') || Str::startsWith($key, 'wirausaha_') || $key == 'check_cash') {
                $checklist[$key] = $value;
            } else {
                $fill[$key] = $value;
            }
        }

        $success = $this->service->create($fill, $documents, $checklist);
        if ($success['error']) {
            return $this->errorResponse($success['error'], null, $success['code']);
        }
        
        if (!$success) {
            return $this->errorResponse('Error when create lead document', null, 400);
        }

        return $this->successResponse(null, 'Lead Documents uploaded successfully', 201);
    }

    public function update(UpdateLeadDocumentRequest $request, string $id)
    {
        $request = $request->validated();

        $documents = [];
        $checklist = [];
        $fill = [];
        foreach ($request as $key => $value) {
            if (Str::startsWith($key, 'doc_')) {
                $key = str_replace('doc_', '', $key);
                $documents[$key] = $value;
            } elseif (Str::startsWith($key, 'pekerja_') || Str::startsWith($key, 'wirausaha_' || $key == 'check_cash')) {
                $checklist[$key] = $value;
            } else {
                $fill[$key] = $value;
            }
        }

        $update = $this->service->update($id, $fill, $documents, $checklist);
        if ($update['error']) {
            return $this->errorResponse($update['error'], null, $update['code']);
        }

        return $this->successResponse(null, 'Lead Document updated successfully');
    }

    public function show(string $id)
    {
        $getById = $this->service->getById($id);
        if ($getById['error']) {
            return $this->errorResponse($getById['error'], null, $getById['code']);
        }
        $getById = $getById['result'];
        
        return $this->successResponse($getById, 'Lead Document fetched successfully');
    }

    public function getReservedLeads(Request $request)
    {
        $request = Validator::make($request->all(), [
            'search' => 'nullable|string'
        ]);

        if ($request->fails()) {
            return $this->errorResponse("Validation error", $request->errors(), 400);
        }
        $search = isset($request->validated()['search']) ? $request->validated()['search'] : null;

        $reservedLeads = $this->service->reservedLeads($search);
        if ($reservedLeads['error']) {
            return $this->errorResponse($reservedLeads['error'], null, $reservedLeads['code']);
        }
        $reservedLeads = $reservedLeads['result'];

        return $this->successResponse($reservedLeads, 'Leads fetched successfully');
    }

    public function updateStatusDocument(Request $request, string $documentId)
    {
        $documentTypes = collect(config('setting.buyer_document_types'))->pluck('code')->toArray();
        $request = Validator::make($request->all(), [
            'type' => 'required|string|in:' . implode(',', $documentTypes),
            'status' => 'required|string|in:verified,unverified'
        ]);
        
        if ($request->fails()) {
            return $this->errorResponse("Validation error", $request->errors(), 400);
        }
        
        $status = $request->validated()['status'];
        $type = $request->validated()['type'];

        $update = $this->service->updateStatusDocument($documentId, $type, $status);
        if ($update['error']) {
            return $this->errorResponse($update['error'], null, $update['code']);
        }

        return $this->successResponse(null, 'Lead Document status updated successfully');
    }

    public function delete(string $id)
    {
        $delete = $this->service->delete($id);
        if ($delete['error']) {
            return $this->errorResponse($delete['error'], null, $delete['code']);
        }
        $delete = $delete['result'];

        return $this->successResponse($delete, 'Lead Document deleted successfully');
    }
}
