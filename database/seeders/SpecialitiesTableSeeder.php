<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Speciality;

class SpecialitiesTableSeeder extends Seeder
{
    public function run()
    {
        $specialties = [
            ['name' => 'Clínica Geral', 'estimated_time_in_minutes' => 30],
            ['name' => 'Pediatria', 'estimated_time_in_minutes' => 30],
            ['name' => 'Neuro Pediatria', 'estimated_time_in_minutes' => 45],
            ['name' => 'Ortopedia', 'estimated_time_in_minutes' => 30],
            ['name' => 'Cirurgia', 'estimated_time_in_minutes' => 45],
            ['name' => 'Ginecologia e Obstetrícia', 'estimated_time_in_minutes' => 45],
            ['name' => 'Medicina Interna', 'estimated_time_in_minutes' => 30],
            ['name' => 'Cardiologia', 'estimated_time_in_minutes' => 45],
            ['name' => 'ORL', 'estimated_time_in_minutes' => 30],
            ['name' => 'Neurologia', 'estimated_time_in_minutes' => 45],
            ['name' => 'Neurocirurgia', 'estimated_time_in_minutes' => 60],
            ['name' => 'Oftalmologia', 'estimated_time_in_minutes' => 30],
            ['name' => 'Psiquiatria', 'estimated_time_in_minutes' => 60],
            ['name' => 'Neuropsicologia', 'estimated_time_in_minutes' => 60],
            ['name' => 'Psicologia', 'estimated_time_in_minutes' => 60],
            ['name' => 'Gastroenterologia', 'estimated_time_in_minutes' => 45],
            ['name' => 'Radiologia', 'estimated_time_in_minutes' => 30],
            ['name' => 'Hematologia', 'estimated_time_in_minutes' => 45],
            ['name' => 'Reumatologia', 'estimated_time_in_minutes' => 45],
            ['name' => 'Dermatologia', 'estimated_time_in_minutes' => 30],
            ['name' => 'Anestesiologia', 'estimated_time_in_minutes' => 30],
            ['name' => 'Psicomotricidade', 'estimated_time_in_minutes' => 45],
            ['name' => 'Urologia', 'estimated_time_in_minutes' => 30],
            ['name' => 'Nefrologia', 'estimated_time_in_minutes' => 45],
            ['name' => 'Nutrição', 'estimated_time_in_minutes' => 45],
            ['name' => 'Maxilofacial', 'estimated_time_in_minutes' => 45],
            ['name' => 'Fonoaudiologia', 'estimated_time_in_minutes' => 45],
        ];

        foreach ($specialties as $specialty) {
            Speciality::create($specialty);
        }
    }
}
