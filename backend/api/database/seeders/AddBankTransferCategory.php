<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddBankTransferCategory extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryId = DB::table('cash_in_categories')->where('name', 'Bank Transfer')->first()?->id;

        if (!$categoryId) {
            $categoryId = DB::table('cash_in_categories')->insertGetId([
                    'id' => Str::uuid(),
                    'name' => "Bank Transfer",
                    'is_property_related' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }
        
        $subCategoryId = DB::table('cash_in_sub_categories')->where('name', 'Bank Transfer')->first()?->id;

        if (!$subCategoryId) {
            $subCategoryId = DB::table('cash_in_sub_categories')->insertGetId([
                    'id' => Str::uuid(),
                    'category_id' => $categoryId,
                    'name' => "Bank Transfer",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        $subSubGroupId = DB::table('cash_in_sub_sub_groups')->where('name', 'Bank Transfer')->first()?->id;

        if (!$subSubGroupId) {
            $subSubGroupId = DB::table('cash_in_sub_sub_groups')->insertGetId([
                    'id' => Str::uuid(),
                    'name' => "Bank Transfer",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        $subSubCategoryId = DB::table('cash_in_sub_sub_categories')->where('name', 'Bank Transfer')->first()?->id;

        if (!$subSubCategoryId) {
            $subSubCategoryId = DB::table('cash_in_sub_sub_categories')->insertGetId([
                    'id' => Str::uuid(),
                    'sub_sub_group_id' => $subSubGroupId,
                    'sub_category_id' => $subCategoryId,
                    'name' => "Bank Transfer",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        $outCategoryId = DB::table('cash_out_categories')->where('name', 'Bank Transfer')->first()?->id;

        if (!$outCategoryId) {
            $outCategoryid = DB::table('cash_out_categories')->insertGetId([
                'id' => Str::uuid(),
                'name' => "Bank Transfer",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $outSubCategoryId = DB::table('cash_out_sub_categories')->where('name', 'Bank Transfer')->first()?->id;

        if (!$outSubCategoryId) {
            $outSubCategoryId = DB::table('cash_out_sub_categories')->insertGetId([
                'id' => Str::uuid(),
                'category_id' => $outCategoryid,
                'name' => "Bank Transfer",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
