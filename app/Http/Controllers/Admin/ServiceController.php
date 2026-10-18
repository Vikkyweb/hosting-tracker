<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Client;
use App\Models\Provider;
use App\Models\ProviderAccount;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * @var array<string, string>
     */
    private const SORTABLE = [
        'name' => 'services.name',
        'type' => 'services.type',
        'account' => 'provider_accounts.account_label',
        'expiry_date' => 'services.expiry_date',
        'renewal_cost' => 'services.renewal_cost',
    ];

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'array'],
            'type.*' => ['string', 'in:'.implode(',', array_keys(ServiceType::options()))],
            'provider' => ['nullable', 'integer', 'exists:providers,id'],
            'status' => ['nullable', 'string', 'in:'.implode(',', array_keys(ServiceStatus::options()))],
            'expiring_soon' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'string', 'in:'.implode(',', array_keys(self::SORTABLE))],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $sort = $validated['sort'] ?? 'expiry_date';
        $direction = $validated['direction'] ?? 'asc';

        $services = Service::query()
            ->with(['providerAccount.provider', 'client'])
            ->when($validated['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('services.name', 'like', "%{$search}%")
                        ->orWhereHas('providerAccount', fn (Builder $q) => $q->where('account_label', 'like', "%{$search}%"))
                        ->orWhereHas('client', fn (Builder $q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->when(! empty($validated['type']), fn (Builder $query) => $query->whereIn('type', $validated['type']))
            ->when($validated['provider'] ?? null, function (Builder $query, int $providerId) {
                $query->whereHas('providerAccount', fn (Builder $q) => $q->where('provider_id', $providerId));
            })
            ->when($validated['status'] ?? null, fn (Builder $query, string $status) => $this->applyStatusFilter($query, $status))
            ->when($request->boolean('expiring_soon'), function (Builder $query) {
                $days = (int) config('services.expiry_check_days', 30);

                $query->whereNull('cancelled_at')
                    ->whereBetween('expiry_date', [today(), today()->addDays($days)]);
            })
            ->tap(function (Builder $query) use ($sort, $direction) {
                if ($sort === 'account') {
                    $query->whereHas('providerAccount')->orderBy(
                        ProviderAccount::select('account_label')
                            ->whereColumn('provider_accounts.id', 'services.provider_account_id'),
                        $direction,
                    );

                    return;
                }

                $query->orderBy(self::SORTABLE[$sort], $direction);
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.services.index', [
            'services' => $services,
            'providers' => Provider::query()->orderBy('name')->get(),
            'typeOptions' => ServiceType::options(),
            'statusOptions' => ServiceStatus::options(),
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create(): View
    {
        return view('admin.services.form', $this->formData());
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        Service::create($request->validated());

        return redirect()->route('admin.services.index')->with('status', 'Service created.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.form', $this->formData($service));
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->validated());

        return redirect()->route('admin.services.index')->with('status', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('status', 'Service deleted.');
    }

    private function applyStatusFilter(Builder $query, string $status): Builder
    {
        match ($status) {
            ServiceStatus::Active->value => $query->whereNull('cancelled_at')->whereDate('expiry_date', '>=', today()),
            ServiceStatus::Expired->value => $query->whereNull('cancelled_at')->whereDate('expiry_date', '<', today()),
            ServiceStatus::Cancelled->value => $query->whereNotNull('cancelled_at'),
            default => null,
        };

        return $query;
    }

    /**
     * @return array{service: Service|null, providerAccounts: Collection, clients: Collection, typeOptions: array<string, string>}
     */
    private function formData(?Service $service = null): array
    {
        return [
            'service' => $service,
            'providerAccounts' => ProviderAccount::query()
                ->with('provider')
                ->get()
                ->mapWithKeys(fn (ProviderAccount $account) => [
                    $account->id => "{$account->account_label} — {$account->provider?->name}",
                ]),
            'clients' => Client::query()->orderBy('name')->pluck('name', 'id'),
            'typeOptions' => ServiceType::options(),
        ];
    }
}
