<?php
        
namespace App\Repositories\Crm;

use App\Models\Contact;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;

class ContactRepository
{

    public function getAll($page, $perPage, $search) : array
    {
        try {
            $subquery = Contact::when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                      ->orWhere('email', 'ilike', "%{$search}%")
                      ->orWhere('phone', 'ilike', "%{$search}%")
                      ->orWhere('location', 'ilike', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->select(
                'id',
                'name',
                'email',
                'phone',
                'location',
                'created_at',
                DB::raw('COUNT(*) OVER (PARTITION BY phone) as duplicates'),
                DB::raw('LEAST(
                    ROW_NUMBER() OVER (PARTITION BY phone ORDER BY created_at)
                 ) as is_original')
            );
        
            $data = DB::table(DB::raw("({$subquery->toSql()}) as sub"))
                ->mergeBindings($subquery->getQuery())
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function export($startDate, $endDate)
    {
        $result = Contact::orderBy('created_at', 'desc')
            ->select(
                'id',
                'name',
                'email',
                'phone',
                'location',
                'created_at'
            )
            ->whereBetween('created_at', [$startDate, $endDate])->get();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];

    }

    // isLeads
    public function isLeads($id) : array
    {
        $isExist = Lead::where('contact_id', $id)->exists();

        return [
            'error' => null,
            'code' => 200,
            'result' => $isExist
        ];
    }

    public function import($data) : array
    {
        try {
            DB::beginTransaction();
            $contact = Contact::insert(collect($data)->map(function ($item) {
                $item['created_at'] = now();
                $item['updated_at'] = now();
                return $item;
            })->toArray());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $contact
        ];
    }

    public function getAllForSelect($search) : array
    {
        try {
            $select = Contact::when($search, function ($query, $search) {
                return $query->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'ilike', "%{$search}%")
                    ->orWhere('location', 'ilike', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->select(
                'id',
                'name',
                'phone',
                // DB::raw('COUNT(*) OVER (PARTITION BY phone) as duplicates'),
                DB::raw('LEAST(
                    ROW_NUMBER() OVER (PARTITION BY phone ORDER BY created_at)
                 ) as is_original')
                )
            ->limit(20)
            ->get();
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $select
        ];
    }

    public function create($data) : array
    {
        try {
            DB::beginTransaction();
            $data['created_at'] = $data['date'] ?? now();
            unset($data['date']);
            $contact = Contact::create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $contact
        ];
    }

    public function get($id) : array
    {
        $data = Contact::where('id', $id)->first();

        return [
            'error' => null,
            'code' => 200,
            'result' => $data
        ];
    }

    public function update($id, $data) : array
    {
        try {
            DB::beginTransaction();
            $contact = Contact::where('id', $id)->first();
            if (!$contact) {
                DB::rollBack();
                return [
                    'error' => "Contact not found",
                    'code' => 404,
                    'result' => null,
                ];
            }
            
            $contact->name = $data['name'] ?? $contact->name;
            $contact->email = $data['email'] ?? $contact->email;
            $contact->phone = $data['phone'] ?? $contact->phone;
            $contact->location = $data['location'] ?? $contact->location;
            if (isset($data['date'])) {
                $contact->updated_at = $data['date'] ?? now();
            }
            $contact->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $contact
        ];
    }

    public function delete($id) : array
    {
        try {
            DB::beginTransaction();
            $contact = Contact::where('id', $id)->first();
            if (!$contact) {
                DB::rollBack();
                return [
                    'error' => "Contact not found",
                    'code' => 404,
                    'result' => null,
                ];
            }
            $contact->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'code' => 500,
                'result' => null
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'result' => $contact
        ];
    }
}