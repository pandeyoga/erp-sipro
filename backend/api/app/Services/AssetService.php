<?php

namespace App\Services;

use App\Repositories\AssetRepository;
use Carbon\Carbon;

class AssetService
{
    public function __construct(protected AssetRepository $repository) {}

    public function listCategory() {
        return $this->repository->listCategory();
    }

    public function listSubCategory($categoryId) {
        return $this->repository->listSubCategory($categoryId);
    }

    public function index($data) {
        $mappedData = [
            'category_id' => $data['category_id'] ?? null,
            'sub_category_id' => $data['sub_category_id'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'search' => $data['search'] ?? null,
            'page' => $data['page'] ?? 1,
            'per_page' => $data['per_page'] ?? 10,
        ];

        $data = $this->repository->index($mappedData);

        $mappedItem = collect($data->items())->map(function ($item) {
            $remainingUsefulLife = $this->calculateRemainingUsefulLife($item->acquisition_date, $item->useful_life ?? 0);
            return [
                'id' => $item->id,
                'acquisition_date' => $item->acquisition_date,
                'registration_number' => $item->registration_number,
                'category_name' => $item->category_name,
                'sub_category_name' => $item->sub_category_name,
                'name' => $item->name,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'price' => (int) $item->price,
                'remaining_price' => $item->has_depreciation ? $remainingUsefulLife * $item->depreciation_rate : (int) $item->price,
                'useful_life' => $item->useful_life,
                'remaining_useful_life' => $remainingUsefulLife,
            ];
        });

        $data->setCollection($mappedItem);

        return $data;
    }

    public function create($data) {
        $category = $this->repository->getCategoryById($data['category_id']);
        $nomorUrut = $this->repository->getNomorUrut($category->id);
        $regNumber = $category->code . '/' . $nomorUrut + 1 . '/' . date('Y');

        $mapping = [
            'registration_number' => $regNumber,
            'category_id' => $category->id,
            'sub_category_id' => $data['sub_category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'quantity' => $data['quantity'],
            'price' => $data['price'],
            'acquisition_date' => $data['acquisition_date'],
            'useful_life' => $category->has_depreciation ? $data['useful_life'] : null,
            'has_depreciation' => $category->has_depreciation,
            'depreciation_rate' => $category->has_depreciation ? $data['price'] / $data['useful_life'] : 0
        ];

        return $this->repository->create($mapping);
    }

    public function update($id, $data) {
        $category = $this->repository->getCategoryById($data['category_id']);
        $mapping = [
            'category_id' => $category->id,
            'sub_category_id' => $data['sub_category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'quantity' => $data['quantity'],
            'price' => $data['price'],
            'acquisition_date' => $data['acquisition_date'],
            'useful_life' => $category->has_depreciation ? $data['useful_life'] : null,
            'has_depreciation' => $category->has_depreciation,
            'depreciation_rate' => $category->has_depreciation ? $data['price'] / $data['useful_life'] : 0
        ];

        return $this->repository->update($id, $mapping);
    }

    public function show($id) {
        $data = $this->repository->show($id);
        $remainingUsefulLife = $this->calculateRemainingUsefulLife($data->acquisition_date, $data->useful_life ?? 0);
        return [
                'id' => $data->id,
                'acquisition_date' => $data->acquisition_date,
                'registration_number' => $data->registration_number,
                'category_name' => $data->category_name,
                'sub_category_name' => $data->sub_category_name,
                'name' => $data->name,
                'description' => $data->description,
                'quantity' => $data->quantity,
                'price' => (int) $data->price,
                'remaining_price' => $data->has_depreciation ? $remainingUsefulLife * $data->depreciation_rate : (int) $data->price,
                'useful_life' => $data->useful_life,
                'remaining_useful_life' => $remainingUsefulLife,
            ];
    }

    public function delete($id) {
        return $this->repository->delete($id);
    }

    private function calculateRemainingUsefulLife($acquisitionDate, $usefulLife)
    {
        $acquisitionDate = Carbon::parse($acquisitionDate);
        $now = now();

        // hitung selisih bulan
        $months = ($now->year - $acquisitionDate->year) * 12 
                + ($now->month - $acquisitionDate->month);

        // kalau sudah masuk bulan berikutnya (apapun tanggalnya), tambahkan 1
        if ($now->greaterThan($acquisitionDate)) {
            $months++;
        }

        $remaining = $usefulLife - $months;

        return max($remaining, 0); // biar ga minus
    }
}