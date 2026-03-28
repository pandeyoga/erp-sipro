<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Services\finance\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function __construct(protected ReportService $service) {}

    public function labaRugi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|digits:4',
            'search' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $month = $validator->validated()['month'] ?? Carbon::now()->month;
        $year = $validator->validated()['year'] ?? Carbon::now()->year;

        // bulan jangan lebih dari bulan sekarang dan tahun jangan lebih dari tahun sekarang
        if ($month > Carbon::now()->month || $year > Carbon::now()->year) {
            return $this->errorResponse( 
                "Validation error",
                ['month' => "Bulan tidak boleh lebih dari bulan sekarang dan tahun tidak boleh lebih dari tahun sekarang"],
                422
            );
        }


        $result = $this->service->labaRugi($validator->validated());
        if ($result['error']) {
            return $this->errorResponse(
                $result['error'],
                "Internal server error",
                500
            );
        }

        return $this->successResponse(
            $result['result'],
            "Success",
            $result['status']
        );
    }

    public function cashIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|digits:4',
            'search' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $month = $validator->validated()['month'] ?? Carbon::now()->month;
        $year = $validator->validated()['year'] ?? Carbon::now()->year;

        // bulan jangan lebih dari bulan sekarang dan tahun jangan lebih dari tahun sekarang
        if ($month > Carbon::now()->month || $year > Carbon::now()->year) {
            return $this->errorResponse( 
                "Validation error",
                ['month' => "Bulan tidak boleh lebih dari bulan sekarang dan tahun tidak boleh lebih dari tahun sekarang"],
                422
            );
        }


        $result = $this->service->cashIn($validator->validated());
        if ($result['error']) {
            return $this->errorResponse(
                $result['error'],
                "Internal server error",
                500
            );
        }

        return $this->successResponse(
            $result['result'],
            "Success",
            $result['status']
        );
    }

    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|digits:4',
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $month = $validator->validated()['month'] ?? Carbon::now()->month;
        $year = $validator->validated()['year'] ?? Carbon::now()->year;

        // bulan jangan lebih dari bulan sekarang dan tahun jangan lebih dari tahun sekarang
        if ($month > Carbon::now()->month || $year > Carbon::now()->year) {
            return $this->errorResponse( 
                "Validation error",
                ['month' => "Bulan tidak boleh lebih dari bulan sekarang dan tahun tidak boleh lebih dari tahun sekarang"],
                422
            );
        }
        
        $spreadsheet = $this->service->export($validator->validated());
        if ($spreadsheet['error']) {
            return $this->errorResponse(
                $spreadsheet['error'],
                "Internal server error",
                500
            );
        }

        $spreadsheet = $spreadsheet['result'];

        $month = $validator->validated()['month'] ?? Carbon::now()->month;
        $year = $validator->validated()['year'] ?? Carbon::now()->year;

        // Nama file
        $fileName = "Laporan Laba Rugi " . $month . "-" . $year . ".xlsx";

        // Output langsung ke browser
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save("php://output");
        }, $fileName, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        ]);
    }

    public function exportCashIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|digits:4',
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $month = $validator->validated()['month'] ?? Carbon::now()->month;
        $year = $validator->validated()['year'] ?? Carbon::now()->year;

        // bulan jangan lebih dari bulan sekarang dan tahun jangan lebih dari tahun sekarang
        if ($month > Carbon::now()->month || $year > Carbon::now()->year) {
            return $this->errorResponse( 
                "Validation error",
                ['month' => "Bulan tidak boleh lebih dari bulan sekarang dan tahun tidak boleh lebih dari tahun sekarang"],
                422
            );
        }
        
        $spreadsheet = $this->service->exportCashIn($validator->validated());
        if ($spreadsheet['error']) {
            return $this->errorResponse(
                $spreadsheet['error'],
                "Internal server error",
                500
            );
        }

        $spreadsheet = $spreadsheet['result'];

        $month = $validator->validated()['month'] ?? Carbon::now()->month;
        $year = $validator->validated()['year'] ?? Carbon::now()->year;

        // Nama file
        $fileName = "Laporan Cash In " . $month . "-" . $year . ".xlsx";

        // Output langsung ke browser
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save("php://output");
        }, $fileName, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        ]);
    }

    public function neraca(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|digits:4',
            'search' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $month = $validator->validated()['month'] ?? Carbon::now()->month;
        $year = $validator->validated()['year'] ?? Carbon::now()->year;

        // bulan jangan lebih dari bulan sekarang dan tahun jangan lebih dari tahun sekarang
        if ($month > Carbon::now()->month || $year > Carbon::now()->year) {
            return $this->errorResponse( 
                "Validation error",
                ['month' => "Bulan tidak boleh lebih dari bulan sekarang dan tahun tidak boleh lebih dari tahun sekarang"],
                422
            );
        }

        $result = $this->service->neraca($validator->validated());

        return $this->successResponse(
            $result['result'],
            "Success",
            $result['status']
        );
    }

    public function exportNeraca(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|digits:4',
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }
        
        $spreadsheet = $this->service->exportNeraca($validator->validated());
        if ($spreadsheet['error']) {
            return $this->errorResponse(
                $spreadsheet['error'],
                "Internal server error",
                500
            );
        }

        $month = $validator->validated()['month'] ?? Carbon::now()->month;
        $year = $validator->validated()['year'] ?? Carbon::now()->year;

        // bulan jangan lebih dari bulan sekarang dan tahun jangan lebih dari tahun sekarang
        if ($month > Carbon::now()->month || $year > Carbon::now()->year) {
            return $this->errorResponse( 
                "Validation error",
                ['month' => "Bulan tidak boleh lebih dari bulan sekarang dan tahun tidak boleh lebih dari tahun sekarang"],
                422
            );
        }

        $spreadsheet = $spreadsheet['result'];

        $month = $validator->validated()['month'] ?? Carbon::now()->month;
        $year = $validator->validated()['year'] ?? Carbon::now()->year;

        // Nama file
        $fileName = "Laporan Neraca " . $month . "-" . $year . ".xlsx";

        // Output langsung ke browser
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save("php://output");
        }, $fileName, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        ]);
    }

}
