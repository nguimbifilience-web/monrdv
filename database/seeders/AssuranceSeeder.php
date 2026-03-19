<?php

namespace Database\Seeders;

use App\Models\Assurance;
use Illuminate\Database\Seeder;

class AssuranceSeeder extends Seeder
{
    public function run(): void
    {
        Assurance::create([
            'nom' => 'CNAMGS',
            'taux_prise_en_charge' => 80,
            'est_partenaire' => true
        ]);

        Assurance::create([
            'nom' => 'AXA',
            'taux_prise_en_charge' => 70,
            'est_partenaire' => true
        ]);

        Assurance::create([
            'nom' => 'Sans Assurance',
            'taux_prise_en_charge' => 0,
            'est_partenaire' => false
        ]);
    }
}