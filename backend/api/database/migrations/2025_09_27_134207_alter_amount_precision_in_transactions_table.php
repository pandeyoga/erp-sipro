<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE transactions ALTER COLUMN amount TYPE numeric(18,2)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE transactions ALTER COLUMN amount TYPE numeric(10,2)');
    }
};
