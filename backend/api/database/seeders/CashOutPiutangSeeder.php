<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashOutPiutangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $id = DB::table('cash_out_categories')->insertGetId([
            'id' => Str::uuid(),
            'name' => "Pembayaran Piutang",
            'created_at' => now(),
            'updated_at' => now(),
            'code' => 'pembayaran-piutang',
        ]);

        DB::table('cash_out_sub_categories')->insert([
            'id' => Str::uuid(),
            'category_id' => $id,
            'name' => "Pembayaran Piutang",
            'created_at' => now(),
            'updated_at' => now(),
            'code' => 'pembayaran-piutang.pembayaran-piutang',
        ]);
    }
}
