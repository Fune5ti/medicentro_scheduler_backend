<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class SpecialitiesTableSeeder extends Seeder
{
    public function run(): void
    {
        $specialities = [
            ['name' => 'Cardiology', 'estimated_time_in_minutes' => 30],
            ['name' => 'Dermatology', 'estimated_time_in_minutes' => 20],
            ['name' => 'Neurology', 'estimated_time_in_minutes' => 45],
            ['name' => 'Orthopedics', 'estimated_time_in_minutes' => 30],
            ['name' => 'Pediatrics', 'estimated_time_in_minutes' => 25],
            ['name' => 'Psychiatry', 'estimated_time_in_minutes' => 60],
            ['name' => 'Ophthalmology', 'estimated_time_in_minutes' => 25],
            ['name' => 'Endocrinology', 'estimated_time_in_minutes' => 30],
        ];

        foreach ($specialities as $speciality) {
            DB::table('specialities')->insert([
                'name' => $speciality['name'],
                'estimated_time_in_minutes' => $speciality['estimated_time_in_minutes'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
