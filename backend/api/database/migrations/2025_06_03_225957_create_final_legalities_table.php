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
        Schema::create('final_legalities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lead_id');
            $table->enum('status', ['pending', 'bast', 'retention', 'complete'])->default('pending');
            $table->string('bast_document')->nullable();
            $table->string('bast_hanover_photo')->nullable();
            $table->date('bast_date')->nullable();
            $table->string('retention_document')->nullable();
            $table->string('retention_hanover_photo')->nullable();
            $table->date('retention_start_date')->nullable();
            $table->date('retention_end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_legalities');
    }
};
