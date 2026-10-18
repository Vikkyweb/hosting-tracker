<?php

use App\Models\Provider;
use App\Models\ProviderAccount;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('admins can view the provider list and create page', function (string $path) {
    $this->actingAs($this->user)->get($path)->assertOk();
})->with([
    '/admin/providers',
    '/admin/providers/create',
]);

test('providers can be created', function () {
    $this->actingAs($this->user)->post(route('admin.providers.store'), [
        'name' => 'Namecheap',
        'website_url' => 'https://www.namecheap.com',
        'support_url' => 'https://www.namecheap.com/support',
        'notes' => 'Primary registrar.',
    ])->assertRedirect(route('admin.providers.index'));

    $this->assertDatabaseHas('providers', ['name' => 'Namecheap']);
});

test('provider validation requires a name and valid urls', function () {
    $this->actingAs($this->user)
        ->from(route('admin.providers.create'))
        ->post(route('admin.providers.store'), [
            'name' => '',
            'website_url' => 'not-a-url',
        ])
        ->assertRedirect(route('admin.providers.create'))
        ->assertSessionHasErrors(['name', 'website_url']);
});

test('the provider edit page lists its accounts', function () {
    $provider = Provider::factory()->create();
    $account = ProviderAccount::factory()->create([
        'provider_id' => $provider->id,
        'account_label' => 'Main login',
        'login_email' => 'ops@example.com',
    ]);

    $this->actingAs($this->user)
        ->get(route('admin.providers.edit', $provider))
        ->assertOk()
        ->assertSee($provider->name)
        ->assertSee('Main login')
        ->assertSee('ops@example.com')
        ->assertSee(route('admin.provider-accounts.create', ['provider_id' => $provider->id]), false)
        ->assertDontSee($account->password);
});

test('providers can be updated and deleted', function () {
    $provider = Provider::factory()->create();

    $this->actingAs($this->user)
        ->put(route('admin.providers.update', $provider), [
            'name' => 'Updated Provider',
            'website_url' => null,
            'support_url' => null,
            'notes' => null,
        ])
        ->assertRedirect(route('admin.providers.index'));

    $this->assertDatabaseHas('providers', [
        'id' => $provider->id,
        'name' => 'Updated Provider',
    ]);

    $this->actingAs($this->user)
        ->delete(route('admin.providers.destroy', $provider))
        ->assertRedirect(route('admin.providers.index'));

    $this->assertDatabaseMissing('providers', ['id' => $provider->id]);
});
