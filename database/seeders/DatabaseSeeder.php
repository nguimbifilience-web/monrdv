<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SpecialiteSeeder::class,
            AssuranceSeeder::class,
            MedecinSeeder::class, // AJOUTÉ
            PatientSeeder::class,  // AJOUTÉ
        ]);
    }
}