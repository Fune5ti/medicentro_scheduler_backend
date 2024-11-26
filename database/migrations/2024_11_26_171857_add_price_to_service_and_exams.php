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
            $table->decimal('price', 10, 2)->after('description')->default(0.00);
        });

        Schema::table('specialities', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->after('name')->default(0.00);
            $table->string('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('specialities', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('description');
        });
    }
};
