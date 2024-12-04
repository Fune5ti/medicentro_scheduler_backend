<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\Speciality;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class DoctorsTableSeeder extends Seeder
{
    public function run()
    {
        // First, ensure the storage directory exists
        Storage::disk('public')->makeDirectory('doctors');

        // Copy all images from seeder directory to storage
        $seedImagesPath = database_path('seeders/images/doctors');
        if (File::exists($seedImagesPath)) {
            $files = File::files($seedImagesPath);
            foreach ($files as $file) {
                $filename = $file->getFilename();
                Storage::disk('public')->put(
                    "doctors/{$filename}",
                    File::get($file->getPathname())
                );
            }
        }

        $doctors = [
            // Clínica Geral
            [
                'name' => 'Dra. Gretty Figueroa',
                'crm' => 'CG001',
                'phone' => '999000001',
                'email' => 'gretty.figueroa@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-feminino).png',
                'specialties' => ['Clínica Geral']
            ],
            [
                'name' => 'Dr. Luis Flores',
                'crm' => 'CG002',
                'phone' => '999000002',
                'email' => 'luis.flores@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-masculino).png',
                'specialties' => ['Clínica Geral']
            ],
            [
                'name' => 'Dra. Ludmila Ramos',
                'crm' => 'CG003',
                'phone' => '999000003',
                'email' => 'ludmila.ramos@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-feminino).png',
                'specialties' => ['Clínica Geral']
            ],
            [
                'name' => 'Dra. Leticia Pereira',
                'crm' => 'CG004',
                'phone' => '999000004',
                'email' => 'leticia.pereira@medicentro.test',
                'photo_location' => 'doctors/Dra.-Leticia-Pereira.png',
                'specialties' => ['Clínica Geral']
            ],
            [
                'name' => 'Dra. Aline Pires',
                'crm' => 'CG005',
                'phone' => '999000005',
                'email' => 'aline.pires@medicentro.test',
                'photo_location' => 'doctors/alinepires.png',
                'specialties' => ['Clínica Geral']
            ],
            [
                'name' => 'Dra. Naydine Evora',
                'crm' => 'CG006',
                'phone' => '999000006',
                'email' => 'naydine.evora@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Clínica Geral']
            ],
            [
                'name' => 'Dra. Kelucy Borges',
                'crm' => 'CG007',
                'phone' => '999000007',
                'email' => 'kelucy.borges@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Clínica Geral']
            ],

            // Especialidades
            [
                'name' => 'Dra. Silvia Sabino',
                'crm' => 'PD001',
                'phone' => '999000008',
                'email' => 'silvia.sabino@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Pediatria']
            ],
            [
                'name' => 'Dra. Catia Costa',
                'crm' => 'PD002',
                'phone' => '999000009',
                'email' => 'catia.costa@medicentro.test',
                'photo_location' => 'doctors/Dra-Catia-Costa-.png',
                'specialties' => ['Pediatria']
            ],
            [
                'name' => 'Dra. Stephanie Monteiro',
                'crm' => 'NP001',
                'phone' => '999000010',
                'email' => 'stephanie.monteiro@medicentro.test',
                'photo_location' => 'doctors/Dra-Stephanie-Monteiro.png',
                'specialties' => ['Neuro Pediatria']
            ],
            [
                'name' => 'Dr. Carlos Reyes',
                'crm' => 'ORT001',
                'phone' => '999000011',
                'email' => 'carlos.reyes@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-masculino).png',
                'specialties' => ['Ortopedia']
            ],
            [
                'name' => 'Dr. Paulo Freire',
                'crm' => 'ORT002',
                'phone' => '999000012',
                'email' => 'paulo.freire@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-masculino).png',
                'specialties' => ['Ortopedia']
            ],
            [
                'name' => 'Dr. Octávio Brito',
                'crm' => 'ORT003',
                'phone' => '999000013',
                'email' => 'octavio.brito@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-masculino).png',
                'specialties' => ['Ortopedia']
            ],
            [
                'name' => 'Dr. Neudis Vazquez',
                'crm' => 'CIR001',
                'phone' => '999000014',
                'email' => 'neudis.vazquez@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-feminino).png',
                'specialties' => ['Cirurgia']
            ],
            [
                'name' => 'Dr. Jorge Rivera',
                'crm' => 'CIR002',
                'phone' => '999000015',
                'email' => 'jorge.rivera@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-masculino).png',
                'specialties' => ['Cirurgia']
            ],
            [
                'name' => 'Dr. Mario Figueroa',
                'crm' => 'CIR003',
                'phone' => '999000016',
                'email' => 'mario.figueroa@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-masculino).png',
                'specialties' => ['Cirurgia']
            ],
            [
                'name' => 'Dra. Isabel Sanchez',
                'crm' => 'GIN001',
                'phone' => '999000017',
                'email' => 'isabel.sanchez@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Ginecologia e Obstetrícia']
            ],
            [
                'name' => 'Dra. Neusa Semedo',
                'crm' => 'GIN002',
                'phone' => '999000018',
                'email' => 'neusa.semedo@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Ginecologia e Obstetrícia']
            ],
            [
                'name' => 'Dra. Ana Brito',
                'crm' => 'MI001',
                'phone' => '999000019',
                'email' => 'ana.brito@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Medicina Interna']
            ],
            [
                'name' => 'Dr. Fernando Lopes',
                'crm' => 'CAR001',
                'phone' => '999000020',
                'email' => 'fernando.lopes@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-masculino).png',
                'specialties' => ['Cardiologia']
            ],
            [
                'name' => 'Dr. Charles Constantino',
                'crm' => 'CAR002',
                'phone' => '999000021',
                'email' => 'charles.constantino@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-masculino).png',
                'specialties' => ['Cardiologia']
            ],
            [
                'name' => 'Dr. Victor Hugo',
                'crm' => 'ORL001',
                'phone' => '999000022',
                'email' => 'victor.hugo@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-masculino).png',
                'specialties' => ['ORL']
            ],
            [
                'name' => 'Dra. Antónia Fortes',
                'crm' => 'NEU001',
                'phone' => '999000023',
                'email' => 'antonia.fortes@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Neurologia']
            ],
            [
                'name' => 'Dra. Ileydis Cabezas',
                'crm' => 'NC001',
                'phone' => '999000024',
                'email' => 'ileydis.cabezas@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Neurocirurgia']
            ],
            [
                'name' => 'Dra. Yoselyvis Saavedra',
                'crm' => 'OFT001',
                'phone' => '999000025',
                'email' => 'yoselyvis.saavedra@medicentro.test',
                'photo_location' =>  'doctors/Dra.-Yosleivy-Saavedra.png',
                'specialties' => ['Oftalmologia']
            ],
            [
                'name' => 'Dr. Aristides da Luz',
                'crm' => 'PSI001',
                'phone' => '999000026',
                'email' => 'aristides.luz@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-masculino).png',
                'specialties' => ['Psiquiatria']
            ],
            [
                'name' => 'Dra. Carla Jesus',
                'crm' => 'NP001',
                'phone' => '999000027',
                'email' => 'carla.jesus@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Neuropsicologia']
            ],
            [
                'name' => 'Dra. Arminda dos Reis',
                'crm' => 'PSI002',
                'phone' => '999000028',
                'email' => 'arminda.reis@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Psicologia']
            ],
            [
                'name' => 'Dra. Romine Oliveira',
                'crm' => 'PSI003',
                'phone' => '999000029',
                'email' => 'romine.oliveira@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Psicologia']
            ],
            [
                'name' => 'Dr. Yaser Garcia',
                'crm' => 'GAS001',
                'phone' => '999000030',
                'email' => 'yaser.garcia@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-masculino).png',
                'specialties' => ['Gastroenterologia']
            ],
            [
                'name' => 'Dra. Susel Molina',
                'crm' => 'RAD001',
                'phone' => '999000031',
                'email' => 'susel.molina@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Radiologia']
            ],
            [
                'name' => 'Dra. Samira Morais',
                'crm' => 'RAD002',
                'phone' => '999000032',
                'email' => 'samira.morais@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Radiologia']
            ],
            [
                'name' => 'Dr. Paulo Almeida',
                'crm' => 'HEM001',
                'phone' => '999000033',
                'email' => 'paulo.almeida@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-masculino).png',
                'specialties' => ['Hematologia']
            ],
            [
                'name' => 'Dra. Carla Lima',
                'crm' => 'HEM002',
                'phone' => '999000034',
                'email' => 'carla.lima@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Hematologia']
            ],
            [
                'name' => 'Dra. Leila Oliveira',
                'crm' => 'REU001',
                'phone' => '999000035',
                'email' => 'leila.oliveira@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Reumatologia']
            ],
            [
                'name' => 'Dr. Valter Oliveira',
                'crm' => 'DER001',
                'phone' => '999000036',
                'email' => 'valter.oliveira@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-masculino).png',
                'specialties' => ['Dermatologia']
            ],
            [
                'name' => 'Dr. Aquino Fernandes',
                'crm' => 'DER002',
                'phone' => '999000037',
                'email' => 'aquino.fernandes@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-masculino).png',
                'specialties' => ['Dermatologia']
            ],
            [
                'name' => 'Dra. Ibet Gayoso',
                'crm' => 'ANE001',
                'phone' => '999000038',
                'email' => 'ibet.gayoso@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Anestesiologia']
            ],
            [
                'name' => 'Dra. Larissa Medina',
                'crm' => 'PSM001',
                'phone' => '999000039',
                'email' => 'larissa.medina@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Psicomotricidade']
            ],
            [
                'name' => 'Dra. Benvinda Miranda',
                'crm' => 'URO001',
                'phone' => '999000040',
                'email' => 'benvinda.miranda@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Urologia']
            ],
            [
                'name' => 'Dra. Suzete Ramos',
                'crm' => 'NEF001',
                'phone' => '999000041',
                'email' => 'suzete.ramos@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Nefrologia']
            ],
            [
                'name' => 'Dra. Milene Lima',
                'crm' => 'NUT001',
                'phone' => '999000042',
                'email' => 'milene.lima@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Nutrição']
            ],
            [
                'name' => 'Dra. Jucélia Borges',
                'crm' => 'NUT002',
                'phone' => '999000043',
                'email' => 'jucelia.borges@medicentro.test',
                'photo_location' =>  'doctors/Dra.-jucelia-Borges.png',
                'specialties' => ['Nutrição']
            ],
            [
                'name' => 'Dr. Daniel Monteiro',
                'crm' => 'CIR004',
                'phone' => '999000044',
                'email' => 'daniel.monteiro@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-masculino).png',
                'specialties' => ['Cirurgia']
            ],
            [
                'name' => 'Dra. Isabel Ponce',
                'crm' => 'MAX001',
                'phone' => '999000045',
                'email' => 'isabel.ponce@medicentro.test',
                'photo_location' => 'doctors/(sem-foto-feminino).png',
                'specialties' => ['Maxilofacial']
            ],
            [
                'name' => 'Dra. Leiny Nascimento',
                'crm' => 'FON001',
                'phone' => '999000046',
                'email' => 'leiny.nascimento@medicentro.test',
                'photo_location' =>  'doctors/(sem-foto-feminino).png',
                'specialties' => ['Fonoaudiologia']
            ],
            [
                'name' => 'Dra. Ailine Lopes',
                'crm' => 'FON002',
                'phone' => '999000047',
                'email' => 'ailine.lopes@medicentro.test',
                'photo_location' =>  'doctors/Dra.-Ailine-Lopes.png',
                'specialties' => ['Fonoaudiologia']
            ],
        ];

        foreach ($doctors as $doctorData) {
            $specialties = $doctorData['specialties'];
            unset($doctorData['specialties']);

            $doctor = Doctor::create($doctorData);

            foreach ($specialties as $specialtyName) {
                $specialty = Speciality::where('name', $specialtyName)->first();
                if ($specialty) {
                    $doctor->specialities()->attach($specialty->id);
                }
            }
        }
    }
}
