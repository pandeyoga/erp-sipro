<?php
        
namespace App\Services\Crm;

use App\Repositories\Crm\ContactRepository;
use App\Repositories\Crm\DashboardRepository;
use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Str;

class DashboardService
{
    public function __construct(protected DashboardRepository $repository) {}

    public function getAllPendingTaskUser() : array
    {
        $leadPending = $this->repository->getAllPendingTaskUser();
        $paymentPending = $this->repository->getAllPendingTaskPaymentLeadByUser();

        $leadPending['result'] = $leadPending['result']->map(function ($item) {
            $status = [
                'new' => 'Follow up lead agar menjadi Prospect',
                'prospect' => 'Follow up lead agar melakukan Booking',
                'reserve' => 'lengkapi dokumen Lead',
                'document_and_legal_process' => 'Follow up lead agar melanjutkan ke proses KPR/Cash',
            ];
            $task = Str::ucfirst($status[$item->status]);
            return [
                'lead_id' => $item->id,
                'lead_name' => $item->contact->name,
                'task' => $task,
                'source' => $item->contact->source,
                'status' => $item->status,
                'phone' => $item->contact->phone,
                'due_date' => date('Y-m-d', strtotime($item->due_date)),
                'is_late' => $item->is_late == 1 ? true : false,
                'remaining_days' => (int) ceil(abs(Carbon::parse($item->due_date)->diffInDays(now(), false))),
                'agent' => $item->assignTo?->name,
                'created_at' => Carbon::parse($item->created_at),
            ];
        });

        $paymentPending['result'] = $paymentPending['result']->map(function ($item) {
            $status = [
                'proses_bank' => 'Menunggu sp3k',
                'sp3k' => 'Menunggu akad kredit',
                'cash' => 'Lengkapi dokumen legalitas akhir Lead',
                'akad_kredit' => 'Lengkapi dokumen legalitas akhir Lead',
            ];
            $task = Str::ucfirst($status[$item->status]);
            return [
                'lead_id' => $item->id,
                'lead_name' => $item->contact->name,
                'task' => $task,
                'source' => $item->contact->source,
                'status' => $item->status,
                'phone' => $item->contact->phone,
                'due_date' => date('Y-m-d', strtotime($item->due_date)),
                'is_late' => $item->is_late == 1 ? true : false,
                'remaining_days' => (int) ceil(abs(Carbon::parse($item->due_date)->diffInDays(now(), false))),
                'agent' => $item->assignTo?->name,
                'created_at' => Carbon::parse($item->created_at),
            ];
        });

        $concatedResult = collect(array_merge(
            $leadPending['result']->toArray(),
            $paymentPending['result']->toArray()
        ))->sortByDesc('created_at');

        $data = [
            'error' => null,
            'code' => 200,
            'result' => $concatedResult,
        ];

        return $data;
    }

    public function getAllNewLead() : array
    {
        $data = $this->repository->getAllNewLead();

        $data['result'] = $data['result']->map(function ($item) {
            return [
                'lead_id' => $item->id,
                'lead_name' => $item->contact->name,
                'source' => $item->contact->source,
                'status' => $item->status,
                'phone' => $item->contact->phone,
                'due_date' => date('Y-m-d', strtotime($item->due_date)),
                'agent' => $item->assignTo?->name,
                'created_at' => $item->created_at,
            ];
        });

        return $data;
    }

    public function marketingPerformace() : array
    {
        $data = $this->repository->getMarketingPerformace();
        return $data;
    }

    public function summaryStatus($when) : array
    {
        $data = $this->repository->summaryStatus($when);
        return $data;
    }

    public function summarySource($when) : array
    {
        $data = $this->repository->summarySource($when);
        return $data;
    }

    public function summaryChanged($when) : array
    {
        $data = $this->repository->summaryChanged($when);
        return $data;
    }

    public function leadFunnel($month, $year) : array
    {
        $data = $this->repository->leadFunnel($month, $year);

        return $data;
    }

    public function taskPerformance($year) : array
    {
        $data = $this->repository->taskPerformance($year);
        return $data;
    }
}