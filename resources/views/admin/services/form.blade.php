@extends('layouts.admin')

@section('title', $service ? 'Edit service' : 'New service')

@section('content')
    <form method="POST"
        action="{{ $service ? route('admin.services.update', $service) : route('admin.services.store') }}"
        class="card max-w-4xl space-y-6 p-6">
        @csrf
        @if ($service)
            @method('PUT')
        @endif

        @include('partials.errors')

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label for="name" class="label">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $service?->name) }}" required
                    class="input mt-1 @error('name') input-error @enderror">
                @error('name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="type" class="label">Type</label>
                <select id="type" name="type" required class="input mt-1 @error('type') input-error @enderror">
                    @foreach ($typeOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('type', $service?->type?->value ?? 'domain') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="provider_account_id" class="label">Provider account</label>
                <select id="provider_account_id" name="provider_account_id" required
                    class="input mt-1 @error('provider_account_id') input-error @enderror">
                    <option value="">Select an account</option>
                    @foreach ($providerAccounts as $id => $label)
                        <option value="{{ $id }}" @selected(old('provider_account_id', $service?->provider_account_id) == $id)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('provider_account_id')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="client_id" class="label">Client</label>
                <select id="client_id" name="client_id" class="input mt-1 @error('client_id') input-error @enderror">
                    <option value="">Unassigned (personal)</option>
                    @foreach ($clients as $id => $name)
                        <option value="{{ $id }}" @selected(old('client_id', $service?->client_id) == $id)>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                <p class="field-help">Leave empty for personal / unassigned services.</p>
                @error('client_id')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="purchase_date" class="label">Date</label>
                <input id="purchase_date" type="date" name="purchase_date" required
                    value="{{ old('purchase_date', $service?->purchase_date?->format('Y-m-d')) }}"
                    class="input mt-1 @error('purchase_date') input-error @enderror">
                @error('purchase_date')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="expiry_date" class="label">Expiry date</label>
                <input id="expiry_date" type="date" name="expiry_date" required
                    value="{{ old('expiry_date', $service?->expiry_date?->format('Y-m-d')) }}"
                    class="input mt-1 @error('expiry_date') input-error @enderror">
                @error('expiry_date')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="renewal_cost" class="label">Cost</label>
                <input id="renewal_cost" type="number" name="renewal_cost" step="0.01" min="0"
                    value="{{ old('renewal_cost', $service?->renewal_cost) }}"
                    class="input mt-1 @error('renewal_cost') input-error @enderror">
                @error('renewal_cost')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="currency" class="label">Currency</label>
                <input id="currency" type="text" name="currency" maxlength="3"
                    value="{{ old('currency', $service?->currency ?? 'NGN') }}" required
                    class="input mt-1 uppercase @error('currency') input-error @enderror">
                <p class="field-help">ISO 4217 code, e.g. NGN, USD.</p>
                @error('currency')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="cancelled_at" class="label">Cancelled at</label>
                <input id="cancelled_at" type="datetime-local" name="cancelled_at"
                    value="{{ old('cancelled_at', $service?->cancelled_at?->format('Y-m-d\TH:i')) }}"
                    class="input mt-1 @error('cancelled_at') input-error @enderror">
                @error('cancelled_at')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2 pt-6">
                <input type="hidden" name="auto_renew" value="0">
                <input id="auto_renew" type="checkbox" name="auto_renew" value="1"
                    @checked(old('auto_renew', $service?->auto_renew))
                    class="rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                <label for="auto_renew" class="text-sm font-medium text-gray-900">Auto renew</label>
            </div>

            <div class="sm:col-span-2">
                <label for="notes" class="label">Notes</label>
                <textarea id="notes" name="notes" rows="3"
                    class="textarea mt-1 @error('notes') input-error @enderror">{{ old('notes', $service?->notes) }}</textarea>
                @error('notes')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
            <button type="submit" class="btn btn-primary">{{ $service ? 'Save changes' : 'Create service' }}</button>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
