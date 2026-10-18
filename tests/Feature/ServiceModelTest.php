<?php

use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use App\Models\ProviderAccount;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

test('status is active when expiry date is in the future', function () {
    $service = Service::factory()->create(['expiry_date' => now()->addDay()]);

    expect($service->status)->toBe(ServiceStatus::Active);
});

test('status is active when expiry date is today', function () {
    $service = Service::factory()->create(['expiry_date' => today()]);

    expect($service->status)->toBe(ServiceStatus::Active);
});

test('status is expired when expiry date is in the past', function () {
    $service = Service::factory()->expired()->create();

    expect($service->status)->toBe(ServiceStatus::Expired);
});

test('status is cancelled when cancelled_at is set', function () {
    $service = Service::factory()->cancelled()->create([
        'expiry_date' => now()->addDays(30),
    ]);

    expect($service->status)->toBe(ServiceStatus::Cancelled);
});

test('days until expiry is positive for future dates and negative for past dates', function () {
    $future = Service::factory()->create(['expiry_date' => today()->addDays(5)]);
    $past = Service::factory()->expired()->create(['expiry_date' => today()->subDays(5)]);

    expect($future->days_until_expiry)->toBe(5)
        ->and($past->days_until_expiry)->toBe(-5);
});

test('type casts to the service type enum', function () {
    $service = Service::factory()->create(['type' => ServiceType::Vps->value]);

    expect($service->type)->toBe(ServiceType::Vps);
});

test('provider account passwords and two factor notes are encrypted at rest', function () {
    $account = ProviderAccount::factory()->create([
        'password' => 'super-secret',
        'two_factor_notes' => 'authenticator on phone',
    ]);

    $raw = DB::table('provider_accounts')->where('id', $account->id)->first();

    expect($raw->password)->not->toBe('super-secret')
        ->and($raw->two_factor_notes)->not->toBe('authenticator on phone');

    $fresh = $account->fresh();

    expect($fresh->password)->toBe('super-secret')
        ->and($fresh->two_factor_notes)->toBe('authenticator on phone');
});

test('sensitive account fields are hidden from serialization', function () {
    $account = ProviderAccount::factory()->create();
    $array = $account->toArray();

    expect($array)->not->toHaveKey('password')
        ->and($array)->not->toHaveKey('two_factor_notes');
});
