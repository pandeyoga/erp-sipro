<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class generateCodeCashflow extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIn = DB::table('cash_in_categories')->get();

        foreach ($categoryIn as $key => $value) {
            $categoryCode = Str::slug($value->name);
            DB::table('cash_in_categories')->where('id', $value->id)->update(['code' => $categoryCode]);
            $subCategoryIn = DB::table('cash_in_sub_categories')->where('category_id', $value->id)->get();
    
            foreach ($subCategoryIn as $key => $value) {
                $subCategoryCode = $categoryCode . '.' . Str::slug($value->name);
                DB::table('cash_in_sub_categories')->where('id', $value->id)->update(['code' => $subCategoryCode]);

                $subSubCategoryIn = DB::table('cash_in_sub_sub_categories')->where('sub_category_id', $value->id)->get();

                foreach ($subSubCategoryIn as $key => $value) {
                    $subSubGroup = DB::table('cash_in_sub_sub_groups')->where('id', $value->sub_sub_group_id)->first();
                    $subSubGroupCode = $subCategoryCode . '.' . Str::slug($subSubGroup->name);
                    DB::table('cash_in_sub_sub_groups')->where('id', $subSubGroup->id)->update(['code' => $subSubGroupCode]);
                    $subSubCategoryCode = $subSubGroupCode . '.' . Str::slug($value->name);
                    DB::table('cash_in_sub_sub_categories')->where('id', $value->id)->update(['code' => $subSubCategoryCode]);
                }
            }
        }

        $categoryOut = DB::table('cash_out_categories')->get();

        foreach ($categoryOut as $key => $value) {
            $categoryCode = Str::slug($value->name);
            DB::table('cash_out_categories')->where('id', $value->id)->update(['code' => $categoryCode]);
            $subCategoryOut = DB::table('cash_out_sub_categories')->where('category_id', $value->id)->get();
    
            foreach ($subCategoryOut as $key => $value) {
                $subCategoryCode = $categoryCode . '.' . Str::slug($value->name);
                DB::table('cash_out_sub_categories')->where('id', $value->id)->update(['code' => $subCategoryCode]);
            }
        }
    }
}
