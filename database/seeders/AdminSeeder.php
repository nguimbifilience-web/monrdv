<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@monrdv.ga'], // Condition pour vérifier si l'admin existe déjà
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'), // Change "password" par ton mot de passe réel
                'is_admin' => true,
                'email_verified_at' => now(), // Optionnel : pour éviter de demander la vérification d'email
            ]
        );
    }
}