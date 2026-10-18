<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpiryCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:check-expiry {--days= : Number of days ahead to check (defaults to EXPIRY_CHECK_DAYS, 30)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List services expiring within the next N days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('services.expiry_check_days', 30));

        $services = Service::query()
            ->with(['providerAccount.provider', 'client'])
            ->whereNull('cancelled_at')
            ->whereDate('expiry_date', '>=', today())
            ->whereDate('expiry_date', '<=', today()->addDays($days))
            ->orderBy('expiry_date')
            ->get();

        if ($services->isEmpty()) {
            $this->info("No services expiring in the next {$days} days.");

            return self::SUCCESS;
        }

        $this->info("Services expiring in the next {$days} days ({$services->count()}):");
        $this->newLine();

        foreach ($services as $service) {
            $this->line(sprintf(
                ' - %s [%s] expires %s (%d days) — account: %s @ %s',
                $service->name,
                $service->type->value,
                $service->expiry_date->toDateString(),
                $service->days_until_expiry,
                $service->providerAccount->account_label,
                $service->providerAccount->provider->name,
            ));

            // Future: swap this single line for
            // Notification::send($admin, new ServiceExpiringNotification($service));
            Log::info('Service expiring soon', [
                'service_id' => $service->id,
                'name' => $service->name,
                'expiry_date' => $service->expiry_date->toDateString(),
                'days_until_expiry' => $service->days_until_expiry,
            ]);
        }

        return self::SUCCESS;
    }
}
