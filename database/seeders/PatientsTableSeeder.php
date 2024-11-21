<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;


class PatientsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 100) as $index) {
            DB::table('patients')->insert([
                'full_name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'nif' => $faker->unique()->numerify('#########'), // 9 digits
                'phone' => $faker->phoneNumber,
                'birth_date' => $faker->date('Y-m-d', '-18 years'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
