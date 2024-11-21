<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ExamsTableSeeder extends Seeder
{
    public function run(): void
    {
        $exams = [
            ['name' => 'Blood Test', 'description' => 'Complete blood count and analysis', 'estimated_time_in_minutes' => 15],
            ['name' => 'X-Ray', 'description' => 'Radiographic imaging', 'estimated_time_in_minutes' => 20],
            ['name' => 'MRI', 'description' => 'Magnetic resonance imaging', 'estimated_time_in_minutes' => 45],
            ['name' => 'CT Scan', 'description' => 'Computed tomography scan', 'estimated_time_in_minutes' => 30],
            ['name' => 'Ultrasound', 'description' => 'Sonographic examination', 'estimated_time_in_minutes' => 25],
            ['name' => 'ECG', 'description' => 'Electrocardiogram', 'estimated_time_in_minutes' => 15],
        ];

        foreach ($exams as $exam) {
            DB::table('exams')->insert([
                'name' => $exam['name'],
                'description' => $exam['description'],
                'estimated_time_in_minutes' => $exam['estimated_time_in_minutes'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
