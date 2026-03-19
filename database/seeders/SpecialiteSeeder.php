<?php

namespace Database\Seeders;

use App\Models\Specialite;
use Illuminate\Database\Seeder;

class SpecialiteSeeder extends Seeder
{
    public function run(): void
    {
        $specialites = [
            ['nom' => 'Cardiologie'],
            ['nom' => 'Pédiatrie'],
            ['nom' => 'Généraliste'],
            ['nom' => 'Dentiste'],
            ['nom' => 'Ophtalmologie'],
        ];

        foreach ($specialites as $specialite) {
            Specialite::create($specialite);
        }
    }
}