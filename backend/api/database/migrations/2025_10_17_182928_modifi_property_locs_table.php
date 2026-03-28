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
        Schema::table('property_locs', function (Blueprint $table) {
            $table->string('top')->change();
            $table->string('left')->change();
            $table->string('width')->change();
            $table->string('height')->change();
            $table->string('rotate')->change();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
