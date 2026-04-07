<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    private Clinic $clinic;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clinic = Clinic::create([
            'name' => 'Clinique Test',
            'slug' => 'clinique-test',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'clinic_id' => $this->clinic->id,
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_can_view_patients_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('patients.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_patient(): void
    {
        $response = $this->actingAs($this->admin)->post(route('patients.store'), [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'telephone' => '0601020304',
            'est_assure' => false,
        ]);

        $response->assertRedirect(route('patients.index'));
        $this->assertDatabaseHas('patients', ['nom' => 'Dupont', 'prenom' => 'Jean']);
    }

    public function test_admin_can_update_patient(): void
    {
        $patient = Patient::create([
            'nom' => 'Ancien',
            'prenom' => 'Nom',
            'telephone' => '0600000000',
            'est_assure' => false,
            'clinic_id' => $this->clinic->id,
        ]);

        $response = $this->actingAs($this->admin)->put(route('patients.update', $patient), [
            'nom' => 'Nouveau',
            'prenom' => 'Nom',
            'telephone' => '0600000000',
            'est_assure' => false,
        ]);

        $response->assertRedirect(route('patients.index'));
        $this->assertDatabaseHas('patients', ['nom' => 'Nouveau']);
    }

    public function test_admin_can_delete_patient(): void
    {
        $patient = Patient::create([
            'nom' => 'Supprimer',
            'prenom' => 'Moi',
            'telephone' => '0600000000',
            'est_assure' => false,
            'clinic_id' => $this->clinic->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('patients.destroy', $patient));
        $response->assertRedirect(route('patients.index'));
        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
    }

    public function test_patient_requires_nom_prenom_telephone(): void
    {
        $response = $this->actingAs($this->admin)->post(route('patients.store'), []);
        $response->assertSessionHasErrors(['nom', 'prenom', 'telephone']);
    }

    public function test_patient_portal_user_can_see_dashboard(): void
    {
        $patientUser = User::create([
            'name' => 'Patient User',
            'email' => 'patient@test.com',
            'password' => bcrypt('password'),
            'role' => 'patient',
            'clinic_id' => $this->clinic->id,
            'email_verified_at' => now(),
        ]);

        Patient::create([
            'nom' => 'User',
            'prenom' => 'Patient',
            'telephone' => '0600000000',
            'est_assure' => false,
            'user_id' => $patientUser->id,
            'clinic_id' => $this->clinic->id,
        ]);

        $response = $this->actingAs($patientUser)->get(route('patient.dashboard'));
        $response->assertStatus(200);
    }
}
