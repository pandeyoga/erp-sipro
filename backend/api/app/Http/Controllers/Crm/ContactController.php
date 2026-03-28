<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\GetContactRequest;
use App\Http\Requests\Crm\ImportContactRequest;
use App\Http\Requests\Crm\StoreContactRequest;
use App\Services\Crm\ContactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ContactController extends Controller
{
    public function __construct(protected ContactService $service) {}

    public function index(GetContactRequest $request)
    {
        $request = $request->validated();
        $query = [
            'search' => $request['search'] ?? null,
            'page' => $request['page'] ?? 1,
            'per_page' => $request['per_page'] ?? 20
        ];

        $contacts = $this->service->getAllContacts($query);
        if ($contacts['error']) {
            $errorMessage = (string) $contacts['error'];
            $errorCode = (int) $contacts['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $contacts = $contacts['result'];

        return $this->paginatedResponse($contacts, 'Contacts fetched successfully');
    }

    // import excel
    public function import(ImportContactRequest $request)
    {
        $import = $this->service->importContacts($request->file('file'));
        if ($import['error']) {
            $errorMessage = (string) $import['error'];
            $errorCode = (int) $import['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $import = $import['result'];

        return $this->successResponse($import, 'Contacts imported successfully', 201);
    }

    public function getAllContactForSelect(Request $request)
    {
        $valiadtor = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255'
        ]);

        if ($valiadtor->fails()) {
            return $this->errorResponse(
                "Validation error", 
                $valiadtor->errors(), 
                400
            );
        }

        $search = $request->input('search') ?? null;

        $select = $this->service->getAllContactForSelect($search);
        if ($select['error']) {
            $errorMessage = (string) $select['error'];
            $errorCode = (int) $select['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $select = $select['result'];

        return $this->successResponse($select);
    }

    public function export(Request $request)
    {
        // ✅ Validasi input tanggal
        $validator = Validator::make($request->all(), [
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $validated = $validator->validated();

        $startDate = Carbon::parse($validated['startDate'])->startOfDay();
        $endDate = Carbon::parse($validated['endDate'])->endOfDay();

        // ✅ Kirim ke service export
        $spreadsheet = $this->service->export($startDate, $endDate);

        if ($spreadsheet['error']) {
            return $this->errorResponse(
                $spreadsheet['error'],
                "Internal server error",
                500
            );
        }

        $spreadsheet = $spreadsheet['result'];

        // ✅ Format nama file
        $fileName = "Contacts" . $startDate->format('d-m-Y') . " s.d " . $endDate->format('d-m-Y') . ".xlsx";

        // ✅ Output ke browser
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save("php://output");
        }, $fileName, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        ]);
    }

    public function create(StoreContactRequest $request)
    {
        $data = $request->validated();

        $create = $this->service->createContact($data);
        if ($create['error']) {
            $errorMessage = (string) $create['error'];
            $errorCode = (int) $create['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $create = $create['result'];

        return $this->successResponse($create, 'Contact created successfully', 201);
    }

    public function show($id)
    {
        $getContact = $this->service->getContact($id);
        if ($getContact['error']) {
            $errorMessage = (string) $getContact['error'];
            $errorCode = (int) $getContact['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $getContact = $getContact['result'];

        return $this->successResponse($getContact, 'Contact fetched successfully');
    }

    public function update(StoreContactRequest $request, string $id)
    {
        $requestData = $request->validated();
        $updateContact = $this->service->updateContact($id, $requestData);
        if ($updateContact['error']) {
            $errorMessage = (string) $updateContact['error'];
            $errorCode = (int) $updateContact['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $updateContact = $updateContact['result'];

        return $this->successResponse($updateContact, 'Contact updated successfully');
    }

    public function destroy(string $id)
    {
        $contact = $this->service->isLeads($id);
        if ($contact['error']) {
            $errorMessage = (string) $contact['error'];
            $errorCode = (int) $contact['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }
        $contact = $contact['result'];

        if ($contact) {
            return $this->errorResponse("Can't delete this contact, because it's leads", null, 400);
        }

        $delete = $this->service->deleteContact($id);
        if ($delete['error']) {
            $errorMessage = (string) $delete['error'];
            $errorCode = (int) $delete['code'];
            return $this->errorResponse($errorMessage, null, $errorCode);
        }

        return $this->successResponse(null, 'Contact deleted successfully');
    }
}
