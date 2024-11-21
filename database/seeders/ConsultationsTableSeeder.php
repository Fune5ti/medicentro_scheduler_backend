<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;


class ConsultationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $availabilities = DB::table('doctor_availabilities')->get();
        $patients = DB::table('patients')->get();
        $statuses = ['scheduled', 'canceled', 'completed'];

        foreach ($availabilities->take(200) as $availability) {
            // Random 30-minute slot within the availability period
            $startTime = Carbon::parse($availability->start_time)
                ->addMinutes(rand(0, 180)); // Random start within 3-hour block
            $endTime = (clone $startTime)->addMinutes(30);

            // Only create if within availability period
            if ($endTime->format('H:i:s') <= $availability->end_time) {
                DB::table('consultations')->insert([
                    'doctor_availability_id' => $availability->id,
                    'patient_id' => $patients->random()->id,
                    'start_time' => $startTime->format('H:i:s'),
                    'end_time' => $endTime->format('H:i:s'),
                    'status' => $faker->randomElement($statuses),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
