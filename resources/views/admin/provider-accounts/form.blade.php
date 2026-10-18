@extends('layouts.admin')

@section('title', $account ? 'Edit account' : 'New account')

@section('content')
    <form method="POST"
        action="{{ $account ? route('admin.provider-accounts.update', $account) : route('admin.provider-accounts.store') }}"
        class="card max-w-2xl space-y-6 p-6">
        @csrf
        @if ($account)
            @method('PUT')
        @endif

        @include('partials.errors')

        <div>
            <label for="provider_id" class="label">Provider</label>
            <select id="provider_id" name="provider_id" required
                class="input mt-1 @error('provider_id') input-error @enderror">
                <option value="">Select a provider</option>
                @foreach ($providers as $provider)
                    <option value="{{ $provider->id }}"
                        @selected(old('provider_id', $account?->provider_id ?? $preselectedProviderId) == $provider->id)>
                        {{ $provider->name }}
                    </option>
                @endforeach
            </select>
            @error('provider_id')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="account_label" class="label">Account label</label>
            <input id="account_label" type="text" name="account_label"
                value="{{ old('account_label', $account?->account_label) }}" required
                class="input mt-1 @error('account_label') input-error @enderror">
            @error('account_label')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="login_email" class="label">Login email</label>
            <input id="login_email" type="email" name="login_email" value="{{ old('login_email', $account?->login_email) }}"
                required class="input mt-1 @error('login_email') input-error @enderror">
            @error('login_email')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="label">Password</label>
            <input id="password" type="password" name="password"
                {{ $account ? '' : 'required' }} autocomplete="new-password"
                class="input mt-1 @error('password') input-error @enderror">
            @if ($account)
                <p class="field-help">Stored encrypted. Leave blank to keep the current password.</p>
            @endif
            @error('password')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="two_factor_notes" class="label">2FA notes</label>
            <textarea id="two_factor_notes" name="two_factor_notes" rows="2"
                class="textarea mt-1 @error('two_factor_notes') input-error @enderror">{{ old('two_factor_notes', $account?->two_factor_notes) }}</textarea>
            <p class="field-help">Stored encrypted. Never shown in tables or logs.</p>
            @error('two_factor_notes')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="notes" class="label">Notes</label>
            <textarea id="notes" name="notes" rows="3"
                class="textarea mt-1 @error('notes') input-error @enderror">{{ old('notes', $account?->notes) }}</textarea>
            @error('notes')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
            <button type="submit" class="btn btn-primary">{{ $account ? 'Save changes' : 'Create account' }}</button>
            <a href="{{ route('admin.provider-accounts.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
