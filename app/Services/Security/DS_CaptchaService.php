<?php

namespace App\Services\Security;

use App\Models\DS_Integration;
use Illuminate\Support\Facades\Http;

use App\Models\User;

/**
 * Class DS_CaptchaService
 * 
 * Manages Google reCAPTCHA integration and verification logic.
 */
class DS_CaptchaService
{
    protected ?array $settings = null;
    protected bool $isActive = false;

    public function __construct()
    {
        $this->loadSettings();
    }

    /**
     * Load settings once.
     */
    protected function loadSettings(): void
    {
        $adminId = $this->getAdminId();
        
        $integration = DS_Integration::where('user_id', $adminId)
            ->where('service', 'google_recaptcha')
            ->first();

        if ($integration) {
            $this->settings = $integration->settings ?? [];
            $this->isActive = $integration->is_active;
        }
    }

    /**
     * Get the primary administrator ID.
     * 
     * @return int
     */
    protected function getAdminId(): int
    {
        return User::whereHas('role', function ($query) {
            $query->where('slug', 'admin');
        })->first()?->id ?? 1; // Fallback to 1 if no admin found
    }

    /**
     * Check if Google reCAPTCHA is active.
     *
     * @param int|null $userId
     * @return bool
     */
    public function isActive(?int $userId = null): bool
    {
        // Ignore userId param as captcha is global/admin controlled usually
        return $this->isActive;
    }

    /**
     * Get reCAPTCHA version.
     *
     * @param int|null $userId
     * @return string
     */
    public function getVersion(?int $userId = null): string
    {
        return $this->settings['version'] ?? 'v2_checkbox';
    }

    /**
     * Get the Site Key for reCAPTCHA.
     *
     * @param int|null $userId
     * @return string|null
     */
    public function getSiteKey(?int $userId = null): ?string
    {
        return $this->settings['site_key'] ?? null;
    }

    /**
     * Get the Secret Key for reCAPTCHA.
     *
     * @param int|null $userId
     * @return string|null
     */
    protected function getSecretKey(?int $userId = null): ?string
    {
        return $this->settings['secret_key'] ?? null;
    }

    /**
     * Verify the reCAPTCHA response.
     *
     * @param string|null $response
     * @param int|null $userId
     * @return bool
     */
    public function verify(?string $response, ?int $userId = null): bool
    {
        if (!$response) {
            return false;
        }

        $secret = $this->getSecretKey($userId);

        if (!$secret) {
            return true;
        }

        try {
            $verifyResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => $secret,
                'response' => $response,
                'remoteip' => request()->ip(),
            ]);

            return $verifyResponse->json()['success'] ?? false;
        } catch (\Exception $e) {
            \Log::error('reCAPTCHA Verification Failed: ' . $e->getMessage());
            return true;
        }
    }
}
