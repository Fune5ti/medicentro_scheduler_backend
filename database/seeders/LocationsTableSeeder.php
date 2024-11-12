<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Generate 10 location records
        foreach (range(1, 10) as $index) {
            DB::table('locations')->insert([
                'name' => $faker->company,
                'address' => $faker->address,
                'city' => $faker->city,
                'state' => $faker->state,
                'email' => $faker->unique()->companyEmail,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
