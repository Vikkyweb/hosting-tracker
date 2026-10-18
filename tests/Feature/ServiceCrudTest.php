<?php

use App\Models\Client;
use App\Models\ProviderAccount;
use App\Models\Service;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('admins can view the service list and create page', function (string $path) {
    $this->actingAs($this->user)->get($path)->assertOk();
})->with([
    '/admin/services',
    '/admin/services/create',
]);

test('admins can view each service edit page', function () {
    $service = Service::factory()->create();

    $this->actingAs($this->user)
        ->get(route('admin.services.edit', $service))
        ->assertOk()
        ->assertSee($service->name);
});

test('services can be created', function () {
    $account = ProviderAccount::factory()->create();
    $client = Client::factory()->create();

    $this->actingAs($this->user)->post(route('admin.services.store'), [
        'name' => 'acme.com',
        'type' => 'domain',
        'provider_account_id' => $account->id,
        'client_id' => $client->id,
        'purchase_date' => '2026-01-01',
        'expiry_date' => '2027-01-01',
        'renewal_cost' => '12.50',
        'currency' => 'usd',
        'auto_renew' => '1',
        'notes' => 'Primary domain',
    ])->assertRedirect(route('admin.services.index'));

    $this->assertDatabaseHas('services', [
        'name' => 'acme.com',
        'type' => 'domain',
        'provider_account_id' => $account->id,
        'client_id' => $client->id,
        'currency' => 'USD',
        'renewal_cost' => 12.50,
        'auto_renew' => true,
    ]);
});

test('service validation requires the essential fields', function () {
    $this->actingAs($this->user)
        ->from(route('admin.services.create'))
        ->post(route('admin.services.store'), [])
        ->assertRedirect(route('admin.services.create'))
        ->assertSessionHasErrors(['name', 'type', 'provider_account_id', 'purchase_date', 'expiry_date', 'currency']);

    $this->assertDatabaseCount('services', 0);
});

test('services can be updated', function () {
    $service = Service::factory()->create();

    $this->actingAs($this->user)
        ->put(route('admin.services.update', $service), [
            'name' => 'renamed.example',
            'type' => 'hosting',
            'provider_account_id' => $service->provider_account_id,
            'client_id' => null,
            'purchase_date' => $service->purchase_date->toDateString(),
            'expiry_date' => $service->expiry_date->toDateString(),
            'renewal_cost' => null,
            'currency' => 'NGN',
            'auto_renew' => '0',
            'cancelled_at' => now()->format('Y-m-d\TH:i'),
            'notes' => null,
        ])
        ->assertRedirect(route('admin.services.index'));

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'renamed.example',
        'type' => 'hosting',
        'auto_renew' => false,
    ]);

    expect($service->refresh()->cancelled_at)->not->toBeNull();
});

test('services can be deleted', function () {
    $service = Service::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('admin.services.destroy', $service))
        ->assertRedirect(route('admin.services.index'));

    $this->assertDatabaseMissing('services', ['id' => $service->id]);
});

test('the service list can be filtered by status and type', function () {
    $account = ProviderAccount::factory()->create();
    $expired = Service::factory()->expired()->create([
        'provider_account_id' => $account->id,
        'name' => 'expired-service',
        'type' => 'hosting',
    ]);
    $domain = Service::factory()->create([
        'provider_account_id' => $account->id,
        'name' => 'domain-service',
        'type' => 'domain',
    ]);

    $this->actingAs($this->user)
        ->get(route('admin.services.index', ['status' => 'expired']))
        ->assertOk()
        ->assertSee($expired->name)
        ->assertDontSee($domain->name);

    $this->actingAs($this->user)
        ->get(route('admin.services.index', ['type' => ['vps']]))
        ->assertOk()
        ->assertDontSee($expired->name)
        ->assertDontSee($domain->name);
});

test('the service list can be searched by name', function () {
    Service::factory()->create(['name' => 'unique-widget-service']);
    Service::factory()->create(['name' => 'something-else']);

    $this->actingAs($this->user)
        ->get(route('admin.services.index', ['search' => 'unique-widget']))
        ->assertOk()
        ->assertSee('unique-widget-service')
        ->assertDontSee('something-else');
});
