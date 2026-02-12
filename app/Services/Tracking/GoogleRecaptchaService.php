<?php

namespace App\Services\Tracking;

/**
 * Class GoogleRecaptchaService
 *
 * Handles the rendering of Google reCAPTCHA script globally.
 * This ensures the script is loaded once in the head via PixelManager.
 *
 * @package App\Services\Tracking
 */
class GoogleRecaptchaService extends AbstractPixelService
{
    /**
     * @return string
     */
    protected function getServiceSlug(): string
    {
        return 'google_recaptcha';
    }

    /**
     * Render the reCAPTCHA script.
     *
     * @return string
     */
    public function render(): string
    {
        if (!$this->isActive()) {
            return '';
        }

        $siteKey = $this->settings['site_key'] ?? null;
        $version = $this->settings['version'] ?? 'v2_checkbox';

        if (!$siteKey) {
            return '';
        }

        $safeKey = e($siteKey);

        if ($version === 'v3') {
            return "
                <!-- Google reCAPTCHA v3 -->
                <script src='https://www.google.com/recaptcha/api.js?render={$safeKey}'></script>
            ";
        }

        return "
            <!-- Google reCAPTCHA v2 -->
            <script src='https://www.google.com/recaptcha/api.js' async defer></script>
        ";
    }
}
