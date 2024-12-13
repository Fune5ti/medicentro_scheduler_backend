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
        Schema::table('consultations', function (Blueprint $table) {
            // Drop foreign key constraints if any exist
            $foreignKeys = DB::select("SELECT constraint_name 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE referenced_table_name IS NOT NULL 
                AND table_schema = DATABASE() 
                AND table_name = 'consultations'");

            foreach ($foreignKeys as $foreignKey) {
                $table->dropForeign($foreignKey->CONSTRAINT_NAME);
            }
        });

        // Then drop the unique constraint
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropUnique('consultation_time_unique');
        });    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            // Recreate the unique constraint
            $table->unique(['doctor_availability_id', 'start_time', 'end_time'], 'consultation_time_unique');
            
            // Recreate foreign key constraints if needed
            $table->foreign('doctor_availability_id')
                  ->references('id')
                  ->on('doctor_availabilities')
                  ->onDelete('cascade');
            
            $table->foreign('patient_id')
                  ->references('id')
                  ->on('patients')
                  ->onDelete('cascade');
        });
    }
};
