<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenancyTest extends TestCase
{
    use RefreshDatabase;

    private Clinic $clinicA;
    private Clinic $clinicB;
    private User $adminA;
    private User $adminB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clinicA = Clinic::create([
            'name' => 'Clinique A',
            'slug' => 'clinique-a',
            'is_active' => true,
        ]);

        $this->clinicB = Clinic::create([
            'name' => 'Clinique B',
            'slug' => 'clinique-b',
            'is_active' => true,
        ]);

        $this->adminA = User::create([
            'name' => 'Admin A',
            'email' => 'admin-a@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'clinic_id' => $this->clinicA->id,
            'email_verified_at' => now(),
        ]);

        $this->adminB = User::create([
            'name' => 'Admin B',
            'email' => 'admin-b@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'clinic_id' => $this->clinicB->id,
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_cannot_see_other_clinic_patients(): void
    {
        Patient::create([
            'nom' => 'Secret',
            'prenom' => 'Patient',
            'telephone' => '0600000000',
            'est_assure' => false,
            'clinic_id' => $this->clinicB->id,
        ]);

        $response = $this->actingAs($this->adminA)->get(route('patients.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Secret');
    }

    public function test_admin_cannot_access_other_clinic_patient(): void
    {
        $patientB = Patient::create([
            'nom' => 'Secret',
            'prenom' => 'Patient',
            'telephone' => '0600000000',
            'est_assure' => false,
            'clinic_id' => $this->clinicB->id,
        ]);

        $response = $this->actingAs($this->adminA)->get(route('patients.show', $patientB));
        $response->assertStatus(404);
    }

    public function test_blocked_clinic_user_cannot_access_app(): void
    {
        $this->clinicA->update([
            'is_blocked' => true,
            'blocked_reason' => 'Non-paiement',
            'blocked_at' => now(),
        ]);

        $response = $this->actingAs($this->adminA)->get(route('patients.index'));
        $response->assertRedirect();
    }

    public function test_inactive_clinic_user_cannot_access_app(): void
    {
        $this->clinicA->update(['is_active' => false]);

        $response = $this->actingAs($this->adminA)->get(route('patients.index'));
        $response->assertRedirect();
    }

    public function test_super_admin_can_see_all_clinics(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($superAdmin)->get(route('clinics.index'));
        $response->assertStatus(200);
        $response->assertSee('Clinique A');
        $response->assertSee('Clinique B');
    }
}
