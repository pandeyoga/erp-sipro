<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Services\Crm\ContactService;
use App\Services\finance\CashInService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CashInController extends Controller
{
    public function __construct(protected CashInService $service) {}

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

    public function subSubCategories($subCategoryId)
    {
        $subSubCategories = $this->service->subSubCategories($subCategoryId);
        return $this->successResponse($subSubCategories['result'], 'Sub Sub Categories retrieved successfully');
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

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'nullable|exists:unit_properties,id',
            'category_id' => 'required|uuid|exists:cash_in_categories,id',
            'sub_category_id' => 'required|uuid|exists:cash_in_sub_categories,id',
            'total_amount' => 'required|integer|min:1000',
            'description' => 'required|string|max:255',
            'bank_account_id' => 'required|uuid|exists:bank_accounts,id',
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
        
        $result = $this->service->create($validated);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Cash In created successfully', 201);
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10',
            'search' => 'nullable|string',
            'status' => 'nullable|string|in:belum-lunas,lunas',
            'category_id' => 'nullable|uuid|exists:cash_in_categories,id',
            'sub_category_id' => 'nullable|uuid|exists:cash_in_sub_categories,id',
            'sortKey' => 'nullable|in:created_at,total_amount,paid_amount',
            'sortDir' => 'nullable|in:asc,desc',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $cashIns = $this->service->getAll($validator->validated());
        return $this->paginatedResponse($cashIns['result'], 'Cash In retrieved successfully');
    }

    public function show($id)
    {
        $cashIn = $this->service->getById($id);
        if ($cashIn['error']) {
            return $this->errorResponse($cashIn['error'], $cashIn['result'], $cashIn['status']);
        }

        return $this->successResponse($cashIn['result'], 'Cash In retrieved successfully');
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
        $fileName = "Laporan Cash IN " . $startDate->format('d-m-Y') . " s.d " . $endDate->format('d-m-Y') . ".xlsx";

        // ✅ Output ke browser
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save("php://output");
        }, $fileName, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        ]);
    }


    public function delete($id)
    {
        $result = $this->service->delete($id);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Cash In deleted successfully');
    }

    public function deleteTransaction($id)
    {
        $result = $this->service->deleteTransaction($id);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Transaction deleted successfully');
    }

    public function getPropertyList()
    {
        $propertyList = $this->service->getPropertyList();
        return $this->successResponse($propertyList['result'], 'Property list retrieved successfully');
    }

    public function createTransaction(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'cash_in_id' => 'required|uuid|exists:cash_flow_ins,id',
            'total_amount' => 'required|integer|min:1000',
            'amount' => 'nullable|integer|min:1000',
            'bank_account_id' => 'nullable|uuid|exists:bank_accounts,id',
            'date' => 'nullable|date',
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
        $validated['parent_id'] = $id;
        $validated['amount'] = $validated['amount'] ?? 0;
        
        $result = $this->service->createTransaction($validated);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Action performed successfully', 201);
    }

    public function getTransaction($parentId)
    {
        $transactions = $this->service->getTransactionByParentId($parentId);
        if ($transactions['error']) {
            return $this->errorResponse($transactions['error'], $transactions['result'], $transactions['status']);
        }
        return $this->paginatedResponse($transactions['result'], 'Transactions retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'total_amount' => 'required|integer|min:1000',
            'description' => 'required|string|max:255',
            'bank_account_id' => 'uuid|exists:bank_accounts,id',
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
        return $this->successResponse($result['result'], 'Cash In updated successfully');
    }
}
