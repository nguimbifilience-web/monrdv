<?php

namespace Tests\Feature;

use App\Models\Assurance;
use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Specialite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationTest extends TestCase
{
    use RefreshDatabase;

    private Clinic $clinic;
    private User $admin;
    private Medecin $medecin;
    private Patient $patient;
    private Specialite $specialite;

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

        $this->specialite = Specialite::create([
            'nom' => 'Généraliste',
            'tarif_consultation' => 10000,
            'clinic_id' => $this->clinic->id,
        ]);

        $this->medecin = Medecin::create([
            'nom' => 'Docteur',
            'prenom' => 'Test',
            'telephone' => '0600000000',
            'specialite_id' => $this->specialite->id,
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

    public function test_admin_can_view_consultations(): void
    {
        $response = $this->actingAs($this->admin)->get(route('consultations.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_consultation(): void
    {
        $response = $this->actingAs($this->admin)->post(route('consultations.store'), [
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'est_assure' => 0,
            'montant_total' => 10000,
            'montant_donne' => 10000,
            'tarif_specialite' => 10000,
        ]);

        $response->assertRedirect(route('rendezvous.index'));
        $this->assertDatabaseHas('consultations', [
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
        ]);
    }

    public function test_consultation_with_insurance_calculates_correctly(): void
    {
        $assurance = Assurance::create([
            'nom' => 'CNAMGS',
            'type' => 'publique',
            'nom_referent' => 'Referent',
            'taux_couverture' => 80,
            'telephone' => '0600000000',
            'email' => 'cnamgs@test.com',
            'clinic_id' => $this->clinic->id,
        ]);

        $this->patient->update([
            'est_assure' => true,
            'assurance_id' => $assurance->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('consultations.store'), [
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'est_assure' => 1,
            'montant_total' => 10000,
            'montant_donne' => 2000,
            'tarif_specialite' => 10000,
        ]);

        $response->assertRedirect();

        $consultation = Consultation::latest()->first();
        $this->assertEquals(80, $consultation->taux_couverture);
        $this->assertEquals(8000, $consultation->montant_assurance);
        $this->assertEquals(2000, $consultation->montant_patient);
    }

    public function test_admin_can_view_monthly_revenue(): void
    {
        $response = $this->actingAs($this->admin)->get(route('consultations.recettes-mensuelles'));
        $response->assertStatus(200);
    }
}
