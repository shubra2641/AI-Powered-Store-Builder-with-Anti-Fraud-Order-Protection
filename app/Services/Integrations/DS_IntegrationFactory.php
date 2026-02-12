<?php

namespace App\Services\Integrations;

use App\Contracts\DS_IntegrationProviderInterface;
use App\Models\DS_Integration;
use App\Services\Integrations\Providers\DS_WhatsappProvider;
use App\Services\Integrations\Providers\DS_SendgridProvider;
use App\Services\Integrations\Providers\DS_GoogleAnalyticsProvider;
use App\Services\Integrations\Providers\DS_FacebookPixelProvider;
use App\Services\Integrations\Providers\DS_TiktokPixelProvider;
use App\Services\Integrations\Providers\DS_SnapchatPixelProvider;
use App\Services\Integrations\Providers\DS_TwitterPixelProvider;
use App\Services\Integrations\Providers\DS_LinkedinInsightProvider;
use App\Services\Integrations\Providers\DS_GoogleMerchantProvider;
use App\Services\Integrations\Providers\DS_GoogleRecaptchaProvider;
use App\Services\Integrations\Providers\DS_GoogleTagManagerProvider;
use App\Services\Integrations\Providers\DS_FacebookCapiProvider;
use InvalidArgumentException;

class DS_IntegrationFactory
{
    /**
     * Get all supported integration service slugs.
     *
     * @return array
     */
    public static function getSupportedServices(): array
    {
        return [
            'whatsapp',
            'sendgrid',
            'google_analytics',
            'facebook_pixel',
            'tiktok_pixel',
            'snapchat_pixel',
            'twitter_pixel',
            'linkedin_insight',
            'google_merchant',
            'google_recaptcha',
            'google_tag_manager',
            'facebook_capi',
        ];
    }

    /**
     * Create an integration provider instance.
     *
     * @param string $service The service slug (e.g. 'whatsapp')
     * @param int|null $userId Optional user ID to fetch settings for. If null, uses auth user.
     * @return DS_IntegrationProviderInterface
     */
    public static function make(string $service, ?int $userId = null): DS_IntegrationProviderInterface
    {
        // No implicit Auth::id() fallback to ensure testability per Envato standards
        
        $integration = null;
        if ($userId) {
            $integration = DS_Integration::where('user_id', $userId)
                ->where('service', $service)
                ->first();
        }

        $settings = $integration ? ($integration->settings ?? []) : [];

        return match ($service) {
            'whatsapp'          => new DS_WhatsappProvider($settings),
            'sendgrid'          => new DS_SendgridProvider($settings),
            'google_analytics'  => new DS_GoogleAnalyticsProvider($settings),
            'facebook_pixel'    => new DS_FacebookPixelProvider($settings),
            'tiktok_pixel'      => new DS_TiktokPixelProvider($settings),
            'snapchat_pixel'    => new DS_SnapchatPixelProvider($settings),
            'twitter_pixel'     => new DS_TwitterPixelProvider($settings),
            'linkedin_insight'  => new DS_LinkedinInsightProvider($settings),
            'google_merchant'   => new DS_GoogleMerchantProvider($settings),
            'google_recaptcha'  => new DS_GoogleRecaptchaProvider($settings),
            'google_tag_manager' => new DS_GoogleTagManagerProvider($settings),
            'facebook_capi'     => new DS_FacebookCapiProvider($settings),
            default             => throw new InvalidArgumentException("Unsupported integration service: {$service}"),
        };
    }
}
