<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DoctorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 50) as $index) {
            DB::table('doctors')->insert([
                'name' => 'Dr. ' . $faker->name,
                'crm' => $faker->unique()->numerify('######'), // generates a random 6-digit number
                'phone' => $faker->phoneNumber,
                'email' => $faker->unique()->safeEmail,
                'photo_location' => 'storage/doctors/' . $faker->lexify('doctor_????') . '.jpg', // random filename
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
