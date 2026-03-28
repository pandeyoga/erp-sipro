<?php
        
namespace App\Repositories\Crm;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\User;
use App\Models\LeadHistory;
use App\Models\MarketingTask;
use App\Models\Survey;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function getAllPendingTaskUser() : array
    {
        $allowedStatuses = [];

        if (Auth::user()->hasPermission('lead.update')) {
            $allowedStatuses[] = 'new';
        }
        if (Auth::user()->hasPermission('lead.update_reservation')) {
            $allowedStatuses[] = 'prospect';
        }
        if (Auth::user()->hasPermission('lead.upload_document')) {
            $allowedStatuses[] = 'reserve';
        }
        if (Auth::user()->hasPermission('lead.update_payment')) {
            $allowedStatuses[] = 'document_and_legal_process';
        }

        $data = Lead::with('contact:id,name,phone,source', 'assignTo:id,name')
            ->when(count($allowedStatuses) > 0, function ($query) use ($allowedStatuses) {
                $query->whereIn('status', $allowedStatuses);
            }, function ($query) {
                $query->whereRaw('0 = 1'); // hasil kosong
            })
            ->whereDoesntHave('payment')
            ->orderBy('status')
            ->select(
                'id',
                'contact_id',
                'status',
                'due_date',
                'assign_to',
                'created_at',
                DB::raw("
                    CASE
                        WHEN due_date < now() THEN 1
                        ELSE 0
                    END AS is_late
                ")
            )
            ->get();

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function getAllPendingTaskPaymentLeadByUser() : array
    {
        $data = Lead::with('contact:id,name,phone,source', 'assignTo:id,name')
            ->join('lead_payments', 'leads.id', '=', 'lead_payments.lead_id')
            ->orderBy('lead_payments.status')
            ->when(Auth::user()->hasPermission('update_final_legality'), function ($query) {
                $query->whereIn('lead_payments.status', ['proses_bank', 'sp3k', 'akad_kredit', 'cash']);
            }, function ($query) {
                $query->whereRaw('0 = 1'); // hasil kosong
            })
            ->select(
                'leads.id',
                'leads.contact_id',
                'lead_payments.status',
                'leads.assign_to',
                'leads.created_at',
                )
            ->get();

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function getAllNewLead() : array
    {
        $data = Lead::with('contact:id,name,phone,source', 'assignTo:id,name')
            ->where('status', 'new')
            ->select(
                'id',
                'contact_id',
                'status',
                'due_date',
                'assign_to',
                'created_at',
                )
            ->get();

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function getMarketingPerformace() : array
    {
        $adminUser = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.name', 'Admin')
            ->select('users.id')
            ->first();

        if (!$adminUser) {
            return [
                'error' => null,
                'code' => 200,
                'result' => []
            ];
        }

        $query = MarketingTask::query()
            ->selectRaw('user_id, COUNT(*) as total_tasks')
            ->selectRaw('SUM(CASE WHEN is_ontime = true THEN 1 ELSE 0 END) as on_time')
            ->selectRaw('SUM(CASE WHEN is_ontime = false THEN 1 ELSE 0 END) as late')
            ->when(!auth()->user()->hasPermission('marketing.get_marketing_performance'), function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->where('user_id', '!=', $adminUser->id)
            ->groupBy('user_id');

        $data = $query->with('user:id,name')->get()->map(function ($row) {
            return [
                'user_id' => $row->user_id,
                'user_name' => $row->user->name ?? '-',
                'total_tasks' => (int) $row->total_tasks,
                'on_time' => (int) $row->on_time,
                'late' => (int) $row->late,
                'ontime_percentage' => $row->total_tasks > 0 ? round($row->on_time / $row->total_tasks * 100, 2) : 0
            ];
        });

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function summaryStatus($when) : array
    {
        if ($when == 'today') {
            $start = Carbon::now()->startOfDay();
            $end = Carbon::now()->endOfDay();
        } elseif ($when == 'last_week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        } elseif ($when == 'last_month') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        } elseif ($when == 'last_year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
        }

        $query = LeadHistory::query()
            ->selectRaw('new_status as status, COUNT(*) as total, changed_at')
            ->whereBetween('changed_at', [$start, $end])
            ->groupBy('new_status', 'changed_at');

        $totalSurvey = Survey::whereBetween('actual_survey_date', [$start, $end])
            ->selectRaw('count(*) as total')
            ->whereNotNull('actual_survey_date')
            ->get()->sum('total');

        
        $data = $query->get()->mapWithKeys(function ($row) use ($totalSurvey) {
            if ($row->status === 'new') {
                $row->total = $row->total - $totalSurvey;
            }

            return [
                $row->status => (int) $row->total,
            ];
        });


        if ($totalSurvey > 0) {
            $data['survey'] = $totalSurvey;
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    // summarySource
    public function summarySource($when) : array
    {
        if ($when == 'today') {
            $start = Carbon::now()->startOfDay();
            $end = Carbon::now()->endOfDay();
        } elseif ($when == 'last_week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        } elseif ($when == 'last_month') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        } elseif ($when == 'last_year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
        }

        $query = Lead::join('contacts', 'leads.contact_id', '=', 'contacts.id')
            ->select('contacts.source', DB::raw('COUNT(*) as total'))
            ->whereBetween('leads.created_at', [$start, $end])
            ->groupBy('contacts.source', 'leads.created_at');

        
        $data = $query->get()->mapWithKeys(function ($row) {
            return [
                $row->source => (int) $row->total,
            ];
        });
        
        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

   public function summaryChanged($when): array
    {
        $now = Carbon::now();

        switch ($when) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end   = $now->copy()->endOfDay();
                break;
            case 'last_week':
                $start = $now->copy()->startOfWeek();
                $end   = $now->copy()->endOfWeek();
                break;
            case 'last_month':
                $start = $now->copy()->startOfMonth();
                $end   = $now->copy()->endOfMonth();
                break;
            case 'last_year':
                $start = $now->copy()->startOfYear();
                $end   = $now->copy()->endOfYear();
                break;
            default:
                return [
                    'error' => 'Invalid period',
                    'code'  => 400,
                    'result' => [],
                ];
        }

        // Ambil data dari LeadHistory
        $leadHistory = LeadHistory::query()
            ->selectRaw('COUNT(*) as total, DATE(changed_at) as changed_date')
            ->whereBetween('changed_at', [$start, $end])
            ->groupBy('changed_date')
            ->get();

        // Ambil data dari Survey
        $survey = Survey::query()
            ->selectRaw('COUNT(*) as total, DATE(actual_survey_date) as survey_date')
            ->whereBetween('actual_survey_date', [$start, $end])
            ->groupBy('survey_date')
            ->get();

        // Ubah ke keyed collection berdasarkan tanggal
        $data = $leadHistory->mapWithKeys(function ($row) {
            return [$row->changed_date => (int) $row->total];
        });

        // Gabungkan data survey
        if ($survey->isNotEmpty()) {
            foreach ($survey as $item) {
                $date = $item->survey_date;
                $data[$date] = ($data[$date] ?? 0) + $item->total;
            }
        }

        // Sortir berdasarkan tanggal (opsional)
        $data = collect($data)->sortKeys();

        return [
            'error' => null,
            'code'  => 200,
            'result' => $data->toArray(),
        ];
    }

   public function leadFunnel($month, $year): array
    {
        // Hitung batas tanggal terakhir
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // LeadHistory sampai tanggal tsb
        $leadData = LeadHistory::query()
            ->selectRaw('new_status as status, COUNT(*) as total')
            ->whereDate('changed_at', '<=', $endDate)
            ->groupBy('new_status')
            ->get();

        // Total survey sampai tanggal tsb
        $totalSurvey = Survey::query()
            ->whereDate('actual_survey_date', '<=', $endDate)
            ->whereNotNull('actual_survey_date')
            ->count();

        // Mapping data status
        $data = $leadData->mapWithKeys(function ($row) use ($totalSurvey) {
            if ($row->status === 'new') {
                $row->total = max(0, $row->total - $totalSurvey);
            }

            return [
                $row->status => (int) $row->total,
            ];
        });

        // Tambahkan survey
        $data['survey'] = $totalSurvey;

        // Pastikan semua tahap ada
        $data = [
            "new"         => $data['new'] ?? 0,
            "survey"      => $data['survey'] ?? 0,
            "reservation" => $data['reservation'] ?? 0,
            "payment"     => $data['payment'] ?? 0,
        ];

        // Base total = semua kontak sampai akhir bulan tsb
        $baseTotal = Contact::query()
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        // Hitung persentase konversi antar tahap
        $previousTotal = $baseTotal;
        foreach ($data as $key => $value) {
            $data[$key] = [
                'total'      => $value,
                'percentage' => $previousTotal > 0 ? round($value / $previousTotal * 100, 2) : 0,
            ];
            $previousTotal = $value;
        }

        return [
            'error'  => null,
            'code'   => 200,
            'result' => $data,
        ];
    }

    public function taskPerformance($year)
    {
        $adminUser = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.name', 'Admin')
            ->select('users.id')
            ->first();

        if (!$adminUser) {
            return [
                'error' => null,
                'code'  => 200,
                'result' => []
            ];
        }

        $tasks = MarketingTask::query()
            ->whereYear('completed_at', $year)
            ->join('users as user', 'user.id', '=', 'marketing_tasks.user_id')
            ->where('user_id', '!=', $adminUser->id)
            ->get()
            ->groupBy('user.name')
            ->map(function ($items, $userName) {
                return [
                    'late'   => $items->where('is_ontime', false)->count(),
                    'ontime' => $items->where('is_ontime', true)->count(),
                ];
            })
            ->toArray();

        return [
            'error' => null,
            'code'  => 200,
            'result' => $tasks
        ];
    }

}