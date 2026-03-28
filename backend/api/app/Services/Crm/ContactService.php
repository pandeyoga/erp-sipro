<?php
        
namespace App\Services\Crm;

use App\Repositories\Crm\ContactRepository;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ContactService
{
    public function __construct(protected ContactRepository $repository) {}

    public function getAllContacts($reqParams) : array
    {
        $data = $this->repository->getAll($reqParams['page'], $reqParams['per_page'], $reqParams['search']);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];

        $items = collect($data->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'phone' => $item->phone,
                'location' => $item->location,
                'is_duplicate' => $item->is_original == 1 ? false : true,
                'created_at' => $item->created_at
            ];
        });
        $data->setCollection($items);

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function export($startDate, $endDate)
    {

        $data = $this->repository->export($startDate, $endDate);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];
        
        $spreadsheet = new Spreadsheet();

        /**
         * SHEET 1 : Ringkasan
         */
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Contacts Data');

        $row = 1;

        $summarySheet->setCellValue("A{$row}", "Name");
        $summarySheet->setCellValue("B{$row}", "Email");
        $summarySheet->setCellValue("C{$row}", "Phone");
        $summarySheet->setCellValue("D{$row}", "Location");
        $summarySheet->setCellValue("E{$row}", "Created At");

        

        $row++;
        foreach ($data as $value) {
            $summarySheet->setCellValue("A{$row}", $value->name);
            $summarySheet->setCellValue("B{$row}", $value->email);
            $summarySheet->setCellValue("C{$row}", $value->phone);
            $summarySheet->setCellValue("D{$row}", $value->location);
            $summarySheet->setCellValue("E{$row}", $value->created_at);
            $row++;
        }

        // auto size
        foreach (range('A', 'E') as $col) {
            $summarySheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [
            'error' => null,
            'status' => 200,
            'result' => $spreadsheet
        ];
    }

    public function isLeads($id) : array
    {
        $isLead = $this->repository->isLeads($id);
        if ($isLead['error']) {
            return $isLead;
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $isLead['result']
        ];
    }

    public function importContacts($file) : array
    {
        $collection = (new FastExcel)->import($file)->map(function ($row) {
            return [
                'id' => Str::uuid(),
                'name' => $row['Nama'],
                'phone' => $row['Nomor telpon'],
            ];
        });

        // remove null phone number
        $collection = $collection->filter(function ($item) {
            return $item['phone'] != null && $item['name'] != null;
        });

        $collection = $collection->unique('phone');
        
        $data = $this->repository->import($collection->toArray());
        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function getAllContactForSelect($search) : array
    {
        $data = $this->repository->getAllForSelect($search);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];

        $data = collect($data)->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'phone' => $item->phone,
                'is_duplicate' => $item->is_original == 1 ? false : true
            ];
        });

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function createContact($data) : array
    {
        $create = $this->repository->create($data);
        if ($create['error']) {
            return $create;
        }
        $create = $create['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => [
                'id' => $create->id,
                'name' => $create->name,
                'email' => $create->email,
                'phone' => $create->phone,
                'location' => $create->location,
                'created_at' => $create->created_at
            ]
        ];
    }

    // getContact
    public function getContact($id) : array
    {
        $data = $this->repository->get($id);
        if ($data['error']) {
            return $data;
        }
        $data = $data['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => [
                'id' => $data->id,
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
                'location' => $data->location,
                'created_at' => $data->created_at
            ]
        ];
    }

    // updateContact
    public function updateContact($id, $data) : array
    {
        $update = $this->repository->update($id, $data);
        if ($update['error']) {
            return $update;
        }
        $update = $update['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => [
                    'id' => $update->id,
                    'name' => $update->name,
                    'email' => $update->email,
                    'phone' => $update->phone,
                    'location' => $update->location,
                    'updated_at' => $update->updated_at
                ]
            ];
    }

    public function deleteContact($id) : array
    {
        $delete = $this->repository->delete($id);
        if ($delete['error']) {
            return $delete;
        }
        $delete = $delete['result'];

        return [
            'error' => null,
            'code' => 200,
            'result' => $delete
        ];
    }
}