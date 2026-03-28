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
        Schema::create('unit_property_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('unit_property_id');
            $table->uuid('action_by')->index()->nullable();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->timestamp('changed_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_property_histories');
    }
};
