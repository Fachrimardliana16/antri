<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Dynamic Theming View Composer for all Blade views
        View::composer('*', function ($view) {
            try {
                $settings = AppSetting::getAll();
            } catch (\Throwable $e) {
                $settings = [
                    'app_name' => 'Sistem Antrian Terpadu',
                    'primary_color' => '#2563eb',
                    'secondary_color' => '#06b6d4',
                    'logo_url' => '',
                    'marquee_text' => 'Selamat datang di Sistem Antrian Terpadu.',
                    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&mute=1&loop=1',
                    'voice_rate' => '0.9',
                    'voice_pitch' => '1.0',
                    'voice_lang' => 'id-ID',
                ];
            }
            $view->with('settings', $settings);
        });

        // 2. Authorization Gates
        Gate::define('is-super-admin', function (User $user) {
            return $user->isSuperAdmin();
        });

        Gate::define('is-admin', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('is-operator', function (User $user) {
            return $user->isOperator() || $user->isAdmin();
        });
    }
}
