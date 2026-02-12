<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RedirectByRole;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth', 'can:admin-access'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware(['web', 'auth', 'verified'])
                ->name('user.')
                ->group(base_path('routes/user.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'guest' => RedirectByRole::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/payments/webhook/*',
        ]);
    })
    ->withSchedule(function ($schedule) {
        $schedule->command('subscription:check-expirations')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
