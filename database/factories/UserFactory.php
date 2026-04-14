<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        // Le cast 'password' => 'hashed' sur User assure le hash automatique,
        // ne pas pre-hasher ici (sinon double hash et Hash::check echoue).
        // clinic_id est requis : ClinicScope global filtre par clinique de
        // l'utilisateur authentifie, sans clinique aucune requete ne trouve rien.
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'role' => 'admin',
            'clinic_id' => fn () => Clinic::create([
                'name' => 'Factory Clinic ' . Str::random(4),
                'slug' => 'factory-' . Str::random(6),
                'is_active' => true,
            ])->id,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
