<?php

namespace App\Http\Requests;

use App\Enums\ServiceType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(ServiceType::class)],
            'provider_account_id' => ['required', 'integer', 'exists:provider_accounts,id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'purchase_date' => ['required', 'date'],
            'expiry_date' => ['required', 'date'],
            'renewal_cost' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'auto_renew' => ['nullable', 'boolean'],
            'cancelled_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'currency.regex' => 'The currency must be an uppercase ISO 4217 code, e.g. NGN, USD.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('currency') && is_string($this->currency)) {
            $this->merge(['currency' => strtoupper(trim($this->currency))]);
        }

        if (! $this->has('auto_renew')) {
            $this->merge(['auto_renew' => false]);
        }
    }
}
