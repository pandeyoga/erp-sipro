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
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lead_id');
            $table->uuid('property_unit_id');
            $table->string('status');
            $table->date('reservation_date');
            $table->string('booking_document_url')->nullable();
            $table->string('dp_proof_url')->nullable();
            $table->decimal('dp_amount', 15, 2)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            // Optional: add index or foreign keys manually if needed
            $table->index('lead_id');
            $table->index('property_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
