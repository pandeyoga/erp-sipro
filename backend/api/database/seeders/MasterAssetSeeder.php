<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use App\Models\AssetSubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1-210 Tanah -
        // 1-220 Bangunan -
        // 1-230 Kendaraan -
        // 1-240 Peralatan & Perlengkapan -
        // 1-250 Surat Berharga -
        $data = [
            [
                'code' => '1-210',
                'name' => 'Tanah',
                'has_depreciation' => false,
                'children' => 'Tanah'
            ],
            [
                'code' => '1-220',
                'name' => 'Bangunan',
                'has_depreciation' => true,
                'children' => 'Bangunan'
            ],
            [
                'code' => '1-230',
                'name' => 'Kendaraan',
                'has_depreciation' => true,
                'children' => 'Kendaraan'
            ],
            [
                'code' => '1-240',
                'name' => 'Peralatan & Perlengkapan',
                'has_depreciation' => true,
                'children' => 'Peralatan & Perlengkapan'
            ],
            [
                'code' => '1-250',
                'name' => 'Surat Berharga',
                'has_depreciation' => false,
                'children' => 'Surat Berharga'
            ]
        ];

        foreach ($data as $item) {
            $category = AssetCategory::create([
                'name' => $item['name'],
                'code' => $item['code'],
                'has_depreciation' => $item['has_depreciation'],
            ]);

            $subCategory = AssetSubCategory::create([
                'asset_category_id' => $category->id,
                'name' => $item['children'],
            ]);
        }

    }
}
