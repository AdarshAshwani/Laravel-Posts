<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Keep username short & URL-safe in case you slug it elsewhere
        $username = Str::lower(Str::slug($this->faker->unique()->userName()));
        $username = substr($username, 0, 30) ?: Str::lower(Str::random(8));

        return [
            'name'              => $this->faker->name(),
            'username'          => $username,               // <-- important
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => bcrypt('password'),      // test-only
            'remember_token'    => Str::random(10),

            // add any other NOT NULL columns you introduced, e.g.:
            // 'status' => 'active',
            // 'role'   => 'user',
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
