<?php

use App\Models\Client;
use App\Models\ProviderAccount;
use App\Models\Service;
use App\Models\User;

test('admins can view the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk();
});

test('the dashboard shows overview stats and expiring services', function () {
    $account = ProviderAccount::factory()->create();
    $client = Client::factory()->create();

    Service::factory()->create([
        'provider_account_id' => $account->id,
        'client_id' => $client->id,
        'expiry_date' => today()->addDays(5),
        'name' => 'Acme wildcard SSL',
    ]);
    Service::factory()->expired()->create([
        'provider_account_id' => $account->id,
        'name' => 'Past due domain',
    ]);
    Service::factory()->cancelled()->create([
        'provider_account_id' => $account->id,
    ]);

    $response = $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSee('Total services')
        ->assertSee('Expiring in 30 days')
        ->assertSee('Expired / overdue')
        ->assertSee('Expiring soon')
        ->assertSee('Acme wildcard SSL');

    $cancelled = Service::whereNotNull('cancelled_at')->first();
    $response->assertDontSee($cancelled->name);
});

test('the dashboard does not list cancelled or far-future services as expiring soon', function () {
    $account = ProviderAccount::factory()->create();

    Service::factory()->cancelled()->create([
        'provider_account_id' => $account->id,
        'name' => 'Cancelled service',
    ]);
    Service::factory()->create([
        'provider_account_id' => $account->id,
        'expiry_date' => today()->addDays(90),
        'name' => 'Far future service',
    ]);

    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk()
        ->assertDontSee('Cancelled service')
        ->assertDontSee('Far future service');
});
