<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // add column 
        // hook_additional_fee
        // additional_land_area_fee
        // additional_building_specifications_fee
        Schema::table('reservations', function (Blueprint $table) {
            $table->decimal('hook_additional_fee', 15, 2)->nullable();
            $table->decimal('additional_land_area_fee', 15, 2)->nullable();
            $table->decimal('additional_building_specifications_fee', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('hook_additional_fee');
            $table->dropColumn('additional_land_area_fee');
            $table->dropColumn('additional_building_specifications_fee');
        });
    }
};
