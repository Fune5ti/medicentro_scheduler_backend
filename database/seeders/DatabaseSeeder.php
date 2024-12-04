<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // LocationsTableSeeder::class,
            SpecialitiesTableSeeder::class,
            // ExamsTableSeeder::class,
            DoctorsTableSeeder::class,
            // UsersTableSeeder::class,
            // PatientsTableSeeder::class,
            // DoctorAvailabilitiesTableSeeder::class,
            // ConsultationsTableSeeder::class,
            // DoctorRelationshipsSeeder::class,
        ]);
    }
}
