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
        Schema::create('debts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
            $table->uuid('cash_in_sub_sub_category_id');
            $table->uuid('bank_account_id');
            $table->uuid('payment_bank_account_id')->nullable();
            $table->double('total_amount');
            $table->string('paid_amount');
            $table->uuid('cash_in_id');
            $table->uuid('cash_out_id')->nullable();
            $table->uuid('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
