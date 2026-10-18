@extends('layouts.admin')

@section('title', $client ? 'Edit client' : 'New client')

@section('content')
    <form method="POST"
        action="{{ $client ? route('admin.clients.update', $client) : route('admin.clients.store') }}"
        class="card max-w-2xl space-y-6 p-6">
        @csrf
        @if ($client)
            @method('PUT')
        @endif

        @include('partials.errors')

        <div>
            <label for="name" class="label">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name', $client?->name) }}" required
                class="input mt-1 @error('name') input-error @enderror">
            @error('name')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="contact_email" class="label">Contact email</label>
            <input id="contact_email" type="email" name="contact_email"
                value="{{ old('contact_email', $client?->contact_email) }}"
                class="input mt-1 @error('contact_email') input-error @enderror">
            @error('contact_email')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="contact_phone" class="label">Contact Phone</label>
            <input id="contact_phone" type="text" name="contact_phone"
                value="{{ old('contact_phone', $client?->contact_phone) }}"
                class="input mt-1 @error('contact_phone') input-error @enderror">
            @error('contact_phone')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="notes" class="label">Notes</label>
            <textarea id="notes" name="notes" rows="3"
                class="textarea mt-1 @error('notes') input-error @enderror">{{ old('notes', $client?->notes) }}</textarea>
            @error('notes')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
            <button type="submit" class="btn btn-primary">{{ $client ? 'Save changes' : 'Create client' }}</button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
