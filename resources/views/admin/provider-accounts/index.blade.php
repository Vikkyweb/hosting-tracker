@extends('layouts.admin')

@section('title', 'Provider accounts')

@section('header-actions')
    <a href="{{ route('admin.provider-accounts.create') }}" class="btn btn-primary">New account</a>
@endsection

@section('content')
    <div class="card p-4">
        <form method="GET" action="{{ route('admin.provider-accounts.index') }}"
            class="flex flex-wrap items-end gap-4">
            <div class="w-full max-w-sm">
                <label for="search" class="label">Search</label>
                <input id="search" type="search" name="search" value="{{ request('search') }}"
                    placeholder="Label, email or provider" class="input mt-1">
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

            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th><x-sort-link column="provider" label="Provider" /></th>
                        <th><x-sort-link column="account_label" label="Account label" /></th>
                        <th>Login email</th>
                        <th>Notes</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $account)
                        <tr>
                            <td>
                                <a class="text-gray-700 hover:text-amber-700"
                                    href="{{ route('admin.providers.edit', $account->provider_id) }}">{{ $account->provider?->name ?? '—' }}</a>
                            </td>
                            <td>
                                <a class="font-medium text-gray-900 hover:text-amber-700"
                                    href="{{ route('admin.provider-accounts.edit', $account) }}">{{ $account->account_label }}</a>
                            </td>
                            <td>{{ $account->login_email }}</td>
                            <td class="max-w-xs text-gray-500">{{ \Illuminate\Support\Str::limit($account->notes, 30) ?: '—' }}</td>
                            <td class="text-right whitespace-nowrap">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.provider-accounts.edit', $account) }}"
                                        class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.provider-accounts.destroy', $account) }}"
                                        onsubmit="return confirm('Delete this account? Its services will also be deleted.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500">No accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3">
            {{ $accounts->links() }}
        </div>
    </div>
@endsection
