<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Compte Administrateur
        User::updateOrCreate(
            ['email' => 'admin@monrdv.ga'],
            [
                'name' => 'NGUIMBI FILIENCE',
                'password' => Hash::make('password'),
                'plain_password' => 'password',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Compte Secrétaire
        User::updateOrCreate(
            ['email' => 'secretaire@monrdv.ga'],
            [
                'name' => 'Secrétaire 1',
                'password' => Hash::make('password'),
                'plain_password' => 'password',
                'role' => 'secretaire',
                'email_verified_at' => now(),
            ]
        );
    }
}
