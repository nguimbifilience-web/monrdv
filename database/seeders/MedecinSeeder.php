<?php

namespace Database\Seeders;

use App\Models\Medecin;
use App\Models\Specialite;
use Illuminate\Database\Seeder;

class MedecinSeeder extends Seeder
{
    public function run(): void
    {
        // On récupère toutes les spécialités qu'on a créées juste avant
        $specialites = Specialite::all();

        // On crée 3 médecins de test
        Medecin::create([
            'nom' => 'NDONG',
            'prenom' => 'Jean',
            'telephone' => '066001122',
            'specialite_id' => $specialites->random()->id, // Choisi une spécialité au hasard
        ]);

        Medecin::create([
            'nom' => 'MVE',
            'prenom' => 'Anne',
            'telephone' => '077554433',
            'specialite_id' => $specialites->random()->id,
        ]);

        Medecin::create([
            'nom' => 'OBIANG',
            'prenom' => 'Marc',
            'telephone' => '062118899',
            'specialite_id' => $specialites->random()->id,
        ]);
    }
}