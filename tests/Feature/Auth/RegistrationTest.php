<?php

namespace Tests\Feature\Auth;

use App\Models\Clinic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $clinic = Clinic::create([
            'name' => 'Clinique Test',
            'slug' => 'clinique-test',
            'is_active' => true,
        ]);

        $response = $this->post('/register', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'telephone' => '0600000000',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'clinic_slug' => $clinic->slug,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('patient.dashboard'));
    }
}
