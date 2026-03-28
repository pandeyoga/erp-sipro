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
use App\Models\Survey;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SurveyRepository
{
    public function getLeadsGroup() : array
    {
        return [
            'error' => null,
            'code' => 200,
            'result' => Survey::select('status', DB::raw('count(*) as total'))->groupBy('status')->get()->toArray(),
        ];
    }

    public function hasSurvey($leadId) : array
    {
        $isHaslead = Survey::where('lead_id', $leadId)->exists();

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

    public function getSurveyLocation() : array
    {
        return [
            'error' => null,
            'code' => 200,
            'result' => Project::select('id', 'name')->get(),
        ];
    }

    public function create($data, $leadId) : array
    {
        try {
            DB::beginTransaction();
            Lead::where('id', $leadId)->update($data);

            $status = 'not_scheduled';//, 'scheduled', 'done'
            if (isset($data['survey_date'])) {
                $status = 'scheduled';
                if (isset($data['actual_survey_date'])) {
                    $status = 'done';
                }
            }
            
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

            if ($status == 'done') {
                $newDueDate = isset(config('setting.lead_status_durations')['prospect']) ? Carbon::now()->addDays(config('setting.lead_status_durations')['prospect']) : null;
            }

            Survey::create([
                'lead_id' => $leadId,
                'status' => $status,
                'scheduled_date' => isset($data['survey_date']) ? $data['survey_date'] : null,
                'actual_survey_date' => isset($data['actual_survey_date']) ? $data['actual_survey_date'] : null,
                'scheduled_at' => $status !== 'not_scheduled' ? Carbon::now() : null,
                'created_by' => auth()->user()->id
            ]);

            $lead = Lead::where('id', $leadId)->first();

            if ($data['survey_documentation'] != null) {
                Lead::where('id', $leadId)->update([
                    'survey_documentation' => $data['survey_documentation'],
                ]);
            }

            if ($status == 'done' && $lead->status == 'new') {
                Lead::where('id', $leadId)->update([
                    'status' => 'prospect',
                    'due_date' => $newDueDate,
                ]);

                Lead::where('id', $leadId)->first()->history()->create([
                    'action_by' => auth()->user()->id,
                    'old_status' => 'new',
                    'new_status' => 'prospect',
                    'changed_at' => now(),
                ]);

                $hasTask = MarketingTask::where('lead_id', $leadId)->where('task', 'lead_to_prospect')->first();
                if (!$hasTask) {
                    MarketingTask::create([
                        'user_id' => auth()->user()->id,
                        'lead_id' => $leadId,
                        'task' => 'lead_to_prospect',
                        'description' => 'Lead to Prospect',
                        'is_ontime' => $lead->due_date < now() ? 0 : 1,
                        'due_date' => $lead->due_date,
                        'completed_at' => now(),
                    ]);
                }
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
            'result' => null
        ];
    }

    public function getAll($search, $marketing_id, $status, $page, $per_page, $source, $sort) : array
    {
        try {
            $order = $sort['sortDir'];

            $leads = Lead::join('contacts', 'contacts.id', '=', 'leads.contact_id')
                ->join('surveys', 'surveys.lead_id', '=', 'leads.id')
                ->leftJoin('reservations', 'reservations.lead_id', '=', 'leads.id')
                ->where(function ($query) use ($status) {
                    if ($status) {
                        $query->where('surveys.status', $status);
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
                    $query->orderByRaw("
                        CASE 
                            WHEN surveys.status = 'done' THEN surveys.actual_survey_date
                            WHEN surveys.status = 'scheduled' THEN surveys.scheduled_date
                            ELSE surveys.created_at
                        END $order
                    ");
                })
                ->when($source, function ($query) use ($source) {
                    $query->where('contacts.source', $source);
                })
                ->select(
                    'surveys.id',
                    'leads.contact_id',
                    'leads.note',
                    'surveys.status',
                    'surveys.created_at',
                    'surveys.scheduled_at',
                    'surveys.scheduled_date',
                    'surveys.actual_survey_date',
                    'contacts.name as contact_name',
                    'contacts.phone as contact_phone',
                    'reservations.created_at as stop_date',
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

    public function getNonSurveyLead($search = null) : array
    {
        $select = Lead::where('leads.status', 'new')
            ->join('contacts', 'contacts.id', '=', 'leads.contact_id')
            ->leftJoin('surveys', 'surveys.lead_id', '=', 'leads.id')
            ->leftJoin('projects', 'projects.id', '=', 'leads.survey_location_id')
            ->leftJoin('units', 'units.id', '=', 'leads.unit_preference_id')
            ->where('surveys.id', null)
            ->when($search, function ($query) use ($search) {
                $query->where('contacts.name', 'ilike', '%' . $search . '%')
                ->orWhere('contacts.email', 'ilike', '%' . $search . '%')
                ->orWhere('contacts.phone', 'ilike', '%' . $search . '%');
            })
            ->select(
                'leads.id',
                'leads.contact_id',
                'contacts.name as contact_name',
                'contacts.phone as contact_phone',
                'contacts.email as contact_email',
                'contacts.source as contact_source',
                'projects.name as project_name',
                'units.type as unit_name'
            )
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
        $lead = Lead::join('contacts', 'contacts.id', '=', 'leads.contact_id')
                ->join('surveys', 'surveys.lead_id', '=', 'leads.id')
                ->where('surveys.id', $id)
                ->select(
                    'surveys.id',
                    'leads.contact_id',
                    'leads.unit_preference_id',
                    'leads.survey_location_id',
                    'leads.survey_documentation',
                    'leads.pic',
                    'leads.note as notes',
                    'surveys.status',
                    'surveys.scheduled_date as survey_date',
                    'surveys.actual_survey_date',
                    'contacts.name as name',
                    'contacts.phone as phone',
                    'contacts.email as email',
                    )
                ->first();
        
        $lead->survey_documentation = $lead->survey_documentation ? url($lead->survey_documentation) : null;

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

    public function update($id, $data) : array
    {
        try {
            DB::beginTransaction();
            $survey = Survey::where('id', $id)->first();
            $leadId = $survey->lead_id;

            $oldLead = Lead::select('status', 'contact_id', 'id', 'due_date')->where('id', $leadId)->first();
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

            $leadUpdate = [];
            if (isset($data['unit_preference_id'])) {
                $leadUpdate['unit_preference_id'] = $data['unit_preference_id'];
            }

            if (isset($data['survey_location_id'])) {
                $leadUpdate['survey_location_id'] = $data['survey_location_id'];
            }

            if (isset($data['survey_date'])) {
                $leadUpdate['survey_date'] = $data['survey_date'];
            }

            if (isset($data['actual_survey_date'])) {
                $leadUpdate['actual_survey_date'] = $data['actual_survey_date'];
            }

            if (isset($data['notes'])) {
                $leadUpdate['note'] = $data['notes'];
            }

            if (isset($data['survey_documentation'])) {
                $leadUpdate['survey_documentation'] = $data['survey_documentation'];
            }

            $status = $survey->status;
            if (isset($data['survey_date'])) {
                $status = 'scheduled';
                if (isset($data['actual_survey_date'])) {
                    $status = 'done';
                }
            }

            if ($status == 'done') {
                $newDueDate = isset(config('setting.lead_status_durations')['prospect']) ? Carbon::now()->addDays(config('setting.lead_status_durations')['prospect']) : null;
            }

            if ($status == 'done') {
            }

            $lead = Lead::where('id', $leadId)->first();
            

            if ($status == 'done' && $lead->status == 'new') {
                $leadUpdate['status'] = 'prospect';
                $leadUpdate['due_date'] = $newDueDate;

                Lead::where('id', $leadId)->first()->history()->create([
                    'action_by' => auth()->user()->id,
                    'old_status' => 'new',
                    'new_status' => 'prospect',
                    'changed_at' => now(),
                ]);

                $hasTask = MarketingTask::where('lead_id', $leadId)->where('task', 'lead_to_prospect')->first();
                if (!$hasTask) {
                    MarketingTask::create([
                        'user_id' => auth()->user()->id,
                        'lead_id' => $leadId,
                        'task' => 'lead_to_prospect',
                        'description' => 'Lead to Prospect',
                        'is_ontime' => $lead->due_date < now() ? 0 : 1,
                        'due_date' => $lead->due_date,
                        'completed_at' => now(),
                    ]);
                }
            }

            $update = Lead::where('id', $leadId)->update($leadUpdate);
            if (!$update) {
                DB::rollBack();
                return [
                    'error' => 'Failed to update lead',
                    'result' => null,
                    'code' => 500
                ];
            }

            // update survey
            $survey->lead_id = $leadId;
            $survey->status = $status;
            
            if (isset($data['survey_date'])) {
                $survey->scheduled_date = $data['survey_date'];
            }
            if (isset($data['actual_survey_date'])) {
                $survey->actual_survey_date = $data['actual_survey_date'];
            }

            $survey->scheduled_at = $status !== 'not_scheduled' ? Carbon::now() : null;
            $survey->save();

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