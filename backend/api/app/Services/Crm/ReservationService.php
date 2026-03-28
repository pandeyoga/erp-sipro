<?php
        
namespace App\Services\Crm;

use App\Repositories\Crm\ReservationRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReservationService
{
    public function __construct(
        protected ReservationRepository $repository,
        protected LeadService $leadService
    ) {}
    
    // summary
    public function summary()
    {
        $summaryFromDb = $this->repository->summary();
        if ($summaryFromDb['error']) {
            return $summaryFromDb;
        }
        $summaryFromDb = $summaryFromDb['result'];
        
        $formating = collect($summaryFromDb)->map(function ($item, $key) {
            return $item[0]['count'];
        });

        $formating = $formating->toArray();

        $result = [
            'pending' => isset($formating['pending']) ? $formating['pending'] : 0,
            'confirmed' => isset($formating['confirmed']) ? $formating['confirmed'] : 0,
            'canceled' => isset($formating['canceled']) ? $formating['canceled'] : 0,
            'expired' => isset($formating['expired']) ? $formating['expired'] : 0,
        ];

        return [
            'error' => null,
            'code' => 200,
            'result' => $result,
        ];
    }

    // getAll
    public function getAll($filters)
    {
        $filters = [
            'search' => isset($filters['search']) ? $filters['search'] : null,
            'sortKey' => isset($filters['sortKey']) ? $filters['sortKey'] : null,
            'sortDir' => isset($filters['sortDir']) ? $filters['sortDir'] : null,
            'page' => isset($filters['page']) ? $filters['page'] : null,
            'per_page' => isset($filters['per_page']) ? $filters['per_page'] : null,
            'status' => isset($filters['status']) ? $filters['status'] : null
        ];

        $result = $this->repository->getAll($filters);
        if ($result['error']) {
            return $result;
        }
        $result = $result['result'];

        $item = collect($result->items())->map(function ($item) {
            if ($item->stop_date) {
                    $startDate = Carbon::parse($item->created_at);
                    $endDate = Carbon::parse($item->stop_date);
                    $duration = (int) $startDate->diffInDays($endDate, true) . ' days';
            } else {
                $startDate = Carbon::parse($item->created_at);
                $duration = (int) Carbon::now()->startOfDay()->diffInDays($startDate, true) . ' days';
            }
            return [
                'id' => $item->id,
                'lead_id' => $item->lead_id,
                'property_id' => $item->property_unit_id,
                'name' => $item->contact_name,
                'phone' => $item->contact_phone,
                'notes' => $item->notes,
                'reservation_status' => $item->status,
                'property_name' => $item->project_name . ' - ' . $item->unit_number . ' [' . $item->cluster_name . ' - ' . $item->unit_type . ']',
                'reservation_date' => $item->reservation_date,
                'duration' => $duration
            ];
        });

        $result->setCollection($item);

        return [
            'error' => null,
            'code' => 200,
            'result' => $result
        ];
    }

    public function leadHasReservation($lead_id)
    {
        $hasReservation = $this->repository->leadHasReservation($lead_id);
        if ($hasReservation['error']) {
            return $hasReservation;
        }
        $hasReservation = $hasReservation['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $hasReservation,
        ];
    }

    public function leadIsProspect($lead_id)
    {
        // $isPros 
    }

    // getProspect
    public function getProspect($search = null)
    {
        $leadProspect = $this->repository->getLeadProspect($search);
        if ($leadProspect['error']) {
            return $leadProspect;
        }
        $leadProspect = $leadProspect['result'];

        $leads = $leadProspect->map(function ($item, $key) {
            return [
                'id' => $item->id,
                'name' => $item->contact->name,
                'phone' => $item->contact->phone,
                'email' => $item->contact->email,
                'survey_location_id' => $item->survey_location_id,
                'survey_date' => date('Y-m-d', strtotime($item->survey_date)),
                'marketing_agent_id' => $item->assign_to,
                'marketing_agent' => $item->assignTo?->name,
            ];
        });

        return [
            'error' => null,
            'code' => 200,
            'result' => $leads,
        ];
    }

    // getProperty
    public function getProperties()
    {
        $property = $this->repository->getProperties();
        if ($property['error']) {
            return $property;
        }
        $property = $property['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $property,
        ];
    }

    // listAllProperties
    public function listAllProperties($reservationId)
    {
        $property = $this->repository->listAllProperties($reservationId);
        if ($property['error']) {
            return $property;
        }
        $property = $property['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $property,
        ];
    }

    public function store($data)
    {
        $constructionNotes = $data['construction_notes'] ?? null;
        $hasReservation = $this->repository->leadHasReservation($data['lead_id']);
        if ($hasReservation['error']) {
            return $hasReservation;
        }

        $hasReservation = $hasReservation['result'];
        if ($hasReservation) {
            return [
                'error' => 'Lead already has reservation',
                'code' => 400,
                'result' => null
            ];
        }

        $isProspect = $this->repository->leadIsProspect($data['lead_id']);
        if (!$isProspect) {
            return [
                'error' => true,
                'code' => 400,
                'result' => null
            ];
        }

        $propertyIsNotBooked = $this->repository->propertyIsNotBooked($data['property_id']);
        if (!$propertyIsNotBooked) {
            return [
                'error' => true,
                'code' => 400,
                'result' => null
            ];
        }

        $dataReservation = [
            'lead_id' => $data['lead_id'],
            'reservation_date' => $data['reservation_date'],
            'unit_price' => $data['unit_price'],
            'all_in_fee' => $data['all_in_fee'],
            'status' => config('setting.reservation_statuses.0'),
            'property_unit_id' => $data['property_id'],
            'dp_amount' => $data['reservation_fee'],
            'hook_additional_fee' => $data['hook_additional_fee'] ?? 0,
            'additional_land_area_fee' => $data['additional_land_area_fee'] ?? 0,
            'additional_building_specifications_fee' => $data['additional_building_specifications_fee'] ?? 0,
            'all_in_fee' => $data['all_in_fee'] ?? 0,
            'notes' => $data['notes'] ?? null,
        ];

        $cashInData = [
            'property_unit_id' => $data['property_id'] ?? null,
            'lead_id' => $data['lead_id'],
            'reservation_fee' => $data['reservation_fee'],
            'hook_additional_fee' => $data['hook_additional_fee'] ?? null,
            'additional_land_area_fee' => $data['additional_land_area_fee'] ?? null,
            'additional_building_specifications_fee' => $data['additional_building_specifications_fee'] ?? null,
        ];

        $store = $this->repository->store($dataReservation, $cashInData, $constructionNotes);
        if ($store['error']) {
            return $store;
        }
        $store = $store['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $store,
        ];
    }

    private function  savefile($path, $file, $id, $type)
    {
        try {
            $fileName = uploadFile($path, $file);
            if ($fileName == false) {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

        $reservation = $this->repository->find($id);
        if ($reservation['error']) {
            return null;
        }
        $reservation = $reservation['result'];

        if ($reservation == null) {
            return null;
        }
        if ($reservation->dp_proof_url !== null && $type == 'bukti_pembayaran') {
            try {
                $path = str_replace('..', '', $reservation->dp_proof_url);
                $path = explode('api/file/', $path)[1];
                deleteFile($path);
            } catch (\Exception $e) {}
        }
        if ($reservation->booking_document_url !== null && $type == 'surat_reservasi') {
            try {
                $path = str_replace('..', '', $reservation->booking_document_url);
                $path = explode('api/file/', $path)[1];
                deleteFile($path);
            } catch (\Exception $e) {}
        }
        

        return $fileName;
    }

    public function update($id,$data)
    {
        $constructionNotes = $data['construction_notes'] ?? null;
        $newPropertyId = $data['property_id'] ?? null;

        if (isset($data['reservation_proof']) && $data['reservation_proof'] != null) {
            $data['reservation_proof'] = $this->savefile('crm/reservation/bukti_pembayaran', $data['reservation_proof'], $id, 'bukti_pembayaran');
            if ($data['reservation_proof'] === false) {
                return [
                    'error' => 'Internal Server Error',
                    'code' => 500,
                    'result' => null
                ];
            }
        }

        if (isset($data['reservation_letter']) && $data['reservation_letter'] != null) {
            $data['reservation_letter'] = $this->savefile('crm/reservation/surat_pemesanan', $data['reservation_letter'], $id, 'surat_reservasi');
            if ($data['reservation_letter'] === false) {
                return [
                    'error' => 'Internal Server Error',
                    'code' => 500,
                    'result' => null
                ];
            }
        }


        $reservation = $this->repository->find($id);
        if ($reservation['error']) {
            return $reservation;
        }
        $reservation = $reservation['result'];

        $unitPrice = $data['unit_price'];
        
        $data = [
            'lead_id' => $reservation->lead_id,
            'status' => $data['status'] ?? $reservation->status,
            'reservation_date' => $data['reservation_date'],
            'dp_proof_url' => isset($data['reservation_proof']) ? $data['reservation_proof'] : $reservation->dp_proof_url,
            'booking_document_url' => isset($data['reservation_letter']) ? $data['reservation_letter'] : $reservation->booking_document_url,
            'dp_amount' => $data['reservation_fee'],
            'all_in_fee' => $data['all_in_fee'],
            'hook_additional_fee' => isset($data['hook_additional_fee']) ? $data['hook_additional_fee'] : $reservation->hook_additional_fee,
            'additional_land_area_fee' => isset($data['additional_land_area_fee']) ? $data['additional_land_area_fee'] : $reservation->additional_land_area_fee,
            'additional_building_specifications_fee' => isset($data['additional_building_specifications_fee']) ? $data['additional_building_specifications_fee'] : $reservation->additional_building_specifications_fee,
            'notes' => isset($data['notes']) ? $data['notes'] : $reservation->notes,
        ];

        if ($newPropertyId != null) {
            $propertyIsNotBooked = $this->repository->propertyIsNotBooked($newPropertyId);
            if (!$propertyIsNotBooked) {
                return [
                    'error' => true,
                    'code' => 400,
                    'result' => null
                ];
            }
            $data['property_unit_id'] = $newPropertyId;
        }

        $update = $this->repository->update($id, $unitPrice, $data, $constructionNotes);
        if ($update['error']) {
            return $update;
        }
        $update = $update['result'];

        if ($data['status'] == 'confirmed' && $reservation->status != 'confirmed') {
            $this->leadService->updateStatus($data['lead_id'], 'reserve');
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $update,
        ];
    }

    public function getById($id)
    {
        $result = $this->repository->getById($id);
        if ($result['error']) {
            return $result;
        }
        $result = $result['result'];
        
        $data = [
            'id' => $result->id,
            'name' => $result->lead->contact->name,
            'phone' => $result->lead->contact->phone,
            'email' => $result->lead->contact->email,
            'status' => $result->status,
            'survey_date' => $result->lead->survey_date,
            'marketing_agent' => $result->lead?->assignTo?->name,
            'property_id' => $result->property_unit_id,
            'property' => $result->project_name . ' - ' . $result->unit_number . ' [' . $result->cluster_name . ' - ' . $result->unit_type . ']',
            'reservation_fee' => $result->dp_amount,
            'unit_price' => $result->unit_price,
            'all_in_fee' => $result->all_in_fee,
            'hook_additional_fee' => $result->hook_additional_fee,
            'additional_land_area_fee' => $result->additional_land_area_fee,
            'additional_building_specifications_fee' => $result->additional_building_specifications_fee,
            'reservation_proof' => $result->dp_proof_url ? url($result->dp_proof_url) : null,
            'reservation_letter' => $result->booking_document_url ? url($result->booking_document_url) : null,
            'reservation_date' => $result->reservation_date,
            'notes' => $result->notes,
            'construction_notes' => $result->construction_notes,
        ];

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function delete($id)
    {
        $reservation = $this->repository->find($id);
        if ($reservation['error']) {
            return $reservation;
        }
        $reservation = $reservation['result'];

        if ($reservation == null) {
            return [
                'error' => true,
                'code' => 400,
                'result' => null
            ];
        }

        $delete = $this->leadService->delete($reservation->lead_id);
        if ($delete['error']) {
            return $delete;
        }
        $delete = $delete['result'];
    }

    
}