<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class HostingTrackerSeeder extends Seeder
{
    /**
     * Seed sample providers, accounts, clients, and services.
     */
    public function run(): void
    {
        $alreadySeeded = Provider::query()
            ->whereIn('name', ['Namecheap', 'Hostinger', 'DigitalOcean'])
            ->exists();

        if ($alreadySeeded) {
            return;
        }

        $namecheap = Provider::create([
            'name' => 'Namecheap',
            'website_url' => 'https://www.namecheap.com',
            'support_url' => 'https://www.namecheap.com/support',
            'notes' => 'Primary domain registrar.',
        ]);

        $namecheapMain = $namecheap->accounts()->create([
            'account_label' => 'Main',
            'login_email' => 'admin@example.com',
            'password' => 'namecheap-main-secret',
            'two_factor_notes' => 'Authenticator app on personal phone.',
            'notes' => 'Personal domains live here.',
        ]);

        $namecheap->accounts()->create([
            'account_label' => 'Client reseller acct',
            'login_email' => 'reseller@example.com',
            'password' => 'namecheap-reseller-secret',
        ]);

        $hostinger = Provider::create([
            'name' => 'Hostinger',
            'website_url' => 'https://www.hostinger.com',
            'support_url' => 'https://www.hostinger.com/support',
        ]);

        $hostingerMain = $hostinger->accounts()->create([
            'account_label' => 'Main',
            'login_email' => 'billing@example.com',
            'password' => 'hostinger-main-secret',
            'two_factor_notes' => 'Backup codes in password manager.',
        ]);

        $digitalocean = Provider::create([
            'name' => 'DigitalOcean',
            'website_url' => 'https://www.digitalocean.com',
            'support_url' => 'https://www.digitalocean.com/support',
            'notes' => 'VPS infrastructure.',
        ]);

        $doMain = $digitalocean->accounts()->create([
            'account_label' => 'Main',
            'login_email' => 'ops@example.com',
            'password' => 'digitalocean-main-secret',
        ]);

        $clientA = Client::create([
            'name' => 'Acme Ltd',
            'contact_email' => 'it@acme.example',
            'notes' => 'Long-standing client.',
        ]);

        $clientB = Client::create([
            'name' => 'Applyqik',
            'contact_email' => 'hello@applyqik.example',
        ]);

        $services = [
            // Past due
            ['account' => $namecheapMain, 'client' => null, 'type' => 'domain', 'name' => 'myownsite.com', 'expiry' => '-45 days', 'cost' => 12.98],
            ['account' => $hostingerMain, 'client' => $clientA, 'type' => 'hosting', 'name' => 'Acme shared hosting', 'expiry' => '-7 days', 'cost' => 59.99],
            // Near expiry
            ['account' => $namecheapMain, 'client' => $clientA, 'type' => 'domain', 'name' => 'acme-site.com', 'expiry' => '+6 days', 'cost' => 15.10],
            ['account' => $doMain, 'client' => $clientB, 'type' => 'vps', 'name' => 'Applyqik VPS', 'expiry' => '+14 days', 'cost' => 48.00],
            ['account' => $hostingerMain, 'client' => null, 'type' => 'domain', 'name' => 'personaldomain.net', 'expiry' => '+25 days', 'cost' => 9.99],
            ['account' => $doMain, 'client' => $clientA, 'type' => 'ssl', 'name' => 'Acme wildcard SSL', 'expiry' => '+29 days', 'cost' => 79.00],
            // Far future
            ['account' => $namecheapMain, 'client' => $clientB, 'type' => 'domain', 'name' => 'applyqik.com', 'expiry' => '+120 days', 'cost' => 13.10],
            ['account' => $hostingerMain, 'client' => $clientA, 'type' => 'hosting', 'name' => 'Acme business hosting', 'expiry' => '+300 days', 'cost' => 119.00],
            ['account' => $doMain, 'client' => null, 'type' => 'vps', 'name' => 'Staging VPS', 'expiry' => '+400 days', 'cost' => 36.00],
        ];

        foreach ($services as $service) {
            Service::create([
                'provider_account_id' => $service['account']->id,
                'client_id' => $service['client']?->id,
                'type' => $service['type'],
                'name' => $service['name'],
                'purchase_date' => now()->subMonths(6)->toDateString(),
                'expiry_date' => Carbon::parse($service['expiry'])->toDateString(),
                'renewal_cost' => $service['cost'],
                'currency' => 'NGN',
                'auto_renew' => false,
                'notes' => null,
            ]);
        }
    }
}
