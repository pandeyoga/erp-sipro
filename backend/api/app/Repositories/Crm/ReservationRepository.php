<?php
        
namespace App\Repositories\Crm;

use App\Models\CollectionDocument;
use App\Models\Lead;
use App\Models\MarketingTask;
use App\Models\Reservation;
use App\Models\UnitProperty;
use App\Repositories\finance\CashInRepository;
use Illuminate\Support\Facades\DB;

class ReservationRepository
{
    // summary()
    public function summary()
    {
        $all = Reservation::with('lead')
            ->select(DB::raw('count(*) as count, status as reservation_status'))
            ->groupBy('reservation_status')
            ->get()
            ->groupBy('reservation_status')
            ->toArray();

        return [
            'error' => null,
            'code' => 200,
            'result' => $all
        ];
    }

    // getAll
    public function getAll($filters)
    {
        try {
            $page = $filters['page'] ?? 1;
            $per_page = $filters['per_page'] ?? 10;
            $search = $filters['search'] ?? null;
            $status = $filters['status'] ?? null;
            $sortKey = $filters['sortKey'] ?? 'created_at';
            $order = $filters['sortDir'] ?? 'desc';

            $query = Reservation::join('leads', 'reservations.lead_id', '=', 'leads.id')
                ->join('contacts', 'contacts.id', '=', 'leads.contact_id')
                ->join('unit_properties', 'reservations.property_unit_id', '=', 'unit_properties.id')
                ->join('projects', 'unit_properties.project_id', '=', 'projects.id')
                ->join('clusters', 'unit_properties.cluster_id', '=', 'clusters.id')
                ->join('units', 'unit_properties.unit_type_id', '=', 'units.id')
                ->leftJoin('collection_documents', 'reservations.lead_id', '=', 'collection_documents.lead_id');

            if ($search != null) {
                $query->where('contacts.name', 'ilike', '%' . $search . '%')
                ->orWhere('contacts.email', 'ilike', '%' . $search . '%')
                ->orWhere('contacts.phone', 'ilike', '%' . $search . '%')
                ->orWhere('reservations.notes', 'ilike', '%' . $search . '%');
            }

            if ($status != null) {
                $query->where('reservations.status', $status);
            }

            if ($sortKey == "name") {
                $query->orderByRaw('LOWER(contacts.name) ' . $order);
            } elseif ($sortKey == "duration") {
                $query->orderBy('reservation_date', $order == 'asc' ? 'desc' : 'asc');
            }

            $query->select(
                'reservations.id',
                'reservations.status',
                'reservations.reservation_date', 
                'reservations.lead_id',
                'reservations.property_unit_id',
                'unit_properties.unit_number as unit_number',
                'projects.name as project_name',
                'clusters.name as cluster_name',
                'units.type as unit_type',
                'reservations.dp_amount',
                'contacts.name as contact_name',
                'contacts.phone as contact_phone',
                'reservations.notes as notes',
                'collection_documents.created_at as stop_date'
            );

            $all = $query->paginate($per_page, ['*'], 'page', $page);
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
            'result' => $all
        ];
    }

    // find
    public function find($id)
    {
        return [
            'error' => null,
            'code' => 200,
            'result' => Reservation::find($id)
        ];
    }

    // leadHasReservation
    public function leadHasReservation($lead_id)
    {
        return [
            'error' => null,
            'code' => 200,
            'result' => Lead::find($lead_id)->reservations()->exists()
        ];
    }

    // leadIsProspect
    public function leadIsProspect($lead_id)
    {
        return [
            'error' => null,
            'code' => 200,
            'result' => Lead::where('id', $lead_id)->where('status', 'prospect')->exists()
        ];
    }

    public function propertyIsNotBooked($property_id)
    {
        $property = UnitProperty::find($property_id)
            ->join('reservations', 'unit_properties.id', '=', 'reservations.property_unit_id')
            ->select('reservations.id')
            ->exists();

        return [
            'error' => null,
            'code' => 200,
            'result' => !$property
        ];
    }

    public function getLeadProspect($search)
    {
        $prospect = Lead::where('status', 'prospect')
            ->with('contact:id,name,phone,email', 'assignTo:id,name')
            ->select('id', 'contact_id', 'survey_location_id', 'survey_date', 'assign_to')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('contact', function ($q) use ($search) {
                    $q->where('contacts.name', 'ilike', '%' . $search . '%')
                    ->orWhere('contacts.email', 'ilike', '%' . $search . '%')
                    ->orWhere('contacts.phone', 'ilike', '%' . $search . '%');
                });
            })
            ->whereDoesntHave('reservations')
            ->get();

        return [
            'error' => null,
            'code' => 200,
            'result' => $prospect
        ];
    }

    // listAllProperties
    public function listAllProperties($reservationId)
    {
        $list = UnitProperty::join('projects', 'unit_properties.project_id', '=', 'projects.id')
            ->join('clusters', 'unit_properties.cluster_id', '=', 'clusters.id')
            ->join('units', 'unit_properties.unit_type_id', '=', 'units.id')
            ->leftjoin('reservations', 'unit_properties.id', '=', 'reservations.property_unit_id')
            ->select(
                'unit_properties.id',
                'projects.name as project_name',
                'clusters.name as cluster_name',
                'units.type as unit_type',
                'unit_properties.unit_number',
                'unit_properties.status'
            )
            ->where(function ($query) use ($reservationId) {
                $query->whereNull('reservations.id')
                ->orWhere('reservations.id', $reservationId);
            })
            ->whereIn('unit_properties.status', ['belum_dibangun', 'under_development', 'available'])
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->project_name . ' - ' . $item->unit_number . ' [' . $item->cluster_name . ' - ' . $item->unit_type . ']',
                ];
            });
        
        return [
            'error' => null,
            'result' => $list,
            'code' => 200
        ];
    }

    public function getProperties()
    {
        $list = UnitProperty::join('projects', 'unit_properties.project_id', '=', 'projects.id')
            ->join('clusters', 'unit_properties.cluster_id', '=', 'clusters.id')
            ->join('units', 'unit_properties.unit_type_id', '=', 'units.id')
            ->select(
                'unit_properties.id',
                'projects.name as project_name',
                'clusters.name as cluster_name',
                'units.type as unit_type',
                'unit_properties.unit_number',
                'unit_properties.status'
            )
            ->whereDoesntHave('reservation')
            ->whereIn('unit_properties.status', ['belum_dibangun', 'under_development', 'available'])
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->project_name . ' - ' . $item->unit_number . ' [' . $item->cluster_name . ' - ' . $item->unit_type . ']',
                ];
            });
        
        return [
            'error' => null,
            'result' => $list,
            'code' => 200
        ];
    }

    // store
    public function store($data, $cashInData = null, $constructionNotes = null)
    {
        $unitPrice = $data['unit_price'];
        try {
            $data = [
                'lead_id' => $data['lead_id'],
                'reservation_date' => $data['reservation_date'],
                'status' => config('setting.reservation_statuses.0'),
                'property_unit_id' => $data['property_unit_id'],
                'dp_amount' => $data['dp_amount'],
                'notes' => $data['notes'],
                'hook_additional_fee' => $data['hook_additional_fee'] ?? 0,
                'additional_land_area_fee' => $data['additional_land_area_fee'] ?? 0,
                'additional_building_specifications_fee' => $data['additional_building_specifications_fee'] ?? 0,
                'all_in_fee' => $data['all_in_fee'] ?? 0
            ];

            $unitProperty = UnitProperty::where('id', $data['property_unit_id'])->first();
            $unitProperty->price = $unitPrice;

            if ($constructionNotes) {
                $unitProperty->construction_notes = $constructionNotes;
            }

            $unitProperty->save();

            DB::beginTransaction();
            $reservation = Reservation::create($data);
            $reservation->unit_price = $unitPrice;

            $dueDate = now()->addDays(config('setting.lead_status_durations')['reserve']);

            $lead = Lead::where('id', $data['lead_id'])->first();
            $lead->due_date = $dueDate;
            $lead->save();


            $repoCashIn = new CashInRepository();
            // create booking cashIn
            $repoCashIn->createCashinForReservation($cashInData);

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
            'result' => $reservation
        ];
    }

    // update
    public function update($id, $priceUnit, $data, $constructionNotes = null)
    {
        $lead = Lead::where('id', $data['lead_id'])->first();
        try {
            DB::beginTransaction();
            $reservation = Reservation::find($id);
            
            $hasDocument = CollectionDocument::where('lead_id', $lead->id)->first();
            if ($hasDocument) {
                $data['status'] = 'confirmed';
            } else {
                $hasTask = MarketingTask::where('lead_id', $data['lead_id'])->where('task', 'lead_to_reservation')->first();
                if (!$hasTask) {
                    $oldLead = Lead::where('id', $data['lead_id'])->first();
                    MarketingTask::create([
                        'user_id' => auth()->user()->id,
                        'lead_id' => $data['lead_id'],
                        'task' => 'lead_to_reservation',
                        'description' => 'Lead to reservation',
                        'is_ontime' => $oldLead->due_date < now() ? 0 : 1,
                        'due_date' => $oldLead->due_date,
                        'completed_at' => now(),
                    ]);
                }
            }

            $reservation->update($data);
            $reservation->dp_proof_url = url($reservation->dp_proof_url);
            $reservation->booking_document_url = url($reservation->booking_document_url);

            $unitProperty = UnitProperty::where('id', $reservation->property_unit_id)->first();
            $unitProperty->price = $priceUnit;

            if ($constructionNotes) {
                $unitProperty->construction_notes = $constructionNotes;
            }

            $unitProperty->save();


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
            'result' => $reservation
        ];
    }

    // getById
    public function getById($id)
    {
        $reservation = Reservation::with(
                'lead:id,status,contact_id,assign_to,survey_date',
                'lead.contact:id,name,email,phone',
                'lead.assignTo:id,name'
                )->join('unit_properties', 'reservations.property_unit_id', '=', 'unit_properties.id')
                ->join('projects', 'unit_properties.project_id', '=', 'projects.id')
                ->join('clusters', 'unit_properties.cluster_id', '=', 'clusters.id')
                ->join('units', 'unit_properties.unit_type_id', '=', 'units.id')
                ->select(
                    'reservations.id',
                    'reservations.status',
                    'reservations.reservation_date',
                    'reservations.lead_id',
                    'reservations.property_unit_id',
                    'reservations.dp_proof_url',
                    'reservations.booking_document_url',
                    'reservations.dp_amount',
                    'unit_properties.unit_number',
                    'unit_properties.price as unit_price',
                    'reservations.all_in_fee',
                    'reservations.hook_additional_fee',
                    'reservations.additional_land_area_fee',
                    'reservations.additional_building_specifications_fee',
                    'reservations.notes',
                    'projects.name as project_name',
                    'clusters.name as cluster_name',
                    'units.type as unit_type'
                    )
                ->find($id);

        if (!$reservation) {
            return [
                'error' => 'Reservation not found',
                'code' => 404,
                'result' => null
            ];
        }

        // get construction notes from unit property
        $unitProperty = UnitProperty::where('id', $reservation->property_unit_id)->first();
        $reservation->construction_notes = $unitProperty->construction_notes;

        return [
            'error' => null,
            'code' => 200,
            'result' => $reservation
        ];
    }
}