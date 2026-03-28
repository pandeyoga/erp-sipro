<?php
        
namespace App\Services\Crm;

use App\Models\Lead;
use App\Repositories\Crm\LeadPaymentRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeadPaymentService
{
    public function __construct(
        protected LeadPaymentRepository $repository,
        protected LeadService $leadService
    ) {}

    // getLeadCompletedDocument
    public function getLeadCompletedDocument()
    {
        $data = $this->repository->getLeadCompletedDocument();
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];
        
        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    // getSummary
    public function getSummary()
    {
        $leadsGroup = $this->repository->getLeadPaymentGroup();
        if ($leadsGroup['error']) {
            return $leadsGroup;
        }
        $leadsGroup = $leadsGroup['result'];
        
        // mapping agar statusnya jadi key
        $mapping = collect($leadsGroup)->mapWithKeys(function ($item) {
            return [$item['status'] => $item['total']];
        });

        $data =  [
            [
                'status' => 'proses_bank',
                'total' => isset($mapping['proses_bank']) ? $mapping['proses_bank'] : 0
            ],
            [
                'status' => 'sp3k',
                'total' => isset($mapping['sp3k']) ? $mapping['sp3k'] : 0
            ],
            [
                'status' => 'akad_kredit',
                'total' => isset($mapping['akad_kredit']) ? $mapping['akad_kredit'] : 0
            ],
            [
                'status' => 'cash_keras',
                'total' => isset($mapping['cash_keras']) ? $mapping['cash_keras'] : 0
            ],
            [
                'status' => 'cash_bertahap',
                'total' => isset($mapping['cash_bertahap']) ? $mapping['cash_bertahap'] : 0
            ]
        ];

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    // getBankList
    public function getBankList()
    {
        $data = $this->repository->getBankList();
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];
        
        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function create($request)
    {
        // check lead harus statusnya document_and_legal_process dan document nya harus completed
        $isValid = $this->repository->checkValid($request['lead_id']);
        if ($isValid['error']) {
            return $isValid;
        }
        if ($request['payment_type'] == 'cash_keras' || $request['payment_type'] == 'cash_bertahap') {
            $create = $this->repository->createCash($request);
        } else {
            $create = $this->repository->createKpr($request);
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => null
        ];
    }

    // getAll
    public function getAll($request)
    {
        $data = $this->repository->getAll($request);
        if ($data['error']) {
            return $data;
        }

        $data = $data['result'];
    
        $items = collect($data->items())->map(function ($item) {
            if ($item->stop_date) {
                $startDate = Carbon::parse($item->created_at);
                $endDate = Carbon::parse($item->stop_date);
                $duration = (int) $startDate->diffInDays($endDate, true) . ' days';
            } else {
                $startDate = Carbon::parse($item->created_at);
                $duration = (int) Carbon::now()->startOfDay()->diffInDays($startDate, true) . ' days';
            }
            $status = ucfirst(str_replace('_', ' ', $item->status));
            return [
                'id' => $item->id,
                'status' => $status,
                'name' => $item->name,
                'phone' => $item->phone,
                'notes' => $item->notes,
                'duration' => $duration,
                'created_at' => date('Y-m-d', strtotime($item->created_at)),
            ];
        });

        $data->setCollection($items);

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    // update
    public function update($request, $id)
    {
        $checkList = [];
        foreach ($request as $key => $value) {
            if (strpos($key, 'checklist_') !== false) {
                $checkList[$key] = $value;
            }
        }
        $update = $this->repository->update($request, $checkList, $id);
        if ($update['error']) {
            return $update;
        }
        $update = $update['result'];



        return [
            'error' => null,
            'code' => 200,
            'result' => null
        ];
    }

    public function getById($id)
    {
        $data = $this->repository->getById($id);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];

        $formatted = [
           "id" =>  $data->id,
           "name" => $data->name,
           "phone" => $data->phone,
           "email" => $data->email,
           "sp3k_status" => $data->sp3k_status,
           "sp3k_document" => $data->sp3k_document ? url($data->sp3k_document) : null,
           "sp3k_bank" => $data->sp3k_bank,
           "sp3k_code" => $data->sp3k_code,
           "sp3k_date" => $data->sp3k_date,
           "sp3k_number" => $data->sp3k_number,
           "akad_kredit_status" => $data->akad_kredit_status,
           "akad_kredit_penandatanganan_document" => $data->akad_kredit_penandatanganan_document ? url($data->akad_kredit_penandatanganan_document) : null,
           'duration' => Carbon::now()->startOfDay()->diffInDays(Carbon::parse($data->created_at)->startOfDay(), true) . ' days',
           "status" => $data->status,
           "payment_type" => $data->payment_type,
           "notes" => $data->notes,
           "proposed_name_1" => $data->proposed_name_1,
           "proposed_name_2" => $data->proposed_name_2,
           "checklists" => collect($data->checklists)->map(function ($item) {
                $name = ucfirst(str_replace('_', ' ', $item->code));
               return [
                   'key' => "checklist_" . $item->code,
                   'name' => $name,
                   'checked' => $item->checked
               ];
           }),
        ];

        return [
            'error' => null,
            'code' => 200,
            'result' => $formatted
        ];
    }

    public function delete($id)
    {
        $payment = $this->repository->getById($id);
        if ($payment['error']) {
            return $payment;
        }
        $payment = $payment['result'];

        // $delete = $this->leadService->delete($payment->lead_id);

        $delete = $this->repository->delete($id);
        if ($delete['error']) {
            return $delete;
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => null
        ];
    }
}