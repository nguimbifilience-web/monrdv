<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Disponibilite;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\Specialite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RendezVousTest extends TestCase
{
    use RefreshDatabase;

    private Clinic $clinic;
    private User $admin;
    private Medecin $medecin;
    private Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clinic = Clinic::create([
            'name' => 'Clinique Test',
            'slug' => 'clinique-test',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'clinic_id' => $this->clinic->id,
            'email_verified_at' => now(),
        ]);

        $specialite = Specialite::create([
            'nom' => 'Généraliste',
            'tarif_consultation' => 5000,
            'clinic_id' => $this->clinic->id,
        ]);

        $this->medecin = Medecin::create([
            'nom' => 'Docteur',
            'prenom' => 'Test',
            'telephone' => '0600000000',
            'specialite_id' => $specialite->id,
            'tarif_heure' => 10000,
            'heures_mois' => 160,
            'clinic_id' => $this->clinic->id,
        ]);

        $this->patient = Patient::create([
            'nom' => 'Patient',
            'prenom' => 'Test',
            'telephone' => '0600000001',
            'est_assure' => false,
            'clinic_id' => $this->clinic->id,
        ]);
    }

    public function test_admin_can_view_rendezvous_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('rendezvous.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_rendezvous(): void
    {
        $date = now()->addDays(3)->toDateString();

        Disponibilite::create([
            'medecin_id' => $this->medecin->id,
            'date_travail' => $date,
            'clinic_id' => $this->clinic->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('rendezvous.store'), [
            'date_rv' => $date,
            'heure_rv' => '10:00',
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'motif' => 'Consultation générale',
        ]);

        $response->assertRedirect(route('rendezvous.index'));
        $this->assertDatabaseHas('rendez_vous', [
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'statut' => 'confirme',
        ]);
    }

    public function test_cannot_create_rdv_if_medecin_not_working(): void
    {
        $date = now()->addDays(3)->toDateString();

        $response = $this->actingAs($this->admin)->post(route('rendezvous.store'), [
            'date_rv' => $date,
            'heure_rv' => '10:00',
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_cannot_exceed_15_rdv_per_day(): void
    {
        $date = now()->addDays(5)->toDateString();

        Disponibilite::create([
            'medecin_id' => $this->medecin->id,
            'date_travail' => $date,
            'clinic_id' => $this->clinic->id,
        ]);

        for ($i = 0; $i < 15; $i++) {
            $p = Patient::create([
                'nom' => "Patient{$i}",
                'prenom' => 'Test',
                'telephone' => "060000{$i}",
                'est_assure' => false,
                'clinic_id' => $this->clinic->id,
            ]);

            RendezVous::create([
                'date_rv' => $date,
                'heure_rv' => sprintf('%02d:00', 8 + intdiv($i, 2)),
                'patient_id' => $p->id,
                'medecin_id' => $this->medecin->id,
                'statut' => 'confirme',
                'clinic_id' => $this->clinic->id,
            ]);
        }

        $response = $this->actingAs($this->admin)->post(route('rendezvous.store'), [
            'date_rv' => $date,
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_can_cancel_rdv(): void
    {
        $rdv = RendezVous::create([
            'date_rv' => now()->addDay()->toDateString(),
            'heure_rv' => '10:00',
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'statut' => 'en_attente',
            'clinic_id' => $this->clinic->id,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('rendezvous.annuler', $rdv->id));
        $response->assertRedirect();
        $this->assertDatabaseHas('rendez_vous', ['id' => $rdv->id, 'statut' => 'annule']);
    }

    public function test_admin_can_confirm_rdv(): void
    {
        $rdv = RendezVous::create([
            'date_rv' => now()->addDay()->toDateString(),
            'heure_rv' => '10:00',
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'statut' => 'en_attente',
            'clinic_id' => $this->clinic->id,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('rendezvous.confirmer', $rdv->id));
        $response->assertRedirect();
        $this->assertDatabaseHas('rendez_vous', ['id' => $rdv->id, 'statut' => 'confirme']);
    }
}
