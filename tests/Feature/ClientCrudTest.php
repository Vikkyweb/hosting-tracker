<?php

use App\Models\Client;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('admins can view the client list and create page', function (string $path) {
    $this->actingAs($this->user)->get($path)->assertOk();
})->with([
    '/admin/clients',
    '/admin/clients/create',
]);

test('clients can be created', function () {
    $this->actingAs($this->user)->post(route('admin.clients.store'), [
        'name' => 'Acme Ltd',
        'contact_email' => 'it@acme.example',
        'notes' => 'Long-standing client.',
    ])->assertRedirect(route('admin.clients.index'));

    $this->assertDatabaseHas('clients', [
        'name' => 'Acme Ltd',
        'contact_email' => 'it@acme.example',
    ]);
});

test('client validation requires a name', function () {
    $this->actingAs($this->user)
        ->from(route('admin.clients.create'))
        ->post(route('admin.clients.store'), ['contact_email' => 'not-an-email'])
        ->assertRedirect(route('admin.clients.create'))
        ->assertSessionHasErrors(['name', 'contact_email']);
});

test('clients can be updated and deleted', function () {
    $client = Client::factory()->create();

    $this->actingAs($this->user)
        ->put(route('admin.clients.update', $client), [
            'name' => 'Renamed Client',
            'contact_email' => null,
            'notes' => null,
        ])
        ->assertRedirect(route('admin.clients.index'));

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'name' => 'Renamed Client',
        'contact_email' => null,
    ]);

    $this->actingAs($this->user)
        ->delete(route('admin.clients.destroy', $client))
        ->assertRedirect(route('admin.clients.index'));

    $this->assertDatabaseMissing('clients', ['id' => $client->id]);
});

test('the client list is searchable', function () {
    Client::factory()->create(['name' => 'Searchable Client']);
    Client::factory()->create(['name' => 'Other Client']);

    $this->actingAs($this->user)
        ->get(route('admin.clients.index', ['search' => 'Searchable']))
        ->assertOk()
        ->assertSee('Searchable Client')
        ->assertDontSee('Other Client');
});
