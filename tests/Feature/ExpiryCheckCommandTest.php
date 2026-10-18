<?php

use App\Models\Service;

test('lists services expiring within the default window', function () {
    Service::factory()->expiringSoon(5)->create(['name' => 'soon.example']);
    Service::factory()->create(['expiry_date' => now()->addDays(200)]);

    $this->artisan('services:check-expiry')
        ->expectsOutputToContain('soon.example')
        ->doesntExpectOutputToContain('200')
        ->assertExitCode(0);
});

test('respects the days option', function () {
    Service::factory()->expiringSoon(40)->create(['name' => 'later.example']);

    $this->artisan('services:check-expiry --days=45')
        ->expectsOutputToContain('later.example')
        ->assertExitCode(0);

    $this->artisan('services:check-expiry --days=30')
        ->doesntExpectOutputToContain('later.example')
        ->assertExitCode(0);
});

test('excludes cancelled services', function () {
    Service::factory()->cancelled()->expiringSoon(3)->create(['name' => 'dropped.example']);

    $this->artisan('services:check-expiry')
        ->doesntExpectOutputToContain('dropped.example')
        ->assertExitCode(0);
});

test('excludes already expired services', function () {
    Service::factory()->expired()->create(['name' => 'past.example']);

    $this->artisan('services:check-expiry')
        ->doesntExpectOutputToContain('past.example')
        ->assertExitCode(0);
});
