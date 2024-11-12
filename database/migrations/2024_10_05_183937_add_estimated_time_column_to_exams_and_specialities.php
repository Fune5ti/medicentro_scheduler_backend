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
        Schema::table('exams', function (Blueprint $table) {
            $table->unsignedInteger('estimated_time_in_minutes')->default(0);
        });

        Schema::table('specialities', function (Blueprint $table) {
            $table->unsignedInteger('estimated_time_in_minutes')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('estimated_time_in_minutes');
        });

        Schema::table('specialities', function (Blueprint $table) {
            $table->dropColumn('estimated_time_in_minutes');
        });
    }
};
