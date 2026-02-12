<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Http\View\Composers\AdminViewComposer;

class ViewServiceProvider extends ServiceProvider
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
        View::composer('*', function ($view) {
            $layout = 'layouts.app';
            
            if (Auth::check() && Auth::user()->isAdmin()) {
                $layout = 'layouts.admin';
            }
            
            $view->with('currentLayout', $layout);
        });

        View::composer(['layouts.app', 'admin.partials.header', 'admin.plans.*'], AdminViewComposer::class);
    }
}
