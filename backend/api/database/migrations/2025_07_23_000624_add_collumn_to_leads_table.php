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
        Schema::table('leads', function (Blueprint $table) {
            // actual survey date
            $table->date('actual_survey_date')->nullable();
            // survey documentation
            $table->string('survey_documentation')->nullable();
            // unit preference
            $table->uuid('unit_preference_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // actual survey date
            $table->dropColumn('actual_survey_date');
            // survey documentation
            $table->dropColumn('survey_documentation');
            // unit preference
            $table->dropColumn('unit_preference_id');
        });
    }
};
