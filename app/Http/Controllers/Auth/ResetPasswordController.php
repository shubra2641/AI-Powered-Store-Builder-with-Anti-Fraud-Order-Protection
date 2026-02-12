<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Services\EmailService;
use App\Traits\DS_TranslationHelper;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Facades\Password;
/**
 * Class ResetPasswordController
 * Handles resetting user passwords using tokens.
 */
class ResetPasswordController extends Controller
{
    use DS_TranslationHelper;

    /**
     * Create a new ResetPasswordController instance.
     */
    public function __construct(
        protected EmailService $emailService,
        protected AuthService $authService
    ) {}

    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, ?string $token = null): View
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset the given user's password.
     * 
     * @param ResetPasswordRequest $request
     * @return RedirectResponse
     */
    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $status = $this->authService->resetPassword(
            $request->email,
            $request->password,
            $request->token
        );

        if ($status === Password::PASSWORD_RESET) {
            $this->notifySuccess('auth.password_reset_success');
            return redirect()->route('login');
        }

        $this->notifyError($status === Password::INVALID_USER 
            ? 'auth.invalid_email' 
            : 'auth.invalid_password_token');

        return back();
    }
}
