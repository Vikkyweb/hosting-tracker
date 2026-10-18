<?php

namespace App\Models;

use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'provider_account_id',
    'client_id',
    'type',
    'name',
    'purchase_date',
    'expiry_date',
    'renewal_cost',
    'currency',
    'auto_renew',
    'cancelled_at',
    'notes',
])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ServiceType::class,
            'purchase_date' => 'date',
            'expiry_date' => 'date',
            'renewal_cost' => 'decimal:2',
            'auto_renew' => 'boolean',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ProviderAccount, $this>
     */
    public function providerAccount(): BelongsTo
    {
        return $this->belongsTo(ProviderAccount::class);
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Days until expiry: positive = future, negative = already past due.
     */
    protected function daysUntilExpiry(): Attribute
    {
        return Attribute::get(fn (): int => (int) now()->startOfDay()
            ->diffInDays($this->expiry_date?->copy()->startOfDay(), absolute: false));
    }

    /**
     * Derived from expiry_date (plus cancelled_at) instead of a stored column.
     */
    protected function status(): Attribute
    {
        return Attribute::get(function (): ServiceStatus {
            if ($this->cancelled_at !== null) {
                return ServiceStatus::Cancelled;
            }

            return $this->expiry_date?->lt(today()) ?? false
                ? ServiceStatus::Expired
                : ServiceStatus::Active;
        });
    }
}
