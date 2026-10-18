<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'string', 'in:name,services_count'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $sort = $validated['sort'] ?? 'name';
        $direction = $validated['direction'] ?? 'asc';

        $sortable = [
            'name' => 'clients.name',
            'services_count' => 'services_count',
        ];

        $clients = Client::query()
            ->withCount('services')
            ->when($validated['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_email', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortable[$sort], $direction)
            ->paginate(10)
            ->withQueryString();

        return view('admin.clients.index', [
            'clients' => $clients,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create(): View
    {
        return view('admin.clients.form', ['client' => null]);
    }

    public function store(ClientRequest $request): RedirectResponse
    {
        Client::create($request->validated());

        return redirect()->route('admin.clients.index')->with('status', 'Client created.');
    }

    public function edit(Client $client): View
    {
        return view('admin.clients.form', ['client' => $client]);
    }

    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return redirect()->route('admin.clients.index')->with('status', 'Client updated.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('admin.clients.index')->with('status', 'Client deleted.');
    }
}
