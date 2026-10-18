@extends('layouts.admin')

@section('title', 'Clients')

@section('header-actions')
    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">New client</a>
@endsection

@section('content')
    <div class="card p-4">
        <form method="GET" action="{{ route('admin.clients.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="w-full max-w-sm">
                <label for="search" class="label">Search</label>
                <input id="search" type="search" name="search" value="{{ request('search') }}"
                    placeholder="Name or email" class="input mt-1">
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
                        <th>Contact email</th>
                        <th>Contact phone</th>
                        <th><x-sort-link column="services_count" label="Services" /></th>
                        <th>Notes</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr>
                            <td>
                                <a class="font-medium text-gray-900 hover:text-amber-700"
                                    href="{{ route('admin.clients.edit', $client) }}">{{ $client->name }}</a>
                            </td>
                            <td>{{ $client->contact_email ?? '—' }}</td>
                            <td>{{ $client->contact_phone ?? '—' }}</td>
                            <td>{{ $client->services_count }}</td>
                            <td class="max-w-xs text-gray-500">{{ \Illuminate\Support\Str::limit($client->notes, 30) ?: '—' }}</td>
                            <td class="text-right whitespace-nowrap">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.clients.destroy', $client) }}"
                                        onsubmit="return confirm('Delete this client? Their services will be unassigned.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500">No clients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3">
            {{ $clients->links() }}
        </div>
    </div>
@endsection
