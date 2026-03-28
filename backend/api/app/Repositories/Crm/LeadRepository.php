<?php
        
namespace App\Repositories\Crm;

use App\Models\CheckListDocument;
use App\Models\CollectionDocument;
use App\Models\Contact;
use App\Models\Document;
use App\Models\FinalLegality;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\LeadPayment;
use App\Models\MarketingTask;
use App\Models\PaymentCheklist;
use App\Models\PaymentSelectedBank;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class LeadRepository
{
    public function getLeadsGroup() : array
    {
        return [
            'error' => null,
            'code' => 200,
            'result' => Lead::select('status', DB::raw('count(*) as total'))->groupBy('status')->get()->toArray(),
        ];
    }

    public function hasLead($contactId) : array
    {
        $isHaslead = Lead::where('contact_id', $contactId)->exists();

        return [
            'error' => null,
            'code' => 200,
            'result' => $isHaslead
        ];
    }

    public function getMarketingAgents() : array
    {
        $data = User::whereHas('role', function ($query) {
            $query->where('group', 'marketing_agent');
        })
        ->select('id', 'name')
        ->get();

        return [
            'error' => null,
            'code' => 200,
            'result' => $data,
        ];
    }

    // getSurveyLocation
    public function getSurveyLocation() : array
    {
        return [
            'error' => null,
            'code' => 200,
            'result' => Project::select('id', 'name')->get(),
        ];
    }

    public function create($data, $source) : array
    {
        try {
            DB::beginTransaction();
            $createLead = Lead::create($data);

            // update contact source
            Contact::where('id', $data['contact_id'])->update([
                'source' => $source
            ]);

            // create lead history
            $createLead->history()->create([
                'action_by' => auth()->user()->id,
                'old_status' => null,
                'new_status' => $createLead->status,
                'changed_at' => now(),
            ]);

            
            MarketingTask::create([
                'user_id' => auth()->user()->id,
                'lead_id' => $createLead->id,
                'task' => 'create_lead',
                'description' => 'create lead from contact',
                'is_ontime' => 1,
                'due_date' => Carbon::now(),
                'completed_at' => Carbon::now(),
            ]);
            
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $createLead
        ];
    }

    public function getAll($search, $marketing_id, $status, $page, $per_page, $source, $sort) : array
    {
        try {
            $order = $sort['sortDir'];

            $leads = Lead::join('contacts', 'contacts.id', '=', 'leads.contact_id')
                ->leftJoin('users', 'users.id', '=', 'leads.assign_to')
                ->leftJoin('surveys', 'surveys.lead_id', '=', 'leads.id')
                ->where(function ($query) use ($marketing_id, $status) {
                    if ($marketing_id) {
                        $query->where('leads.assign_to', $marketing_id);
                    }
                    if ($status) {
                        $query->where('leads.status', $status);
                    }
                })
                ->when($search, function ($query) use ($search,) {
                    $query->where('contacts.name', 'ilike', '%' . $search . '%')
                    ->orWhere('contacts.phone', 'ilike', '%' . $search . '%')
                    ->orWhere('contacts.email', 'ilike', '%' . $search . '%')
                    ->orWhere('leads.note', 'ilike', '%' . $search . '%');
                })
                ->when($sort['by'] == 'name', function ($query) use ($order) {
                    $query->orderByRaw('LOWER(contacts.name) ' . $order);
                })
                ->when($sort['by'] == 'duration', function ($query) use ($order) {
                    $query->orderBy('leads.created_at', $order == 'asc' ? 'desc' : 'asc');
                })
                ->when($sort['by'] == 'order_number', function ($query) use ($order) {
                    $query->orderBy('leads.order_number', $order);
                })
                ->when($source, function ($query) use ($source) {
                    $query->where('contacts.source', $source);
                })
                ->select(
                    'leads.id',
                    'leads.order_number',
                    'leads.contact_id',
                    'leads.status',
                    'leads.due_date',
                    'leads.assign_to',
                    'leads.created_at',
                    'leads.note',
                    'contacts.name as contact_name',
                    'contacts.phone as contact_phone',
                    'contacts.source as contact_source',
                    'users.name as marketing_agent',
                    'surveys.created_at as stop_date'
                    )
                ->paginate($per_page, ['*'], 'page', $page);
        } catch (\Throwable $th) {
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null,
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $leads,
        ];
    }

    public function getNonLeadContacts($search = null) : array
    {
        $select = Contact::when($search, function ($query, $search) {
                return $query->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'ilike', "%{$search}%")
                    ->orWhere('location', 'ilike', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->whereDoesntHave('lead')
            ->select(
                'id',
                'name',
                'phone',
                DB::raw('LEAST(
                    ROW_NUMBER() OVER (PARTITION BY phone ORDER BY created_at)
                 ) as is_original')
                )
            // ->limit(20)
            ->get();

        return [
            'error' => null,
            'code' => 200,
            'result' => $select,
        ];
    }

    public function getLeadReserve($search = null) : array
    {
        $data = Lead::where('leads.status', 'reserve')
            ->join('reservations', 'reservations.lead_id', '=', 'leads.id')
            ->where('reservations.status', 'confirmed')
            ->with('contact:id,name,phone,email', 'assignTo:id,name')
            // ->select('id', 'contact_id', 'survey_location_id', 'survey_date', 'assign_to')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('contact', function ($q) use ($search) {
                    $q->where('contacts.name', 'ilike', '%' . $search . '%')
                    ->orWhere('contacts.email', 'ilike', '%' . $search . '%')
                    ->orWhere('contacts.phone', 'ilike', '%' . $search . '%');
                });
            })
            ->whereDoesntHave('collectionDocuments')
            ->select('leads.id', 'leads.contact_id', 'leads.survey_location_id', 'leads.survey_date', 'leads.assign_to', 'reservations.id as reservation_id')
            ->get();

        return [
            'error' => null,
            'code' => 200,
            'result' => $data,
        ];
    }

    public function getLeadById($id) : array
    {
        $lead = Lead::with('contact:id,name,phone,email,source', 'assignTo:id,name')->where('id', $id)->first();
        if (!$lead) {
            return [
                'error' => 'Lead not found',
                'code' => 404,
                'result' => null
            ];
        }

        $lead->unit_preferences = Unit::where('id', $lead->unit_preference_id)->first()?->type;

        $lokasiSurvey = Project::where('id', $lead->survey_location_id)->first();
        if ($lokasiSurvey) {
            $lead->survey_location = $lokasiSurvey->name;
        } else {
            $lead->survey_location = null;
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $lead
        ];
    }


    public function getLeadHistory($id) : array
    {
        $lead = Lead::with('history')
            ->with('history.actionBy:id,name')
            ->where('id', $id)->first();
        if (!$lead) {
            return [
                'error' => 'Lead not found',
                'code' => 404,
                'result' => null
            ];
        }
        return [
            'error' => null,
            'code' => 200,
            'result' => $lead
        ];
    }

    public function isLeadReserved($id) : array
    {
        $lead = Lead::with('reservations')->where('id', $id)->first();
        if (!$lead) {
            return [
                'error' => 'Lead not found',
                'code' => 404,
                'result' => null
            ];
        }

        if (!$lead->reservations || $lead->reservations->status != 'confirmed') {
            return [
                'error' => null,
                'code' => 200,
                'result' => false
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => true
        ];
    }

    public function update($id, $data, $source) : array
    {
        try {
            DB::beginTransaction();
            $oldLead = Lead::select('status', 'contact_id', 'id', 'due_date')->where('id', $id)->first();
            if (!$oldLead) {
                DB::rollBack();
                return [
                    'error' => 'Lead not found',
                    'code' => 404,
                    'result' => null
                ];
            }

            $oldDocumentation = $oldLead->survey_documentation ?? null;
            if (isset($data['survey_documentation']) && $data['survey_documentation'] != null) {
                $data['survey_documentation'] = uploadFile('crm/survey_documentation', $data['survey_documentation']);
                if ($data['survey_documentation'] === false) {
                    DB::rollBack();
                    return [
                        'error' => 'Failed to upload file',
                        'result' => null,
                        'code' => 500
                    ];
                }
            }
            
            Lead::where('id', $id)->update($data);

            Contact::where('id', $oldLead['contact_id'])->update([
                'source' => $source
            ]);

            // if ($oldLead->status != $data['status']) {
            //     Lead::where('id', $id)->first()->history()->create([
            //         'action_by' => auth()->user()->id,
            //         'old_status' => $oldLead->status,
            //         'new_status' => $data['status'],
            //         'changed_at' => now(),
            //     ]);

            //     if ($data['status'] == 'prospect') {
            //         $hasTask = MarketingTask::where('lead_id', $id)->where('task', 'lead_to_prospect')->first();
            //         if (!$hasTask) {
            //             MarketingTask::create([
            //                 'user_id' => auth()->user()->id,
            //                 'lead_id' => $id,
            //                 'task' => 'lead_to_prospect',
            //                 'description' => 'Lead to Prospect',
            //                 'is_ontime' => $oldLead->due_date < now() ? 0 : 1,
            //                 'due_date' => $oldLead->due_date,
            //                 'completed_at' => now(),
            //             ]);
            //         }
            //     }
            // }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        if ($oldDocumentation != null) {
            $path = explode('api/file/', $oldDocumentation);
            deleteFile(isset($path[1]));
        }
        
        return [
            'error' => null,
            'code' => 200,
            'result' => Lead::where('id', $id)->first()
        ];
    }

    public function updateStatus($id, $status) : array
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return [
                'error' => 'Lead not found',
                'code' => 404,
                'result' => null
            ];
        }

        $oldStatus = $lead->status;

        $dueDate = null;
        $durationSetting = config('setting.lead_status_durations');
        if (isset($durationSetting[$status]) && $durationSetting[$status] > 0) {
            $dueDate = now()->addDays($durationSetting[$status]);
        }

        if ($oldStatus == $status) {
            return [
                'error' => 'Status already updated',
                'code' => 400,
                'result' => null
            ];
        }

        try {
            DB::transaction(function () use ($lead, $status, $dueDate, $oldStatus) {
                $lead->update([
                    'status' => $status,
                    'due_date' => $dueDate
                ]);

                $lead->history()->create([
                    'action_by'   => auth()->id(),
                    'old_status'  => $oldStatus,
                    'new_status'  => $status,
                    'changed_at'  => now(),
                ]);
            });
        } catch (\Throwable $th) {
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $lead->fresh()
        ];
    }

    public function delete($id) : array
    {
        try {
            DB::beginTransaction();
            $lead = Lead::find($id);
            if (!$lead) {
                DB::rollBack();
                return [
                    'error' => 'Lead not found',
                    'code' => 404,
                    'result' => null
                ];
            }
            LeadHistory::where('lead_id', $id)->delete();
            $lead->delete();

            // cek reservasi
            $reservations = Reservation::where('lead_id', $id)->first();
            if ($reservations) {
                $reservations->delete();
            }

            // cek dokument
            $collectionDocuments = CollectionDocument::where('lead_id', $id)->first();
            if ($collectionDocuments) {
                $collectionDocuments->delete();
                Document::where('collection_document_id', $collectionDocuments->id)->delete();
                CheckListDocument::where('lead_document_id', $collectionDocuments->id)->delete();
            }

            // cek payment
            $payments = LeadPayment::where('lead_id', $id)->first();
            if ($payments) {
                $payments->delete();
                PaymentCheklist::where('payment_id', $payments->id)->delete();
                PaymentSelectedBank::where('payment_id', $payments->id)->delete();
            }

            // delete final legality
            $legalitas = FinalLegality::where('lead_id', $id)->first();
            if ($legalitas) {
                $legalitas->delete();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'error' => $th->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $lead
        ];
    }
}