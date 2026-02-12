<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Services\EmailService;
use App\Traits\DS_TranslationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Class ForgotPasswordController
 * Manages password reset link requests.
 */
class ForgotPasswordController extends Controller
{
    use DS_TranslationHelper;

    /**
     * Create a new ForgotPasswordController instance.
     */
    public function __construct(
        protected EmailService $emailService,
        protected \App\Services\AuthService $authService
    ) {}

    /**
     * Show the forgot password link request form.
     */
    public function showLinkRequestForm(): View
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     * 
     * @param ForgotPasswordRequest $request
     * @return RedirectResponse
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request): RedirectResponse
    {
        $token = $this->authService->createPasswordResetToken($request->email);

        if ($token) {
            $user = User::where('email', $request->email)->first();
            $this->emailService->sendTemplateEmail($user, 'password_reset_email', [
                'reset_url' => route('password.reset', ['token' => $token, 'email' => $user->email]),
            ]);
        }

        $this->notifySuccess('auth.password_reset_link_sent');
        return back();
    }
}
