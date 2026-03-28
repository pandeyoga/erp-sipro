<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Services\Crm\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $service) {}

    public function pendingTasks()
    {
        return $this->successResponse($this->service->getAllPendingTaskUser()['result'], 'Success Get All Pending Task User', 200);
    }

    public function newLead()
    {
        return $this->successResponse($this->service->getAllNewLead()['result'], 'Success Get All New Lead', 200);
    }

    public function getMarketingPerformance()
    {
        return $this->successResponse($this->service->marketingPerformace()['result'], 'Success Get Marketing Performance', 200);
    }

    public function summaryStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'when' => 'nullable|in:today,last_week,last_month,last_year'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $when = $request->when ?? 'today';
        
        return $this->successResponse($this->service->summaryStatus($when)['result'], 'Success Get Summary Status', 200);
    }

    public function summarySource(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'when' => 'nullable|in:today,last_week,last_month,last_year'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $when = $request->when ?? 'today';
        
        return $this->successResponse($this->service->summarySource($when)['result'], 'Success Get Summary Status', 200);
    }

    public function summaryChanged(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'when' => 'nullable|in:today,last_week,last_month,last_year'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $when = $request->when ?? 'today';
        
        return $this->successResponse($this->service->summaryChanged($when)['result'], 'Success Get Summary Status', 200);
    }

    public function leadFunnel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'nullable|in:1,2,3,4,5,6,7,8,9,10,11,12',
            'year' => 'nullable'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');
        
        return $this->successResponse($this->service->leadFunnel($month, $year)['result'], 'Success Get Leaf Funnel', 200);
    }

    public function taskPerformance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'nullable'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $year = $request->year ?? date('Y');
        
        return $this->successResponse($this->service->taskPerformance($year)['result'], 'Success Get Task Performance', 200);
    }
    
}
