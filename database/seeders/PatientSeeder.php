<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Assurance;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $assurances = Assurance::all();

        Patient::create([
            'nom' => 'ZUE',
            'prenom' => 'Paul',
            'email' => 'paul.zue@exemple.com',
            'telephone' => '065123456',
            'assurance_id' => $assurances->where('nom', 'CNAMGS')->first()->id, // On force CNAMGS
            'numero_assurance' => 'CN-998877',
        ]);

        Patient::create([
            'nom' => 'MESSA',
            'prenom' => 'Julie',
            'email' => 'julie.messa@exemple.com',
            'telephone' => '074987654',
            'assurance_id' => $assurances->where('nom', 'Sans Assurance')->first()->id,
            'numero_assurance' => null,
        ]);
    }
}