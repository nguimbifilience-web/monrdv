<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // S'assurer qu'une clinique par défaut existe
        $clinic = \App\Models\Clinic::firstOrCreate(
            ['slug' => 'clinique-monrdv'],
            [
                'name' => 'Clinique MonRDV',
                'email' => 'contact@monrdv.ga',
                'phone' => '+241 00 00 00 00',
                'address' => 'Libreville, Gabon',
                'is_active' => true,
            ]
        );

        // Compte Administrateur
        User::withoutGlobalScopes()->updateOrCreate(
            ['email' => 'admin@monrdv.ga'],
            [
                'name' => 'NGUIMBI FILIENCE',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'clinic_id' => $clinic->id,
                'email_verified_at' => now(),
            ]
        );

        // Compte Secrétaire
        User::withoutGlobalScopes()->updateOrCreate(
            ['email' => 'secretaire@monrdv.ga'],
            [
                'name' => 'Secrétaire 1',
                'password' => Hash::make('password'),
                'role' => 'secretaire',
                'clinic_id' => $clinic->id,
                'email_verified_at' => now(),
            ]
        );
    }
}
