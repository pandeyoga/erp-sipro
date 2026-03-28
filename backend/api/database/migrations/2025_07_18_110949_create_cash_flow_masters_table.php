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
        Schema::create('cash_in_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->boolean('is_property_related')->default(false);
            $table->timestamps();
        });

        Schema::create('cash_in_sub_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('cash_in_categories')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('cash_in_sub_sub_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('cash_in_sub_sub_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sub_category_id')->constrained('cash_in_sub_categories')->cascadeOnDelete();
            $table->foreignUuid('sub_sub_group_id')->constrained('cash_in_sub_sub_groups')->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_custom_input')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_in_sub_sub_categories');
        Schema::dropIfExists('cash_in_sub_sub_groups');
        Schema::dropIfExists('cash_in_sub_categories');
        Schema::dropIfExists('cash_in_categories');
    }
};
