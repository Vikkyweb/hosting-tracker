<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProviderAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isCreating = $this->route('providerAccount') === null;

        return [
            'provider_id' => ['required', 'integer', 'exists:providers,id'],
            'account_label' => ['required', 'string', 'max:255'],
            'login_email' => ['required', 'email', 'max:255'],
            'password' => [$isCreating ? 'required' : 'nullable', 'string'],
            'two_factor_notes' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
