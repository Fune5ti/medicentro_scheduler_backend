<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;


class DoctorRelationshipsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Get all IDs
        $doctorIds = DB::table('doctors')->pluck('id')->toArray();
        $specialityIds = DB::table('specialities')->pluck('id')->toArray();
        $examIds = DB::table('exams')->pluck('id')->toArray();
        $locationIds = DB::table('locations')->pluck('id')->toArray();

        // Seed doctor_speciality relationships
        foreach ($doctorIds as $doctorId) {
            // Each doctor gets 1-3 random specialities
            $randomSpecialities = array_rand(array_flip($specialityIds), rand(1, 3));
            foreach ((array)$randomSpecialities as $specialityId) {
                DB::table('doctor_speciality')->insert([
                    'doctor_id' => $doctorId,
                    'speciality_id' => $specialityId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Seed doctor_exams relationships
        foreach ($doctorIds as $doctorId) {
            // Each doctor gets 2-4 random exams
            $randomExams = array_rand(array_flip($examIds), rand(2, 4));
            foreach ((array)$randomExams as $examId) {
                DB::table('doctor_exams')->insert([
                    'doctor_id' => $doctorId,
                    'exam_id' => $examId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Seed doctor_location relationships
        foreach ($doctorIds as $doctorId) {
            // Each doctor works in 1-2 locations
            $randomLocations = array_rand(array_flip($locationIds), rand(1, 2));
            foreach ((array)$randomLocations as $locationId) {
                DB::table('doctor_location')->insert([
                    'doctor_id' => $doctorId,
                    'location_id' => $locationId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
