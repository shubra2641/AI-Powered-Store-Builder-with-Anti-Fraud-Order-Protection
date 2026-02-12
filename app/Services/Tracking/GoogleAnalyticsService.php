<?php

namespace App\Services\Tracking;

/**
 * Class GoogleAnalyticsService
 *
 * Handles the rendering of Google Analytics 4 (GA4) tracking code.
 *
 * @package App\Services\Tracking
 */
class GoogleAnalyticsService extends AbstractPixelService
{
    /**
     * @return string
     */
    protected function getServiceSlug(): string
    {
        return 'google_analytics';
    }

    /**
     * Render the GA4 code.
     *
     * @return string
     */
    public function render(): string
    {
        if (!$this->isActive()) {
            return '';
        }

        $measurementId = $this->settings['measurement_id'] ?? null;

        if (!$measurementId) {
            return '';
        }

        $safeId = e($measurementId);

        return "
            <!-- Google Analytics (gtag.js) -->
            <script async src='https://www.googletagmanager.com/gtag/js?id={$safeId}'></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
              gtag('config', '{$safeId}');
            </script>
            <!-- End Google Analytics -->
        ";
    }
}
