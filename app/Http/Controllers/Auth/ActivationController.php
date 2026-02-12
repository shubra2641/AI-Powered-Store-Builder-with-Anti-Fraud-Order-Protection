<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Traits\DS_TranslationHelper;
use Illuminate\Http\RedirectResponse;
use App\Traits\DS_RoleRedirect;
use Illuminate\Support\Facades\Auth;

/**
 * Class ActivationController
 * Handles account activation via email tokens.
 */
class ActivationController extends Controller
{
    use DS_TranslationHelper, DS_RoleRedirect;

    /**
     * Create a new ActivationController instance.
     */
    public function __construct(
        protected AuthService $authService
    ) {}

    public function activate(string $token): RedirectResponse
    {
        $user = $this->authService->activate($token);

        if ($user) {
            Auth::login($user);
            
            $this->notifySuccess('auth.activation_success_login_now');
            
            return redirect()->to($this->getDashboardUrl($user));
        }

        $this->notifyError('auth.activation_failed');
        return redirect()->route('login');
    }
}
