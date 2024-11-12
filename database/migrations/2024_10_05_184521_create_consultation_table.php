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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_availability_id')->constrained('doctor_availabilities')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['scheduled', 'canceled', 'completed'])->default('scheduled');
            $table->timestamps();

            $table->unique(['doctor_availability_id', 'start_time', 'end_time'], 'consultation_time_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
