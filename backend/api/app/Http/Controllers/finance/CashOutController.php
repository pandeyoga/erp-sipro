<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Services\Crm\ContactService;
use App\Services\finance\CashOutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CashOutController extends Controller
{
    public function __construct(protected CashOutService $service) {}

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
            'category_id' => 'required|uuid|exists:cash_out_categories,id',
            'sub_category_id' => 'required|uuid|exists:cash_out_sub_categories,id',
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
        return $this->successResponse($result['result'], 'Cash Out created successfully', 201);
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10',
            'search' => 'nullable|string',
            'category_id' => 'nullable|uuid|exists:cash_out_categories,id',
            'sub_category_id' => 'nullable|uuid|exists:cash_out_sub_categories,id',
            'status' => 'nullable|in:lunas,belum-lunas',
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
        return $this->paginatedResponse($cashIns['result'], 'Cash Out retrieved successfully');
    }

    public function show($id)
    {
        $cashIn = $this->service->getById($id);
        if ($cashIn['error']) {
            return $this->errorResponse($cashIn['error'], $cashIn['result'], $cashIn['status']);
        }

        return $this->successResponse($cashIn['result'], 'Cash Out retrieved successfully');
    }

    public function delete($id)
    {
        $result = $this->service->delete($id);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Cash Out deleted successfully');
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
        $fileName = "Laporan Cash Out " . $startDate->format('d-m-Y') . " s.d " . $endDate->format('d-m-Y') . ".xlsx";

        // ✅ Output ke browser
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save("php://output");
        }, $fileName, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        ]);
    }

    public function deleteTransaction($id)
    {
        $result = $this->service->deleteTransaction($id);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Transaction deleted successfully');
    }

    public function createTransaction(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1000',
            'date' => 'nullable|date',
            'bank_account_id' => 'nullable|uuid|exists:bank_accounts,id',
            'notes' => 'nullable'
        ]);

        $idValidator = Validator::make(['id' => $id], [
            'id' => 'required|uuid|exists:cash_flow_outs,id'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }
        if ($idValidator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $idValidator->errors(),
                422
            );
        }

        $validated = $validator->validated();
        $validated['amount'] = $validated['amount'] ?? 0;
        $validated['cash_out_id'] = $id;
        
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
        
        $result = $this->service->update($id, $validated);
        if ($result['error']) {
            return $this->errorResponse($result['error'], $result['result'], $result['status']);
        }
        return $this->successResponse($result['result'], 'Cash Out updated successfully');
    }
}
