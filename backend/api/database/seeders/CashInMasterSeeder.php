<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashInMasterSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Penjualan Rumah', 'Cash Keras', 'general', 'All in'],
            ['Penjualan Rumah', 'Cash Keras', 'penambahan spek', 'Hook'],
            ['Penjualan Rumah', 'Cash Keras', 'penambahan spek', 'Penambahan tanah'],
            ['Penjualan Rumah', 'Cash Keras', 'penambahan spek', 'Penambahan Spek bangunan'],
            ['Penjualan Rumah', 'Cash Keras', 'pembayaran bertahap', 'DP'],
            ['Penjualan Rumah', 'Cash Keras', 'pembayaran bertahap', 'Pelunasan'],
            ['Penjualan Rumah', 'Cash Bertahap', 'general', 'All in'],
            ['Penjualan Rumah', 'Cash Bertahap', 'penambahan spek', 'Hook'],
            ['Penjualan Rumah', 'Cash Bertahap', 'penambahan spek', 'Penambahan tanah'],
            ['Penjualan Rumah', 'Cash Bertahap', 'penambahan spek', 'Penambahan Spek bangunan'],
            ['Penjualan Rumah', 'Cash Bertahap', 'pembayaran bertahap', 'DP'],
            ['Penjualan Rumah', 'Cash Bertahap', 'pembayaran bertahap', 'Cicilan Pelunasan'],
            ['Penjualan Rumah', 'KPR', 'general', 'All In'],
            ['Penjualan Rumah', 'KPR', 'penambahan spek', 'Hook'],
            ['Penjualan Rumah', 'KPR', 'penambahan spek', 'Penambahan tanah'],
            ['Penjualan Rumah', 'KPR', 'penambahan spek', 'Penambahan Spek bangunan'],
            ['Penjualan Rumah', 'KPR', 'pencairan kpr', 'Pencairan AKAD'],
            ['Penjualan Rumah', 'KPR', 'pencairan kpr', 'Selisih KYG'],
            ['Penjualan Rumah', 'KPR', 'pencairan kpr', 'Retensi Sertifikat'],
            ['Penjualan Rumah', 'KPR', 'pencairan kpr', 'Retensi Air'],
            ['Penjualan Rumah', 'KPR', 'pencairan kpr', 'Restensi Listrik'],
            ['Penjualan Rumah', 'KPR', 'pencairan kpr', 'IMB/PBG'],
            ['Penjualan Rumah', 'KPR', 'pencairan kpr', 'Bestek'],
            ['Penjualan Rumah', 'KPR', 'pencairan kpr', 'JKK'],
            ['Penjualan Rumah', 'KPR', 'pencairan kpr', 'Retensi Bangunan'],
            ['Booking', 'biaya booking', 'general', 'biaya booking'],
            ['Pencarian SBUM', 'pencairan SBUM', 'general', 'pencairan SBUM'],
            ['Pinjaman', 'Pinjaman BPRS Mentari', 'general', 'Pinjaman BPRS Mentari'],
            ['Pinjaman', 'Pinjaman Bank KYG-PPL', 'general', 'Pinjaman Bank KYG-PPL'],
            ['Pinjaman', 'Pinjaman lainnya', 'general', 'Pinjaman lainnya'],
            ['Pemodalan', 'Pemodalan', 'general', 'Pemodalan'],
            ['Pendapatan bunga bank', 'bunga bank', 'general', 'bunga bank'],
            ['Pemasukan Lainya', 'lainya', 'general', 'lainya (manual)'],
        ];

        // truncate table
        DB::table('cash_in_categories')->truncate();
        DB::table('cash_in_sub_categories')->truncate();
        DB::table('cash_in_sub_sub_categories')->truncate();
        // truncate table cash_in_sub_sub_groups
        DB::table('cash_in_sub_sub_groups')->truncate();
        // cash_flow_ins
        DB::table('cash_flow_ins')->truncate();

        foreach ($data as [$category, $subCategory, $subSubGroup, $subSubCategory]) {
            $isPropertyRelated = $category === 'Penjualan Rumah';

            $categoryId = DB::table('cash_in_categories')->where('name', $category)->value('id');
            if (!$categoryId) {
                $categoryId = DB::table('cash_in_categories')->insertGetId([
                    'id' => Str::uuid(),
                    'name' => $category,
                    'is_property_related' => $isPropertyRelated,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $subCategoryId = DB::table('cash_in_sub_categories')
                ->where('name', $subCategory)
                ->where('category_id', $categoryId)
                ->value('id');

            if (!$subCategoryId) {
                $subCategoryId = DB::table('cash_in_sub_categories')->insertGetId([
                    'id' => Str::uuid(),
                    'category_id' => $categoryId,
                    'name' => $subCategory,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $subSubGroupId = DB::table('cash_in_sub_sub_groups')
                ->where('name', $subSubGroup)
                ->value('id');

            if (!$subSubGroupId) {
                $subSubGroupId = DB::table('cash_in_sub_sub_groups')->insertGetId([
                    'id' => Str::uuid(),
                    'name' => $subSubGroup,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $isCustomInput = $subSubCategory === 'lainya (manual)';
            if ($isCustomInput) {
                $subSubCategory = 'lainya';
            }

            $exists = DB::table('cash_in_sub_sub_categories')
                ->where('name', $subSubCategory)
                ->where('sub_sub_group_id', $subSubGroupId)
                ->where('sub_category_id', $subCategoryId)
                ->exists();

            if (!$exists) {
                DB::table('cash_in_sub_sub_categories')->insert([
                    'id' => Str::uuid(),
                    'sub_sub_group_id' => $subSubGroupId,
                    'sub_category_id' => $subCategoryId,
                    'name' => $subSubCategory,
                    'is_custom_input' => $isCustomInput,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
