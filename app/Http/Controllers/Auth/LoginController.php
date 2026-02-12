<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use App\Traits\DS_TranslationHelper;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\DS_RoleRedirect;

/**
 * Class LoginController
 * Handles authenticating users and role-based redirection.
 */
class LoginController extends Controller
{
    use DS_TranslationHelper, DS_RoleRedirect;

    /**
     * Create a new LoginController instance.
     */
    public function __construct(
        protected AuthService $authService
    ) {}

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $status = $this->authService->login($request->validated(), $request->filled('remember'));

        if ($status === 'SUCCESS') {
            $request->session()->regenerate();
            return $this->authenticated();
        }

        if ($status === 'INACTIVE') {
            $this->notifyError('auth.account_inactive_check_email');
            return back()->withInput();
        }

        $this->notifyError('auth.failed');
        return back()->withErrors(['email' => __('auth.failed')])->withInput();
    }

    protected function authenticated(): RedirectResponse
    {
        return redirect()->intended($this->getDashboardUrl());
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
