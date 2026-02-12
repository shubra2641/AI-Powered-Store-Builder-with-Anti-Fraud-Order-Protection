<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Models\Plan;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SystemNotification;
use App\Events\UserRegistered;
use App\Events\UserActivated;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\Password;

/**
 * Class AuthService
 * Handles the core authentication logic for the DropSaaS project.
 */
class AuthService
{
    /**
     * Create a new AuthService instance.
     */
    public function __construct(
        protected EmailService $emailService,
        protected LanguageService $languageService,
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Handle user registration and dispatch verification email.
     *
     * @param array<string, mixed> $data User registration data.
     * @return User
     */
    public function register(array $data): User
    {
        $user = DB::transaction(function () use ($data): User {
            $userRole = Role::where('slug', 'user')->first();
            $defaultLang = $this->languageService->getDefaultLanguage();

            return User::create([
                'name'             => $data['name'],
                'email'            => $data['email'],
                'password'         => Hash::make($data['password']),
                'role_id'          => $userRole?->id,
                'language_id'      => $defaultLang?->id,
                'is_active'        => false,
                'activation_token' => Str::random(60),
            ]);
        });

        $defaultPlan = Plan::where('is_default', true)->where('is_active', true)->first();
        if ($defaultPlan) {
            $this->subscriptionService->initiatePurchase($user, $defaultPlan, 'free');
        }

        UserRegistered::dispatch($user);

        return $user;
    }

    /**
     * Activate a user account by token.
     *
     * @param string $token Activation token.
     * @return string Status code of activation.
     */
    public function activate(string $token): string
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return 'INVALID_TOKEN';
        }

        if ($user->is_active) {
            return 'ALREADY_ACTIVE';
        }

        DB::transaction(function () use ($user) {
            $user->update([
                'is_active'        => true,
                'activation_token' => null,
                'email_verified_at' => now(),
            ]);
        });

        UserActivated::dispatch($user);

        return 'SUCCESS';
    }

    /**
     * Attempt login.
     */
    /**
     * Attempt login and return detailed status.
     * 
     * @param array $credentials
     * @param bool $remember
     * @return string
     */
    public function login(array $credentials, bool $remember = false): string
    {
        $user = User::where('email', $credentials['email'] ?? '')->first();

        if (!$user || !Hash::check($credentials['password'] ?? '', $user->password)) {
            return 'INVALID_CREDENTIALS';
        }

        if (!$user->is_active) {
            return 'INACTIVE';
        }

        Auth::login($user, $remember);

        return 'SUCCESS';
    }

    /**
     * Reset the user's password.
     * 
     * @param string $email
     * @param string $password
     * @param string $token
     * @return string Status of the reset attempt.
     */
    public function resetPassword(string $email, string $password, string $token): string
    {
        $reset = PasswordResetToken::where('email', $email)->first();

        if (!$reset || !Hash::check($token, $reset->token)) {
            return Password::INVALID_TOKEN;
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update(['password' => Hash::make($password)]);
            $reset->delete();
            
            $this->emailService->sendTemplateEmail($user, 'password_changed_notification');
            return Password::PASSWORD_RESET;
        }

        return Password::INVALID_USER;
    }

    /**
     * Create or update a password reset token.
     *
     * @param string $email
     * @return string|null The clear-text token or null if user not found.
     */
    public function createPasswordResetToken(string $email): ?string
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return null;
        }

        $token = Str::random(60);
        
        PasswordResetToken::updateOrCreate(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        return $token;
    }

    /**
     * Resend the verification email to the user.
     * 
     * @param User $user
     * @return void
     */
    public function resendVerification(User $user): void
    {
        if (!$user->activation_token) {
            $user->regenerateActivationToken();
        }

        $this->emailService->sendTemplateEmail($user, 'activation_email', [
            'activation_url' => route('activate', ['token' => $user->activation_token]),
        ]);
    }
}
