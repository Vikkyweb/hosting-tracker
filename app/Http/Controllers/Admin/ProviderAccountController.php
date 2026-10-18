<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProviderAccountRequest;
use App\Models\Provider;
use App\Models\ProviderAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ProviderAccountController extends Controller
{
    /**
     * @var array<string, string>
     */
    private const SORTABLE = [
        'provider' => 'providers.name',
        'account_label' => 'provider_accounts.account_label',
    ];

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'provider' => ['nullable', 'integer', 'exists:providers,id'],
            'sort' => ['nullable', 'string', 'in:'.implode(',', array_keys(self::SORTABLE))],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $sort = $validated['sort'] ?? 'provider';
        $direction = $validated['direction'] ?? 'asc';

        $accounts = ProviderAccount::query()
            ->with('provider')
            ->when($validated['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('account_label', 'like', "%{$search}%")
                        ->orWhere('login_email', 'like', "%{$search}%")
                        ->orWhereHas('provider', fn (Builder $q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($validated['provider'] ?? null, fn (Builder $query, int $providerId) => $query->where('provider_id', $providerId))
            ->tap(function (Builder $query) use ($sort, $direction) {
                if ($sort === 'provider') {
                    $query->join('providers', 'providers.id', '=', 'provider_accounts.provider_id')
                        ->orderBy('providers.name', $direction)
                        ->select('provider_accounts.*');

                    return;
                }

                $query->orderBy(self::SORTABLE[$sort], $direction);
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.provider-accounts.index', [
            'accounts' => $accounts,
            'providers' => Provider::query()->orderBy('name')->get(),
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.provider-accounts.form', $this->formData(
            account: null,
            preselectedProviderId: $request->integer('provider_id') ?: null,
        ));
    }

    public function store(ProviderAccountRequest $request): RedirectResponse
    {
        ProviderAccount::create($request->validated());

        return redirect()->route('admin.provider-accounts.index')->with('status', 'Account created.');
    }

    public function edit(ProviderAccount $providerAccount): View
    {
        return view('admin.provider-accounts.form', $this->formData($providerAccount));
    }

    public function update(ProviderAccountRequest $request, ProviderAccount $providerAccount): RedirectResponse
    {
        $data = $request->validated();

        if ($data['password'] === null) {
            unset($data['password']);
        }

        $providerAccount->update($data);

        return redirect()->route('admin.provider-accounts.index')->with('status', 'Account updated.');
    }

    public function destroy(ProviderAccount $providerAccount): RedirectResponse
    {
        $providerAccount->delete();

        return redirect()->route('admin.provider-accounts.index')->with('status', 'Account deleted.');
    }

    /**
     * @return array{account: ProviderAccount|null, providers: Collection, preselectedProviderId: int|null}
     */
    private function formData(?ProviderAccount $account = null, ?int $preselectedProviderId = null): array
    {
        return [
            'account' => $account,
            'providers' => Provider::query()->orderBy('name')->get(['id', 'name']),
            'preselectedProviderId' => $preselectedProviderId,
        ];
    }
}
