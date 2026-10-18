<?php

use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\HostingTrackerSeeder;

test('the database seeder can run repeatedly without failing', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(User::where('email', 'test@example.com')->count())->toBe(1);
});

test('the sample data seeder does not duplicate rows when run twice', function () {
    $this->seed(HostingTrackerSeeder::class);
    $this->seed(HostingTrackerSeeder::class);

    expect(Provider::count())->toBe(3)
        ->and(Service::count())->toBe(9);
});
