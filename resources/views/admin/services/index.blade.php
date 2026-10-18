@extends('layouts.admin')

@section('title', 'Services')

@section('header-actions')
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">New service</a>
@endsection

@section('content')
    <div class="card p-4">
        <form method="GET" action="{{ route('admin.services.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <div class="lg:col-span-2">
                <label for="search" class="label">Search</label>
                <input id="search" type="search" name="search" value="{{ request('search') }}"
                    placeholder="Name, account or client" class="input mt-1">
            </div>

            <div>
                <label for="provider" class="label">Provider</label>
                <select id="provider" name="provider" class="input mt-1">
                    <option value="">All</option>
                    @foreach ($providers as $provider)
                        <option value="{{ $provider->id }}" @selected(request('provider') == $provider->id)>
                            {{ $provider->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="label">Status</label>
                <select id="status" name="status" class="input mt-1">
                    <option value="">All</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <span class="label">Type</span>
                <div class="mt-1 flex flex-wrap gap-2">
                    @foreach ($typeOptions as $value => $label)
                        <label class="inline-flex items-center gap-1 text-sm text-gray-700">
                            <input type="checkbox" name="type[]" value="{{ $value }}"
                                @checked(in_array($value, request('type', []), true))
                                class="rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-end gap-3">
                <label class="inline-flex items-center gap-2 pb-2 text-sm text-gray-700">
                    <input type="hidden" name="expiring_soon" value="0">
                    <input type="checkbox" name="expiring_soon" value="1"
                        @checked(request()->boolean('expiring_soon'))
                        class="rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                    Expiring soon
                </label>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </div>

            @if (request()->hasAny(['search', 'provider', 'status', 'type', 'expiring_soon']))
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th><x-sort-link column="name" label="Name" /></th>
                        <th><x-sort-link column="type" label="Type" /></th>
                        <th><x-sort-link column="account" label="Account" /></th>
                        <th>Client</th>
                        <th><x-sort-link column="expiry_date" label="Expiry date" /></th>
                        <th>Days left</th>
                        <th>Status</th>
                        <th><x-sort-link column="renewal_cost" label="Cost" /></th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $service)
                        @php
                            $cancelled = $service->cancelled_at !== null;
                            $expired = $service->expiry_date->lt(today());
                            $soon = $service->days_until_expiry <= 30;
                            $expiryBadge = $cancelled ? 'gray' : ($expired ? 'red' : ($soon ? 'amber' : 'green'));
                        @endphp
                        <tr>
                            <td>
                                <a class="font-medium text-gray-900 hover:text-amber-700"
                                    href="{{ route('admin.services.edit', $service) }}">{{ $service->name }}</a>
                                @if ($service->auto_renew)
                                    <span class="ml-1 text-xs text-gray-400">auto-renew</span>
                                @endif
                            </td>
                            <td><x-badge :variant="match ($service->type->value) {
                                'domain' => 'blue',
                                'hosting' => 'indigo',
                                'vps' => 'amber',
                                'ssl' => 'green',
                                default => 'gray',
                            }">{{ $service->type->label() }}</x-badge></td>
                            <td>
                                {{ $service->providerAccount?->account_label ?? '—' }}
                                <div class="text-xs text-gray-500">{{ $service->providerAccount?->provider?->name }}</div>
                            </td>
                            <td>{{ $service->client?->name ?? '—' }}</td>
                            <td><x-badge :variant="$expiryBadge">{{ $service->expiry_date->format('F j, Y') }}</x-badge></td>
                            <td><x-badge :variant="$expiryBadge">{{ $service->days_until_expiry }}d</x-badge></td>
                            <td><x-badge :variant="$service->status->value === 'active' ? 'green' : ($service->status->value === 'expired' ? 'red' : 'gray')">{{ $service->status->label() }}</x-badge></td>
                            <td>{{ $service->renewal_cost !== null ? number_format((float) $service->renewal_cost, 2).' '.$service->currency : '—' }}</td>
                            <td class="text-right whitespace-nowrap">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
                                        onsubmit="return confirm('Delete this service?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-gray-500">No services found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3">
            {{ $services->links() }}
        </div>
    </div>
@endsection
