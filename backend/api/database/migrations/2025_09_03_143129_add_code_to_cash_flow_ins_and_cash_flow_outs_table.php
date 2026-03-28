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
        Schema::table('cash_in_categories', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('cash_in_sub_categories', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('cash_in_sub_sub_groups', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('cash_in_sub_sub_categories', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('cash_out_categories', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('cash_out_sub_categories', function (Blueprint $table) {
            $table->string('code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_in_categories', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('cash_in_sub_categories', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('cash_in_sub_sub_groups', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('cash_in_sub_sub_categories', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('cash_out_categories', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('cash_out_sub_categories', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
