<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Traits\DS_TranslationHelper;
use Illuminate\Http\RedirectResponse;
use App\Traits\DS_RoleRedirect;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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
        $status = $this->authService->activate($token);

        if ($status === 'SUCCESS') {
            $user = User::where('activation_token', null) // Activated user has no token
                                     ->where('is_active', true)
                                     ->latest('updated_at')
                                     ->first();
            
            if ($user) Auth::login($user);
            
            $this->notifySuccess('auth.activation_success_login_now');
            return redirect()->to($this->getDashboardUrl($user));
        }

        if ($status === 'ALREADY_ACTIVE') {
            $this->notifyInfo('auth.account_already_active');
            return redirect()->route('login');
        }

        $this->notifyError('auth.activation_failed_invalid_token');
        return redirect()->route('login');
    }
}
