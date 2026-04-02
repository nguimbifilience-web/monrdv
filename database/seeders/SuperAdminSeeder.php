<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Créer la clinique par défaut si elle n'existe pas
        $clinic = Clinic::firstOrCreate(
            ['slug' => 'clinique-monrdv'],
            [
                'name' => 'Clinique MonRDV',
                'email' => 'contact@monrdv.ga',
                'phone' => '+241 00 00 00 00',
                'address' => 'Libreville, Gabon',
                'is_active' => true,
            ]
        );

        // Créer le compte Super Admin (sans clinic_id)
        User::withoutGlobalScopes()->updateOrCreate(
            ['email' => 'superadmin@monrdv.ga'],
            [
                'name' => 'Super Administrateur',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'clinic_id' => null,
                'email_verified_at' => now(),
            ]
        );

        // Assigner la clinique par défaut aux comptes existants sans clinique
        User::withoutGlobalScopes()
            ->whereNull('clinic_id')
            ->where('role', '!=', 'super_admin')
            ->update(['clinic_id' => $clinic->id]);
    }
}
