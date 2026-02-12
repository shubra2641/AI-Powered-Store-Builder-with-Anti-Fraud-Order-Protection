<?php

namespace App\Http\View\Composers;

use App\Services\LanguageService;
use App\Services\Payments\DS_PaymentGatewayService;
use App\Services\Tracking\PixelManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AdminViewComposer
{
    /**
     * Create a new profile composer.
     */
    public function __construct(
        protected LanguageService $languageService,
        protected DS_PaymentGatewayService $paymentService,
        protected PixelManager $pixelManager
    ) {}

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $availableLanguages = $this->languageService->getAllLanguages();
        $currentLang = $availableLanguages->where('code', app()->getLocale())->first() 
                    ?? $this->languageService->getDefaultLanguage();

        $view->with('availableLanguages', $availableLanguages);
        $view->with('currentLang', $currentLang);
        $view->with('allGateways', Cache::remember('ds_supported_gateways_list', 86400, function() {
            return $this->paymentService->getAvailableGateways();
        }));

        $view->with('availableFeatures', Cache::remember('ds_available_features_list', 86400, function() {
            $services = [];
            // Reflective access to services property if needed, but PixelManager should expose it
            // For now, we manually list or use a method if we add one to PixelManager
            return [
                'whatsapp', 'whatsapp_messages', 'fb_pixel', 'snap_pixel', 'twitter_pixel', 
                'tiktok_pixel', 'ga_pixel', 'google_merchant', 'google_tag_manager', 'fb_capi', 'recaptcha', 'seo', 'remove_branding'
            ];
        }));
    }
}
