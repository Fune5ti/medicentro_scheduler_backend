<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DoctorAvailabilitiesTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $doctors = DB::table('doctors')->get();
        $specialities = DB::table('specialities')->get();
        $exams = DB::table('exams')->get();

        foreach ($doctors as $doctor) {
            // Generate availabilities for the next 30 days
            for ($i = 0; $i < 30; $i++) {
                $date = Carbon::now()->addDays($i);

                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }

                // Morning shift
                DB::table('doctor_availabilities')->insert([
                    'doctor_id' => $doctor->id,
                    'available_date' => $date,
                    'start_time' => '09:00',
                    'end_time' => '13:00',
                    'serviceable_type' => 'App\Models\Speciality',
                    'serviceable_id' => $specialities->random()->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Afternoon shift
                DB::table('doctor_availabilities')->insert([
                    'doctor_id' => $doctor->id,
                    'available_date' => $date,
                    'start_time' => '14:00',
                    'end_time' => '18:00',
                    'serviceable_type' => 'App\Models\Exam',
                    'serviceable_id' => $exams->random()->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
