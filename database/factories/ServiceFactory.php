<?php

namespace Database\Factories;

use App\Enums\ServiceType;
use App\Models\Client;
use App\Models\ProviderAccount;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_account_id' => ProviderAccount::factory(),
            'client_id' => Client::factory(),
            'type' => fake()->randomElement(ServiceType::cases()),
            'name' => fake()->domainName(),
            'purchase_date' => now()->subMonths(6)->toDateString(),
            'expiry_date' => now()->addDays(fake()->numberBetween(1, 365))->toDateString(),
            'renewal_cost' => fake()->randomFloat(2, 5, 500),
            'currency' => 'NGN',
            'auto_renew' => false,
            'cancelled_at' => null,
            'notes' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'expiry_date' => now()->subDays(fake()->numberBetween(1, 60))->toDateString(),
        ]);
    }

    public function expiringSoon(int $days = 7): static
    {
        return $this->state(fn (array $attributes): array => [
            'expiry_date' => now()->addDays($days)->toDateString(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'cancelled_at' => now(),
        ]);
    }
}
