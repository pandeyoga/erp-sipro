<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\Assets;
use App\Models\AssetSubCategory;
use Illuminate\Support\Facades\DB;

class AssetRepository
{
    public function listCategory() {
        return AssetCategory::select('id', 'name')->get();
    }

    public function listSubCategory($categoryId) {
        return AssetSubCategory::select('id', 'name')
            ->where('asset_category_id', $categoryId)
            ->get();
    }

    public function getCategoryById($id) {
        return AssetCategory::find($id);
    }

    public function getNomorUrut($categoryId) {
        return Assets::where('category_id', $categoryId)->count();
    }

    public function index($data) {
        return Assets::join('asset_categories', 'asset_categories.id', '=', 'assets.category_id')
            ->join('asset_sub_categories', 'asset_sub_categories.id', '=', 'assets.sub_category_id')
            ->select(
                'assets.id',
                'assets.registration_number',
                'asset_categories.name as category_name',
                'asset_sub_categories.name as sub_category_name',
                'assets.name',
                'assets.description',
                'assets.quantity',
                'assets.price',
                'assets.acquisition_date',
                'assets.useful_life',
                'assets.has_depreciation',
                'assets.depreciation_rate'
            )
            ->where(function ($query) use ($data) {
                if (!empty($data['category_id'])) {
                    $query->where('assets.category_id', $data['category_id']);
                }
                if (!empty($data['sub_category_id'])) {
                    $query->where('assets.sub_category_id', $data['sub_category_id']);
                }
                if (!empty($data['start_date'])) {
                    $query->where('assets.acquisition_date', '>=', $data['start_date']);
                }
                if (!empty($data['end_date'])) {
                    $query->where('assets.acquisition_date', '<=', $data['end_date']);
                }
                if (!empty($data['search'])) {
                    $query->where('assets.name', 'ilike', '%' . $data['search'] . '%');
                }
            })
            ->orderBy('assets.acquisition_date', 'desc')
            ->paginate($data['per_page']);
    }

    public function create($data) {
        try {
            DB::beginTransaction();
            $asset = Assets::create($data);
            DB::commit();
            return $asset;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, $data) {
        try {
            DB::beginTransaction();
            $asset = Assets::find($id);
            $asset->update($data);
            DB::commit();
            return $asset;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show($id) {
        return Assets::join('asset_categories', 'asset_categories.id', '=', 'assets.category_id')
            ->join('asset_sub_categories', 'asset_sub_categories.id', '=', 'assets.sub_category_id')
            ->select(
                'assets.id',
                'assets.registration_number',
                'asset_categories.name as category_name',
                'asset_sub_categories.name as sub_category_name',
                'assets.name',
                'assets.description',
                'assets.quantity',
                'assets.price',
                'assets.acquisition_date',
                'assets.useful_life',
                'assets.has_depreciation',
                'assets.depreciation_rate'
            )
            ->where('assets.id', $id)
            ->first();
    }

    public function delete($id) {
        try {
            DB::beginTransaction();
            $asset = Assets::find($id);
            $asset->delete();
            DB::commit();
            return $asset;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}