# Project Brief: Hosting & Domain Tracker (for Claude Code)

## Goal
An internal tool (single admin user, me) to track every hosting/domain account
across multiple providers, the login credentials for each, and purchase/expiry
dates — so I stop losing track of where a given client's domain/hosting lives.
Must be architected so email expiry notifications can be added later without
restructuring.

## Stack
- Laravel 11
- Filament 3 (admin panel package) for all CRUD UI
- MySQL
- Laravel's built-in Notification system (mail channel, added later) +
  Task Scheduler for the daily expiry check

## Setup steps
1. `composer create-project laravel/laravel hosting-tracker`
2. `cd hosting-tracker`
3. `composer require filament/filament:"^3.2"`
4. `php artisan filament:install --panels`
5. `php artisan make:filament-user` (create my admin login)
6. Configure `.env` for local MySQL

## Data model

### providers
- id
- name (e.g. Namecheap, Hostinger, DigitalOcean, GoDaddy)
- website_url (nullable)
- support_url (nullable)
- notes (text, nullable)

### provider_accounts
(handles the "5 different logins at one company" case)
- id
- provider_id (FK)
- account_label (e.g. "Main", "Client X reseller acct")
- login_email
- password — **cast as `encrypted`** in the model, never plaintext
- two_factor_notes (text, nullable, also encrypted)
- notes (text, nullable)

### services
(the actual domain or hosting plan)
- id
- provider_account_id (FK)
- client_id (FK, nullable — for unassigned/own services)
- type (enum: domain, hosting, vps, ssl, email, other)
- name (e.g. "clientsite.com", "Applyqik VPS")
- purchase_date (date)
- expiry_date (date)
- renewal_cost (decimal, nullable)
- currency (default NGN)
- auto_renew (boolean, default false)
- status (enum: active, expired, cancelled — can be computed from expiry_date
  instead of stored, your call)
- notes (text, nullable)

### clients
- id
- name
- contact_email (nullable)
- notes (nullable)

## Filament Resources needed
- ProviderResource
- ProviderAccountResource (nested/relation manager under Provider, with
  password field masked by default, "reveal" toggle in the form)
- ServiceResource (main resource — this is the one I'll live in daily)
  - Table columns: name, type, provider account (via relationship), expiry_date,
    days_until_expiry (computed), status
  - Default sort: expiry_date ascending
  - Filters: type, provider, status, "expiring within 30 days"
  - Color-code expiry_date column: red if past due, orange if <30 days, green otherwise
- ClientResource

## Dashboard
- Filament widget: "Expiring in next 30 days" table widget on the main dashboard
- Widget: count cards (total services, expiring this month, expired/overdue)

## Security requirements
- `password` and `two_factor_notes` fields on `provider_accounts` MUST use
  Laravel's `encrypted` Eloquent cast — never store plaintext, never log these
  fields (add to a `$hidden` or scrub from logging/debugbar)
- Filament panel itself sits behind auth (default) — also enable Filament's
  built-in 2FA/simple password confirmation before revealing a stored password
  in the UI if that plugin is easy to wire in, otherwise skip for v1
- No public registration — only the artisan-created admin user should exist

## Built for future email notifications (don't build the emails yet, just leave the door open)
- Add an `ExpiryCheckCommand` (`php artisan services:check-expiry`) that queries
  services expiring in the next N days (make N configurable via .env, default 30)
  and currently just logs/dumps the list to console
- Register it in the scheduler (`routes/console.php` or `Kernel.php`) to run daily
- Structure it so swapping the `Log::info(...)` for
  `Notification::send($me, new ServiceExpiringNotification($service))` is the
  only change needed later — i.e., build the query/command now, wire mail later

## Seed data
Add a seeder with 2-3 sample providers, a couple of provider_accounts each,
and 5-10 sample services with a mix of past, near, and far expiry dates so the
dashboard/table has something to show immediately after install.

## Acceptance check
After scaffolding, run `php artisan serve`, log in via `/admin`, confirm:
1. I can create a Provider → Provider Account → Service in sequence
2. The Service table sorts by expiry date and color-codes correctly
3. `php artisan services:check-expiry` runs and lists services expiring soon
4. Stored passwords are encrypted at rest (check the raw DB column value)