@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="card p-4 sm:p-5">
            <p class="text-sm font-medium text-gray-500">Total services</p>
            <p class="mt-2 text-2xl font-semibold sm:text-3xl">{{ $totalServices }}</p>
        </div>

        <div class="card border-l-4 border-l-amber-500 p-4 sm:p-5">
            <p class="text-sm font-medium text-gray-500">Expiring in {{ $days }} days</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700 sm:text-3xl">{{ $expiringSoonCount }}</p>
        </div>

        <div class="card border-l-4 border-l-red-500 p-4 sm:p-5">
            <p class="text-sm font-medium text-gray-500">Expired / overdue</p>
            <p class="mt-2 text-2xl font-semibold text-red-700 sm:text-3xl">{{ $expiredCount }}</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="border-b border-gray-200 px-4 py-4 sm:px-5">
            <h2 class="font-semibold">Expiring soon</h2>
            <p class="text-sm text-gray-500">Within the next {{ $days }} days.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="table-base min-w-[42rem]">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Account</th>
                        <th>Client</th>
                        <th>Expiry date</th>
                        <th>Days left</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expiringSoon as $service)
                        @php
                            $danger = $service->days_until_expiry <= 7;
                            $badge = $danger ? 'red' : 'amber';
                        @endphp
                        <tr>
                            <td>
                                <a class="font-medium text-gray-900 hover:text-amber-700"
                                    href="{{ route('admin.services.edit', $service) }}">{{ $service->name }}</a>
                            </td>
                            <td>
                                <x-badge>{{ $service->type->label() }}</x-badge>
                            </td>
                            <td>
                                {{ $service->providerAccount?->account_label ?? '—' }}
                                <div class="text-xs text-gray-500">{{ $service->providerAccount?->provider?->name }}</div>
                            </td>
                            <td>{{ $service->client?->name ?? '—' }}</td>
                            <td>
                                <x-badge :variant="$badge">{{ $service->expiry_date->format('F j, Y') }}</x-badge>
                            </td>
                            <td>
                                <x-badge :variant="$badge">{{ $service->days_until_expiry }}d</x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500">Nothing expiring soon.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 sm:px-5">
            {{ $expiringSoon->links() }}
        </div>
    </div>
@endsection
