<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\EmailService;
use App\Traits\DS_TranslationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Traits\DS_RoleRedirect;
use App\Rules\DS_RecaptchaRule;
use App\Services\Security\DS_CaptchaService;
use Illuminate\Support\Facades\Validator;

/**
 * Class VerificationController
 * Handles email verification notice and resend logic.
 */
class VerificationController extends Controller
{
    use DS_TranslationHelper, DS_RoleRedirect;

    public function __construct(
        protected AuthService $authService,
        protected EmailService $emailService,
        protected DS_CaptchaService $captchaService
    ) {}

    /**
     * Show the email verification notice.
     */
    public function show(Request $request): View|RedirectResponse
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->to($this->getDashboardUrl($request->user()))
            : view('auth.verify');
    }

    /**
     * Resend the email verification/activation link.
     */
    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('admin.dashboard');
        }

        if ($this->captchaService->isActive()) {
            $validator = Validator::make($request->all(), [
                'g-recaptcha-response' => ['required', new DS_RecaptchaRule],
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }

        $this->authService->resendVerification($request->user());

        $this->notifySuccess('auth.verification_link_sent');

        return back()->with('resent', true);
    }
    
    /**
     * Standard Laravel verification route (optional, since we use custom activation)
     */
    public function verify(Request $request): RedirectResponse
    {
        return redirect()->route('admin.dashboard');
    }
}
