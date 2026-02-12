<?php

use App\Services\SettingsService;
use App\Services\Security\DS_CaptchaService;

if (!function_exists('ds_currency')) {
    /**
     * Format amount with the site's default currency.
     * 
     * @param mixed $amount
     * @return string
     */
    function ds_currency($amount, ?int $decimals = null)
    {
        static $currency = null;
        
        if ($currency === null) {
            $settings = app(SettingsService::class);
            $currency = $settings->get('site_currency', 'USD', null);
        }

        if ($decimals === null) {
            $decimalMap = [
                'KWD' => 3,
                'OMR' => 3,
                'BHD' => 3,
                'JPY' => 0,
                'KRW' => 0,
                'CLP' => 0,
                'PYG' => 0,
            ];
            $decimals = $decimalMap[$currency] ?? 2;
        }
        
        $formatted = number_format($amount, $decimals);
        
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'SAR' => 'SAR',
            'EGP' => 'EGP',
            'AED' => 'AED',
            'KWD' => 'KWD',
            'QAR' => 'QAR',
            'INR' => '₹',
        ];

        $symbol = $symbols[$currency] ?? $currency;

        if (in_array($currency, ['SAR', 'EGP', 'AED', 'KWD', 'QAR'])) {
            return $formatted . ' ' . $symbol;
        }
        
        return $symbol . $formatted;
    }
}

if (!function_exists('captcha_active')) {
    function captcha_active(?int $userId = null): bool
    {
        return app(DS_CaptchaService::class)->isActive($userId);
    }
}

if (!function_exists('captcha_site_key')) {
    function captcha_site_key(?int $userId = null): ?string
    {
        return app(DS_CaptchaService::class)->getSiteKey($userId);
    }
}

if (!function_exists('captcha_render_script')) {
    function captcha_render_script(?int $userId = null): string
    {
        // Handled globally via PixelManager in platform layouts
        return '';
    }
}

    function captcha_render_widget(?int $userId = null): string
    {
        $captcha = app(DS_CaptchaService::class);
        $isActive = $captcha->isActive($userId);
        
        if (!$isActive) return '';
        
        $version = $captcha->getVersion($userId);
        $siteKey = $captcha->getSiteKey($userId);

        return view('partials.captcha', compact('isActive', 'version', 'siteKey'))->render();
    }
