<?php

namespace Database\Factories;

use App\Models\Provider;
use App\Models\ProviderAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProviderAccount>
 */
class ProviderAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_id' => Provider::factory(),
            'account_label' => fake()->randomElement(['Main', 'Reseller', 'Client backup']),
            'login_email' => fake()->unique()->safeEmail(),
            'password' => 'secret-'.fake()->unique()->bothify('####'),
            'two_factor_notes' => fake()->optional()->sentence(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
