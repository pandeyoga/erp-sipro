<?php
        
namespace App\Repositories\Property;

use App\Models\Construction;
use App\Models\SubContractor;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
class SubContractorRepository
{
    public function index($request)
    {
        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 10;

        $search = $request->search ?? null;
        $subCon = SubContractor::leftJoin('constructions', 'sub_contractors.id', '=', 'constructions.sub_contractor_id')
            ->select(
                'sub_contractors.id',
                'sub_contractors.name as sub_contractor_name',
                DB::raw("COUNT(CASE WHEN constructions.status != 'done' THEN 1 END) as total_in_progress_constructions"),
                DB::raw("COUNT(CASE WHEN constructions.status = 'done' THEN 1 END) as total_done_constructions"),
                DB::raw("COUNT(CASE WHEN constructions.status = 'done' AND constructions.actual_end_date < constructions.estimated_end_date THEN 1 END) as on_time_constructions"),
                'sub_contractors.created_at as added_at'
            )
            ->groupBy('sub_contractors.id', 'sub_contractors.name')
            ->orderBy('sub_contractors.name', 'asc')
            ->when($search, function ($query) use ($search) {
                return $query->where('sub_contractors.name', 'ilike', '%' . $search . '%');
            })
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'error' => null,
            'result' => $subCon,
            'code' => 200
        ];
    }
    public function store($data)
    {
        try {
            DB::beginTransaction();
            SubContractor::create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }

        return [
            'error' => null,
            'result' => null,
            'code' => 200
        ];
    }

    public function getById($id)
    {
        $subCon = SubContractor::where('sub_contractors.id', $id)
            ->leftJoin('constructions', 'sub_contractors.id', '=', 'constructions.sub_contractor_id')
            ->select(
                'sub_contractors.id',
                'sub_contractors.name as sub_contractor_name',
                DB::raw("COUNT(CASE WHEN constructions.status != 'done' THEN 1 END) as total_in_progress_constructions"),
                DB::raw("COUNT(CASE WHEN constructions.status = 'done' THEN 1 END) as total_done_constructions"),
                DB::raw("COUNT(CASE WHEN constructions.status = 'done' AND constructions.actual_end_date < constructions.estimated_end_date THEN 1 END) as on_time_constructions"),
                'sub_contractors.created_at as added_at'
            )
            ->groupBy('sub_contractors.id', 'sub_contractors.name')
            ->first();

        if (!$subCon) {
            return [
                'error' => 'Sub Contractor not found',
                'result' => null,
                'code' => 404
            ];
        }
        return [
            'error' => null,
            'result' => $subCon,
            'code' => 200
        ];
    }

    public function update($data, $id)
    {
        $subCon = SubContractor::where('id', $id)->first();
        if (!$subCon) {
            return [
                'error' => 'Sub Contractor not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();
            $subCon->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }

        return [
            'error' => null,
            'result' => null,
            'code' => 200
        ];
    }

    public function destroy($id)
    {   
        $subCon = SubContractor::where('id', $id)->first();
        if (!$subCon) {
            return [
                'error' => 'Sub Contractor not found',
                'result' => null,
                'code' => 404
            ];
        }

        // Check if unit has any constructions
        $constructions = Construction::where('sub_contractor_id', $id)->get();
        if ($constructions->count() > 0) {
            return [
                'error' => 'Sub Contractor has constructions',
                'result' => null,
                'code' => 400
            ];
        }

        try {
            DB::beginTransaction();
            $subCon->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }

        return [
            'error' => null,
            'result' => null,
            'code' => 200
        ];
    }
}