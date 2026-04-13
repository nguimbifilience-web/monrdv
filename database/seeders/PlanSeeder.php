<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'price_monthly' => 20000,
                'max_medecins' => 5,
                'max_rdv_monthly' => 500,
                'includes_insurance' => false,
                'description' => 'Idéal pour les petits cabinets démarrant leur activité.',
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price_monthly' => 50000,
                'max_medecins' => 20,
                'max_rdv_monthly' => 3000,
                'includes_insurance' => true,
                'description' => 'Pour les cliniques en croissance avec gestion des assurances.',
                'is_active' => true,
            ],
            [
                'name' => 'Entreprise',
                'slug' => 'entreprise',
                'price_monthly' => 150000,
                'max_medecins' => null,
                'max_rdv_monthly' => null,
                'includes_insurance' => true,
                'description' => 'Sans limite, pour les grandes structures hospitalières.',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
