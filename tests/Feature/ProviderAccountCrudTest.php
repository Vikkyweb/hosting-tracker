<?php

use App\Models\Provider;
use App\Models\ProviderAccount;
use App\Models\Service;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('admins can view the provider account list and create page', function (string $path) {
    $this->actingAs($this->user)->get($path)->assertOk();
})->with([
    '/admin/provider-accounts',
    '/admin/provider-accounts/create',
]);

test('the create form preselects the provider when requested', function () {
    $provider = Provider::factory()->create();
    Provider::factory()->create();

    $this->actingAs($this->user)
        ->get(route('admin.provider-accounts.create', ['provider_id' => $provider->id]))
        ->assertOk()
        ->assertSee("value=\"{$provider->id}\"", false);
});

test('provider accounts can be created with an encrypted password', function () {
    $provider = Provider::factory()->create();

    $this->actingAs($this->user)->post(route('admin.provider-accounts.store'), [
        'provider_id' => $provider->id,
        'account_label' => 'Main',
        'login_email' => 'ops@example.com',
        'password' => 'super-secret',
        'two_factor_notes' => 'Authenticator on phone.',
        'notes' => null,
    ])->assertRedirect(route('admin.provider-accounts.index'));

    $account = ProviderAccount::firstWhere('login_email', 'ops@example.com');

    expect($account)->not->toBeNull()
        ->and($account->password)->toBe('super-secret');

    $raw = $account->getRawOriginal('password');

    expect($raw)->not->toBe('super-secret')
        ->and(strlen($raw))->toBeGreaterThan(strlen('super-secret'));
});

test('provider account validation requires provider label email and password', function () {
    $this->actingAs($this->user)
        ->from(route('admin.provider-accounts.create'))
        ->post(route('admin.provider-accounts.store'), [])
        ->assertRedirect(route('admin.provider-accounts.create'))
        ->assertSessionHasErrors(['provider_id', 'account_label', 'login_email', 'password']);
});

test('updating without a password keeps the current password', function () {
    $account = ProviderAccount::factory()->create([
        'password' => 'original-secret',
    ]);

    $this->actingAs($this->user)
        ->put(route('admin.provider-accounts.update', $account), [
            'provider_id' => $account->provider_id,
            'account_label' => 'Renamed account',
            'login_email' => $account->login_email,
            'password' => '',
            'two_factor_notes' => null,
            'notes' => null,
        ])
        ->assertRedirect(route('admin.provider-accounts.index'));

    $account->refresh();

    expect($account->account_label)->toBe('Renamed account')
        ->and($account->password)->toBe('original-secret');
});

test('provider accounts can be deleted with their services cascading', function () {
    $account = ProviderAccount::factory()->create();
    $service = Service::factory()->create(['provider_account_id' => $account->id]);

    $this->actingAs($this->user)
        ->delete(route('admin.provider-accounts.destroy', $account))
        ->assertRedirect(route('admin.provider-accounts.index'));

    $this->assertDatabaseMissing('provider_accounts', ['id' => $account->id]);
    $this->assertDatabaseMissing('services', ['id' => $service->id]);
});

test('the provider account list can be filtered by provider', function () {
    $provider = Provider::factory()->create();
    ProviderAccount::factory()->create([
        'provider_id' => $provider->id,
        'account_label' => 'filtered-account',
    ]);
    ProviderAccount::factory()->create([
        'account_label' => 'other-account',
    ]);

    $this->actingAs($this->user)
        ->get(route('admin.provider-accounts.index', ['provider' => $provider->id]))
        ->assertOk()
        ->assertSee('filtered-account')
        ->assertDontSee('other-account');
});
