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
        Schema::create('retention_cases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('property_id');
            $table->uuid('sub_contractor_id');
            $table->date('opened_at');
            $table->text('description')->nullable();
            $table->string('status')->default('open');
            $table->dateTime('resolved_at')->nullable();
            $table->date('estimated_resolved_at')->nullable();
            $table->jsonb('case_pictures')->nullable();
            $table->jsonb('case_documentations')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retention_cases');
    }
};
