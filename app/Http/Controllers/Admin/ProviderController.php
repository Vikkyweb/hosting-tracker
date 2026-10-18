<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProviderRequest;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'string', 'in:name,accounts_count'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $sort = $validated['sort'] ?? 'name';
        $direction = $validated['direction'] ?? 'asc';

        $sortable = [
            'name' => 'providers.name',
            'accounts_count' => 'accounts_count',
        ];

        $providers = Provider::query()
            ->withCount('accounts')
            ->when($validated['search'] ?? null, fn (Builder $query, string $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy($sortable[$sort], $direction)
            ->paginate(10)
            ->withQueryString();

        return view('admin.providers.index', [
            'providers' => $providers,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create(): View
    {
        return view('admin.providers.form', ['provider' => null, 'accounts' => collect()]);
    }

    public function store(ProviderRequest $request): RedirectResponse
    {
        Provider::create($request->validated());

        return redirect()->route('admin.providers.index')->with('status', 'Provider created.');
    }

    public function edit(Provider $provider): View
    {
        return view('admin.providers.form', [
            'provider' => $provider,
            'accounts' => $provider->accounts()->orderBy('account_label')->get(),
        ]);
    }

    public function update(ProviderRequest $request, Provider $provider): RedirectResponse
    {
        $provider->update($request->validated());

        return redirect()->route('admin.providers.index')->with('status', 'Provider updated.');
    }

    public function destroy(Provider $provider): RedirectResponse
    {
        $provider->delete();

        return redirect()->route('admin.providers.index')->with('status', 'Provider deleted.');
    }
}
