<?php

namespace App\Http\Middleware;

use App\Traits\DS_RoleRedirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RedirectByRole
 * Redirects authenticated users to their role-specific dashboard.
 * Adheres to SRP (Single Responsibility Principle) and Envato standards.
 */
class RedirectByRole
{
    use DS_RoleRedirect;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  ...$guards
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect()->to($this->getDashboardUrl(Auth::guard($guard)->user()));
            }
        }

        return $next($request);
    }
}
