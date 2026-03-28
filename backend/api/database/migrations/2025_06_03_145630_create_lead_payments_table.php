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
        Schema::create('lead_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lead_id');
            $table->string('payment_type'); //cash, kpr
            $table->string('status');
            $table->string('sp3k_status')->nullable();
            $table->string('sp3k_document')->nullable();
            $table->string('sp3k_bank')->nullable();
            $table->string('sp3k_code')->nullable();
            $table->date('sp3k_date')->nullable();
            $table->string('sp3k_number')->nullable();
            $table->string('akad_kredit_status')->nullable();
            $table->string('akad_kredit_penandatanganan_document')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_payments');
    }
};
