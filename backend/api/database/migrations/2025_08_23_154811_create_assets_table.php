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
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('registration_number');
            $table->uuid('category_id');
            $table->uuid('sub_category_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->date('acquisition_date');
            $table->integer('useful_life')->nullable();
            $table->boolean('has_depreciation')->default(false);
            $table->decimal('depreciation_rate', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
