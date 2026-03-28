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
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('order_number')->autoIncrement();
            $table->uuid('contact_id')->index();
            $table->uuid('assign_to')->index()->nullable();
            $table->string('status', 30);// new, prospect, reserve, document, complete, cancel
            $table->date('survey_date')->nullable();
            $table->uuid('survey_location_id')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
