<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Traits\DS_TranslationHelper;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Services\Tracking\PixelManager;
use App\Models\User;

/**
 * Class RegisterController
 * Handles the registration of new users.
 */
class RegisterController extends Controller
{
    use DS_TranslationHelper;

    /**
     * Create a new RegisterController instance.
     */
    public function __construct(
        protected AuthService $authService,
        protected PixelManager $pixelManager
    ) {}

    public function showRegistrationForm(): View
    {
        // For guest pages, we usually load pixels configured by the main admin
        $adminId = User::whereHas('role', function ($query) {
            $query->where('slug', 'admin');
        })->first()?->id ?? 1;

        $trackingPixels = $this->pixelManager->render($adminId);

        return view('auth.register', compact('trackingPixels'));
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        try {
            $this->authService->register($request->validated());

            $this->notifySuccess('auth.registration_success_check_email');

            return redirect()->route('login');
        } catch (\Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage());
            
            $this->notifyError($e->getMessage() ?: 'auth.registration_failed');
            
            return redirect()->back()->withInput();
        }
    }
}
