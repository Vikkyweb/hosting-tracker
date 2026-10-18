<?php

use App\Models\User;

test('the application root redirects to the dashboard', function () {
    $this->get('/')->assertRedirect(route('dashboard'));
});

test('guests are redirected to the login page', function () {
    $this->get('/admin')->assertRedirect(route('login'));
});

test('guests cannot access resource pages', function (string $path) {
    $this->get($path)->assertRedirect(route('login'));
})->with([
    '/admin/services',
    '/admin/services/create',
    '/admin/clients',
    '/admin/clients/create',
    '/admin/providers',
    '/admin/providers/create',
    '/admin/provider-accounts',
    '/admin/provider-accounts/create',
]);

test('users can log in with valid credentials', function () {
    $user = User::factory()->create([
        'password' => 'secret-password',
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('login fails with invalid credentials', function () {
    $user = User::factory()->create([
        'password' => 'secret-password',
    ]);

    $this->from('/login')
        ->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
        ->assertRedirect('/login')
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('authenticated users are redirected away from the login page', function () {
    $this->actingAs(User::factory()->create())
        ->get('/login')
        ->assertRedirect(route('dashboard'));
});

test('users can log out', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
