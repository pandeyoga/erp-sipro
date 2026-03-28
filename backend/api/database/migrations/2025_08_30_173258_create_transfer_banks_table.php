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
        Schema::create('transfer_banks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_cash_in_id');
            $table->uuid('parent_cash_out_id');
            $table->uuid('from_bank_account_id');
            $table->uuid('to_bank_account_id');
            $table->integer('amount');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_banks');
    }
};
