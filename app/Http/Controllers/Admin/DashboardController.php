<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $days = (int) config('services.expiry_check_days', 30);

        $expiringSoon = Service::query()
            ->with(['providerAccount.provider', 'client'])
            ->whereNull('cancelled_at')
            ->whereBetween('expiry_date', [today(), today()->addDays($days)])
            ->orderBy('expiry_date')
            ->paginate(10)
            ->withQueryString();

        return view('admin.dashboard', [
            'totalServices' => Service::query()->count(),
            'expiringSoonCount' => Service::query()
                ->whereNull('cancelled_at')
                ->whereBetween('expiry_date', [today(), today()->addDays($days)])
                ->count(),
            'expiredCount' => Service::query()
                ->whereNull('cancelled_at')
                ->whereDate('expiry_date', '<', today())
                ->count(),
            'expiringSoon' => $expiringSoon,
            'days' => $days,
        ]);
    }
}
