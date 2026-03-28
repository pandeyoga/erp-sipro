<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\GetReservationRequest;
use App\Http\Requests\Crm\StoreReservationRequest;
use App\Http\Requests\Crm\UpdateReservationRequest;
use App\Services\Crm\LeadService;
use App\Services\Crm\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function __construct(protected ReservationService $service) {}

    public function summary()
    {
        $summary = $this->service->summary();
        if ($summary['error']) {
            return $this->errorResponse($summary['error'], null, $summary['code']);
        }
        $summary = $summary['result'];

        return $this->successResponse($summary, 'Summary fetched successfully');
    }

    public function index(GetReservationRequest $request)
    {
        $filters = $request->validated();

        $data = $this->service->getAll($filters);
        if ($data['error']) {
            return $this->errorResponse($data['error'], null, $data['code']);
        }
        $data = $data['result'];

        return $this->paginatedResponse($data, 'Reservations fetched successfully');
    }

    public function listAllProperties($reservationId)
    {
        $listAllProperties = $this->service->listAllProperties($reservationId);
        if ($listAllProperties['error']) {
            return $this->errorResponse($listAllProperties['error'], null, $listAllProperties['code']);
        }
        $listAllProperties = $listAllProperties['result'];

        return $this->successResponse($listAllProperties, 'Properties fetched successfully');
    }
    
    public function store(StoreReservationRequest $request)
    {
        $data = $request->validated();

        $store = $this->service->store($data);
        if ($store['error']) {
            return $this->errorResponse($store['error'], null, $store['code']);
        }
        $store = $store['result'];

        return $this->successResponse($store, 'Reservation created successfully');
    }
    
    public function update(UpdateReservationRequest $request, $id)
    {
        $data = $request->validated();

        $update = $this->service->update($id, $data);
        if ($update['error']) {
            return $this->errorResponse($update['error'], null, $update['code']);
        }
        $update = $update['result'];

        $updateReservation = $this->successResponse($update, 'Reservation updated successfully');

        return $updateReservation;
    }
    
    public function getById($id)
    {
        $getById = $this->service->getById($id);
        if ($getById['error']) {
            return $this->errorResponse($getById['error'], null, $getById['code']);
        }
        $getById = $getById['result'];

        return $this->successResponse($getById, 'Reservation fetched successfully');
    }
    
    public function getProspect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => "nullable|string|max:255",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $listProspect = $this->service->getProspect($request->search);
        if ($listProspect['error']) {
            return $this->errorResponse($listProspect['error'], null, $listProspect['code']);
        }
        $listProspect = $listProspect['result'];

        return $this->successResponse($listProspect, 'Prospect fetched successfully');
    }
    
    public function getProperties()
    {
        $data = $this->service->getProperties();
        if ($data['error']) {
            return $this->errorResponse($data['error'], null, $data['code']);
        }
        $data = $data['result'];

        return $this->successResponse($data, 'Properties fetched successfully');
    }

    public function delete($id)
    {
        $delete = $this->service->delete($id);
        if ($delete['error']) {
            return $this->errorResponse($delete['error'], null, $delete['code']);
        }
        $delete = $delete['result'];

        return $this->successResponse($delete, 'Reservation deleted successfully');
    }

}
