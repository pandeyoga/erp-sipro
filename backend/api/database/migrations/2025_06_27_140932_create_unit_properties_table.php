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
        Schema::create('unit_properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->uuid('cluster_id');
            $table->uuid('unit_type_id');
            $table->string('unit_number');
            $table->decimal('price', 15, 2)->nullable();
            $table->enum('status', ['belum_dibangun', 'under_development', 'available', 'retention', 'sold', 'disabled'])->default('belum_dibangun');
            $table->enum('dev_substatus', ['pondasi','naik_bata','naik_atap','plester_aci','keramik_cat','finishing', 'done'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_properties');
    }
};
