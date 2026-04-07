<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private Clinic $clinic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clinic = Clinic::create([
            'name' => 'Clinique Test',
            'slug' => 'clinique-test',
            'is_active' => true,
        ]);
    }

    private function createUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => "{$role}@test.com",
            'password' => bcrypt('password'),
            'role' => $role,
            'clinic_id' => $this->clinic->id,
            'email_verified_at' => now(),
        ]);
    }

    public function test_secretaire_can_access_dashboard(): void
    {
        $user = $this->createUser('secretaire');
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_secretaire_cannot_access_admin_routes(): void
    {
        $user = $this->createUser('secretaire');
        $response = $this->actingAs($user)->get(route('medecins.index'));
        $response->assertRedirect();
    }

    public function test_patient_cannot_access_staff_routes(): void
    {
        $patientUser = $this->createUser('patient');
        Patient::create([
            'nom' => 'Test',
            'prenom' => 'Patient',
            'telephone' => '0600000000',
            'est_assure' => false,
            'user_id' => $patientUser->id,
            'clinic_id' => $this->clinic->id,
        ]);

        $response = $this->actingAs($patientUser)->get(route('patients.index'));
        $response->assertRedirect();
    }

    public function test_medecin_cannot_access_staff_routes(): void
    {
        $medecinUser = $this->createUser('medecin');
        $response = $this->actingAs($medecinUser)->get(route('patients.index'));
        $response->assertRedirect();
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }
}
