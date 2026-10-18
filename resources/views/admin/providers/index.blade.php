@extends('layouts.admin')

@section('title', 'Providers')

@section('header-actions')
    <a href="{{ route('admin.providers.create') }}" class="btn btn-primary">New provider</a>
@endsection

@section('content')
    <div class="card p-4">
        <form method="GET" action="{{ route('admin.providers.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="w-full max-w-sm">
                <label for="search" class="label">Search</label>
                <input id="search" type="search" name="search" value="{{ request('search') }}" placeholder="Name"
                    class="input mt-1">
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th><x-sort-link column="name" label="Name" /></th>
                        <th>Website URL</th>
                        <th>Support URL</th>
                        <th><x-sort-link column="accounts_count" label="Accounts" /></th>
                        <th>Notes</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($providers as $provider)
                        <tr>
                            <td>
                                <a class="font-medium text-gray-900 hover:text-amber-700"
                                    href="{{ route('admin.providers.edit', $provider) }}">{{ $provider->name }}</a>
                            </td>
                            <td>
                                @if ($provider->website_url)
                                    <a href="{{ $provider->website_url }}" target="_blank" rel="noopener"
                                        class="text-amber-700 hover:underline">{{ \Illuminate\Support\Str::limit($provider->website_url, 30) }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if ($provider->support_url)
                                    <a href="{{ $provider->support_url }}" target="_blank" rel="noopener"
                                        class="text-amber-700 hover:underline">{{ \Illuminate\Support\Str::limit($provider->support_url, 30) }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $provider->accounts_count }}</td>
                            <td class="max-w-xs text-gray-500">{{ \Illuminate\Support\Str::limit($provider->notes, 30) ?: '—' }}</td>
                            <td class="text-right whitespace-nowrap">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.providers.edit', $provider) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.providers.destroy', $provider) }}"
                                        onsubmit="return confirm('Delete this provider? Their accounts and services will also be deleted.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500">No providers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3">
            {{ $providers->links() }}
        </div>
    </div>
@endsection
