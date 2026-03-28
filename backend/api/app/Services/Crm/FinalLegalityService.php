<?php

namespace App\Services\Crm;

use App\Repositories\Crm\FinalLegalityRepository;
use Carbon\Carbon;

class FinalLegalityService
{
    public function __construct(
            protected FinalLegalityRepository $repository,
            protected LeadService $leadService
        ) {
        $this->updateFinishedRetention();
    }

    public function summary()
    {
        $summary = $this->repository->groupedByStatus();
        if ($summary['error']) {
            return $summary;
        }
        $summary = $summary['result'];

        // mapping agar statusnya jadi key
        $mapping = collect($summary)->mapWithKeys(function ($item) {
            return [$item['status'] => $item['total']];
        });

        // input, verification, completed
        $data =  [
            [
                'status' => 'pending',
                'total' => isset($mapping['pending']) ? $mapping['pending'] : 0
            ],
            [
                'status' => 'bast',
                'total' => isset($mapping['bast']) ? $mapping['bast'] : 0
            ],
            [
                'status' => 'retention',
                'total' => isset($mapping['retention']) ? $mapping['retention'] : 0
            ],
            [
                'status' => 'complete',
                'total' => isset($mapping['complete']) ? $mapping['complete'] : 0
            ]
        ];
        
        return [
            'error' => false,
            'code' => 200,
            'result' => $data
        ];
    }

    public function index($request)
    {
        $finalLegalities = $this->repository->index($request);
        if ($finalLegalities['error']) {
            return $finalLegalities;
        }
        $finalLegalities = $finalLegalities['result'];

        $items = collect($finalLegalities->items())->map(function ($item) {
            $status = ucfirst(str_replace('_', ' ', $item->status));
            return [
                'id' => $item->id,
                'status' => $status,
                'name' => $item->name,
                'phone' => $item->phone,
                'notes' => $item->notes,
                'property_unit_id' => $item->property_unit_id,
                'property_unit' => isset(config('setting.dummy_property')[$item->property_unit_id]) ? config('setting.dummy_property')[$item->reservations->property_unit_id]['name'] : '-',
                'duration' => Carbon::now()->startOfDay()->diffInDays(Carbon::parse($item->created_at)->startOfDay(), true) . ' days',
                'created_at' => date('Y-m-d', strtotime($item->created_at)),
            ];
        });

        $finalLegalities->setCollection($items);

        return [
            'error' => null,
            'result' => $finalLegalities,
            'code' => 200
        ];
    }

    public function create($request)
    {
        $create = $this->repository->create($request);
        if ($create['error']) {
            return $create;
        }
        $create['result'] = $create['result'];

        return [
            'error' => null,
            'result' => $create['result'],
            'code' => 201
        ];
    }

    public function update($request, $id)
    {
        $update = $this->repository->update($request, $id);
        if ($update['error']) {
            return $update;
        }
        $update['result'] = $update['result'];

        return [
            'error' => null,
            'result' => $update['result'],
            'code' => 200
        ];
    }

    public function getById($id)
    {
        $finalLegality = $this->repository->getById($id);
        if ($finalLegality['error']) {
            return $finalLegality;
        }
        $finalLegality = $finalLegality['result'];

        $formatted = [
            'id' => $finalLegality->id,
            'lead_id' => $finalLegality->lead_id,
            'name' => $finalLegality->name,
            'phone' => $finalLegality->phone,
            'email' => $finalLegality->email,
            'bast_document' => url($finalLegality->bast_document),
            'bast_hanover_photo' => url($finalLegality->bast_hanover_photo),
            'bast_date' => $finalLegality->bast_date,
            'retention_document' => url($finalLegality->retention_document),
            'retention_hanover_photo' => url($finalLegality->retention_hanover_photo),
            'retention_start_date' => $finalLegality->retention_start_date,
            'retention_end_date' => $finalLegality->retention_end_date,
            'notes' => $finalLegality->notes,
            'created_at' => date('Y-m-d', strtotime($finalLegality->created_at)),
        ];

        return [
            'error' => null,
            'result' => $formatted,
            'code' => 200
        ];
    }

    private function updateFinishedRetention()
    {
        $update = $this->repository->updateFinishedRetention();
        if ($update['error']) {
            return $update;
        }
        $update['result'] = $update['result'];

        return [
            'error' => null,
            'result' => $update['result'],
            'code' => 200
        ];
    }

    // getLeadCompletedPayment
    public function getLeadCompletedPayment()
    {
        $finalLegality = $this->repository->getLeadCompletedPayment();
        if ($finalLegality['error']) {
            return $finalLegality;
        }
        $finalLegality = $finalLegality['result'];

        return [
            'error' => null,
            'result' => $finalLegality,
            'code' => 200
        ];
    }

    // delete
    public function delete($id)
    {
        $finalLegality = $this->repository->getById($id);
        if ($finalLegality['error']) {
            return $finalLegality;
        }
        $finalLegality = $finalLegality['result'];

        $delete = $this->leadService->delete($finalLegality->lead_id);
        if ($delete['error']) {
            return $delete;
        }
        $delete['result'] = $delete['result'];

        return [
            'error' => null,
            'result' => $delete['result'],
            'code' => 200
        ];
    }
}