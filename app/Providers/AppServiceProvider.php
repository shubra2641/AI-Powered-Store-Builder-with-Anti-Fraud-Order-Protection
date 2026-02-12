<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Services\SettingsService;
use App\Policies\DS_AdminPolicy;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Http\View\Composers\PixelViewComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, DS_AdminPolicy::class);
        
        Gate::define('admin-access', function (User $user) {
            return $user->isAdmin();
        });

        try {
            // Attempt to load settings without explicit Schema check for performance
            $settingsService = app(SettingsService::class);
            View::share('ds_settings', $settingsService);
            
            // This might throw if table doesn't exist (e.g. fresh install)
            $settingsService->setSmtpConfig();

            View::composer(['layouts.admin', 'layouts.app', 'layouts.guest'], PixelViewComposer::class);
        } catch (\Throwable $e) {
            // Silently fail if settings table missing or other boot errors
            // This ensures the app doesn't crash during migrations or fresh install
        }
    }
}
