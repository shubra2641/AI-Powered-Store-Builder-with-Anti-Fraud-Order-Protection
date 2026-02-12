<?php

namespace App\Services\Tracking;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class FacebookCapiService
 *
 * Handles Facebook Conversion API (CAPI) events.
 *
 * @package App\Services\Tracking
 */
class FacebookCapiService extends AbstractPixelService
{
    /**
     * @return string
     */
    protected function getServiceSlug(): string
    {
        return 'facebook_capi';
    }

    /**
     * CAPI is server-side, it usually doesn't render anything in the browser.
     * However, it can return an empty string or a small comment.
     *
     * @return string
     */
    public function render(): string
    {
        if (!$this->isActive()) {
            return '';
        }

        return "<!-- Facebook CAPI Active -->";
    }

    /**
     * Send a server-side event to Facebook.
     *
     * @param string $eventName
     * @param array $userData
     * @param array $customData
     * @return bool
     */
    public function sendEvent(string $eventName, array $userData = [], array $customData = []): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $pixelId = $this->settings['pixel_id'] ?? null;
        $accessToken = $this->settings['access_token'] ?? null;
        $testCode = $this->settings['test_event_code'] ?? null;

        if (!$pixelId || !$accessToken) {
            Log::warning('Facebook CAPI: Missing Pixel ID or Access Token.');
            return false;
        }

        try {
            $payload = [
                'data' => [
                    [
                        'event_name' => $eventName,
                        'event_time' => time(),
                        'action_source' => 'website',
                        'user_data' => array_merge([
                            'client_ip_address' => request()->ip(),
                            'client_user_agent' => request()->userAgent(),
                        ], $userData),
                        'custom_data' => $customData,
                    ]
                ],
            ];

            if ($testCode) {
                $payload['test_event_code'] = $testCode;
            }

            $response = Http::post("https://graph.facebook.com/v17.0/{$pixelId}/events?access_token={$accessToken}", $payload);

            if (!$response->successful()) {
                Log::error('Facebook CAPI Error: ' . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Facebook CAPI Exception: ' . $e->getMessage());
            return false;
        }
    }
}
