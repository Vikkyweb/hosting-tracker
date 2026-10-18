<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProviderAccountController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/admin/logout', [LoginController::class, 'destroy'])->name('logout');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('services', ServiceController::class)->except('show');
    Route::resource('clients', ClientController::class)->except('show');
    Route::resource('providers', ProviderController::class)->except('show');
    Route::resource('provider-accounts', ProviderAccountController::class)->except('show')->parameters([
        'provider-accounts' => 'providerAccount',
    ]);
});
