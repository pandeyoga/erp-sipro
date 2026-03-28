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
        Schema::create('construction_phases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('construction_id');
            $table->string('construction_phase');
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->text('documentation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('construction_phases');
    }
};
