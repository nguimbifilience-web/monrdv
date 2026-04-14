<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        $clinic = Clinic::create([
            'name' => 'Clinique Test',
            'slug' => 'clinique-test-' . uniqid(),
            'is_active' => true,
        ]);

        return User::factory()->create([
            'clinic_id' => $clinic->id,
            'role' => 'admin',
        ]);
    }

    public function test_profile_page_is_displayed(): void
    {
        $this->actingAs($this->makeUser())
            ->get('/profile')
            ->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ])
            ->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ])
            ->assertSessionHasNoErrors();

        $this->assertNotNull($user->refresh()->email_verified_at);
    }
}
