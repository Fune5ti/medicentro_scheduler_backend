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
        Schema::create('doctor_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['doctor_id', 'exam_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_exams');
    }
};
