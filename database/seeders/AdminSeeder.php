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
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Compte Utilisateur simple
        User::updateOrCreate(
            ['email' => 'user@monrdv.ga'],
            [
                'name' => 'Utilisateur',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );
    }
}
