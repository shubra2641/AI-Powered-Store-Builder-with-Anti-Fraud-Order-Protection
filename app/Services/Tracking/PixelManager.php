<?php

namespace App\Services\Tracking;

use App\Models\DS_Integration;
/**
 * Class PixelManager
 *
 * Orchestrates all active pixel services and renders them together.
 *
 * @package App\Services\Tracking
 */
class PixelManager
{
    /**
     * @var array
     */
    /**
     * @var array
     */
    protected static array $serviceMap = [
        'facebook_pixel' => FacebookPixelService::class,
        'google_analytics' => GoogleAnalyticsService::class,
        'snapchat_pixel' => SnapchatPixelService::class,
        'twitter_pixel' => TwitterPixelService::class,
        'tiktok_pixel' => TiktokPixelService::class,
        'linkedin_insight' => LinkedinInsightService::class,
        'google_merchant' => GoogleMerchantService::class,
        'google_tag_manager' => GoogleTagManagerService::class,
        'facebook_capi' => FacebookCapiService::class,
        'google_recaptcha' => GoogleRecaptchaService::class,
        'custom_script' => CustomScriptService::class, // Added for new requirement
    ];

    /**
     * Render all active pixels for a specific user.
     *
     * @param int|null $userId
     * @return string
     */
    public function render(?int $userId = null): string
    {
        if (!$userId) {
            return '';
        }

        // Fetch all active integrations for this user
        $integrations = DS_Integration::where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        $html = '';

        foreach ($integrations as $integration) {
            $serviceClass = self::$serviceMap[$integration->service] ?? null;

            if ($serviceClass && class_exists($serviceClass)) {
                /** @var AbstractPixelService $serviceInstance */
                $serviceInstance = new $serviceClass($integration->settings ?? []);
                $html .= $serviceInstance->render() . "\n";
            }
        }

        return $html;
    }
}
