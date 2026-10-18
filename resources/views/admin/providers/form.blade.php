@extends('layouts.admin')

@section('title', $provider ? 'Edit provider' : 'New provider')

@section('content')
    <form method="POST"
        action="{{ $provider ? route('admin.providers.update', $provider) : route('admin.providers.store') }}"
        class="card max-w-2xl space-y-6 p-6">
        @csrf
        @if ($provider)
            @method('PUT')
        @endif

        @include('partials.errors')

        <div>
            <label for="name" class="label">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name', $provider?->name) }}" required
                class="input mt-1 @error('name') input-error @enderror">
            @error('name')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="website_url" class="label">Website URL</label>
            <input id="website_url" type="url" name="website_url" value="{{ old('website_url', $provider?->website_url) }}"
                class="input mt-1 @error('website_url') input-error @enderror">
            @error('website_url')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="support_url" class="label">Support URL</label>
            <input id="support_url" type="url" name="support_url" value="{{ old('support_url', $provider?->support_url) }}"
                class="input mt-1 @error('support_url') input-error @enderror">
            @error('support_url')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="notes" class="label">Notes</label>
            <textarea id="notes" name="notes" rows="3"
                class="textarea mt-1 @error('notes') input-error @enderror">{{ old('notes', $provider?->notes) }}</textarea>
            @error('notes')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
            <button type="submit" class="btn btn-primary">{{ $provider ? 'Save changes' : 'Create provider' }}</button>
            <a href="{{ route('admin.providers.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    @if ($provider)
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <div>
                    <h2 class="font-semibold">Accounts</h2>
                    <p class="text-sm text-gray-500">Logins for {{ $provider->name }}.</p>
                </div>
                <a href="{{ route('admin.provider-accounts.create', ['provider_id' => $provider->id]) }}"
                    class="btn btn-secondary btn-sm">Add account</a>
            </div>

            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Account label</th>
                            <th>Login email</th>
                            <th>Notes</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accounts as $account)
                            <tr>
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
                                            onsubmit="return confirm('Delete this account?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-gray-500">No accounts yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
