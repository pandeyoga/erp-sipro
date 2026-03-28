<?php
        
namespace App\Repositories\Property;

use App\Models\Unit;
use Illuminate\Support\Facades\DB;
class UnitRepository
{
    public function index($request)
    {
        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 10;

        $search = $request->search ?? null;
        $units = Unit::orderBy('created_at', 'desc')
            ->select(
                'id',
                'type',
                'land_area',
                'building_area',
            )
            ->when($search, function ($query) use ($search) {
                return $query->where('type', 'ilike', '%' . $search . '%');
            })
            ->paginate($perPage, ['*'], 'page', $page);
        return [
            'error' => null,
            'result' => $units,
            'code' => 200
        ];
    }
    public function store($data)
    {
        try {
            DB::beginTransaction();
            Unit::create($data);
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
        $unit = Unit::where('id', $id)
            ->select(
                'id',
                'type',
                'land_area',
                'building_area',
                'notes',
            )
            ->first();
        if (!$unit) {
            return [
                'error' => 'Unit not found',
                'result' => null,
                'code' => 404
            ];
        }
        return [
            'error' => null,
            'result' => $unit,
            'code' => 200
        ];
    }

    public function update($data, $id)
    {
        $unit = Unit::where('id', $id)->first();
        if (!$unit) {
            return [
                'error' => 'Unit not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();
            $unit->update($data);
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
        $unit = Unit::where('id', $id)->first();
        if (!$unit) {
            return [
                'error' => 'Unit not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();
            $unit->delete();
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

    // get All Unit
    public function getAll()
    {
        $units = Unit::get()->map(function ($unit) {
            return [
                'id' => $unit->id,
                'type' => $unit->type,
                'land_area' => $unit->land_area,
                'building_area' => $unit->building_area,
            ];
        });
        
        return [
            'error' => null,
            'result' => $units,
            'code' => 200
        ];
    }
}