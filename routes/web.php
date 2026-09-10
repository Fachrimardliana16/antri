<?php

use App\Livewire\Admin\Analytics;
use App\Livewire\Admin\Announcements;
use App\Livewire\Admin\Counters;
use App\Livewire\Admin\Services;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\Users;
use App\Livewire\Auth\Login;
use App\Livewire\Display\TvMonitor;
use App\Livewire\Kiosk\TakeTicket;
use App\Livewire\Operator\Dashboard;
use App\Livewire\Tracking\LiveTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Root Redirection
Route::get('/', function () {
    return redirect()->route('kiosk.take-ticket');
});

// 2. Public Displays & Tracking
Route::get('/kiosk', TakeTicket::class)->name('kiosk.take-ticket');
Route::get('/tv', TvMonitor::class)->name('display.tv');
Route::get('/tracking/{token}', LiveTicket::class)->name('tracking.ticket');

// 3. Guest Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

// 4. Authenticated Operator Area
Route::middleware(['auth', 'role:operator'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
});

// 5. Authenticated Admin Area
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.analytics');
    });
    Route::get('/analytics', Analytics::class)->name('analytics');
    Route::get('/services', Services::class)->name('services');
    Route::get('/counters', Counters::class)->name('counters');
    Route::get('/users', Users::class)->name('users');
    Route::get('/announcements', Announcements::class)->name('announcements');
});

// 6. Authenticated Super Admin Area (Theme Engine & Root Config)
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/settings', Settings::class)->name('settings');
});
