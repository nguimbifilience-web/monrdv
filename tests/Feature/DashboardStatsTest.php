<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\Specialite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_affiche_stats_sans_erreur_meme_vide(): void
    {
        $clinic = Clinic::create(['name' => 'C', 'slug' => 'c', 'is_active' => true]);
        $admin  = User::factory()->create(['role' => 'admin', 'clinic_id' => $clinic->id]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Tableau de Bord')
            ->assertSee('Statistiques');
    }

    public function test_dashboard_compile_les_stats_avec_donnees(): void
    {
        $clinic = Clinic::create(['name' => 'C', 'slug' => 'c', 'is_active' => true]);
        $admin  = User::factory()->create(['role' => 'admin', 'clinic_id' => $clinic->id]);
        $spe = Specialite::create(['nom' => 'G', 'tarif_consultation' => 5000, 'clinic_id' => $clinic->id]);
        $medecin = Medecin::create([
            'nom' => 'D', 'prenom' => 'E', 'telephone' => '0',
            'specialite_id' => $spe->id, 'tarif_heure' => 1, 'heures_mois' => 1,
            'clinic_id' => $clinic->id,
        ]);
        $patient = Patient::create([
            'nom' => 'P', 'prenom' => 'A', 'telephone' => '1',
            'est_assure' => false, 'clinic_id' => $clinic->id,
        ]);

        // 3 consultations ce mois, 1 mois passé (pour l'évolution)
        foreach ([0, 0, 0] as $_) {
            Consultation::create([
                'patient_id' => $patient->id, 'medecin_id' => $medecin->id,
                'montant_total' => 5000, 'taux_couverture' => 0,
                'montant_assurance' => 0, 'montant_patient' => 5000,
                'montant_donne' => 5000, 'montant_rendu' => 0,
                'clinic_id' => $clinic->id,
            ]);
        }
        $ancienne = Consultation::create([
            'patient_id' => $patient->id, 'medecin_id' => $medecin->id,
            'montant_total' => 3000, 'taux_couverture' => 0,
            'montant_assurance' => 0, 'montant_patient' => 3000,
            'montant_donne' => 3000, 'montant_rendu' => 0,
            'clinic_id' => $clinic->id,
        ]);
        $ancienne->forceFill(['created_at' => now()->subMonthNoOverflow()->startOfMonth()->addDay()])->save();

        // RDV varies pour le donut
        foreach (['en_attente', 'confirme', 'annule'] as $statut) {
            RendezVous::create([
                'patient_id' => $patient->id, 'medecin_id' => $medecin->id,
                'date_rv' => now()->toDateString(), 'heure_rv' => '10:00',
                'statut' => $statut, 'clinic_id' => $clinic->id,
            ]);
        }

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('15 000 F') // 3 x 5000
            ->assertSee('Top médecins du mois')
            ->assertSee('Dr. D E');
    }
}
