<?php

namespace App\Services\Tracking;

/**
 * Class FacebookPixelService
 *
 * Handles the rendering of Facebook Pixel tracking code.
 *
 * @package App\Services\Tracking
 */
class FacebookPixelService extends AbstractPixelService
{
    /**
     * @return string
     */
    protected function getServiceSlug(): string
    {
        return 'facebook_pixel';
    }

    /**
     * Render the Facebook Pixel code.
     *
     * @return string
     */
    public function render(): string
    {
        if (!$this->isActive()) {
            return '';
        }

        $pixelId = $this->settings['pixel_id'] ?? null;

        if (!$pixelId) {
            return '';
        }

        $safeId = e($pixelId);

        return "
            <!-- Facebook Pixel Code -->
            <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{$safeId}');
            fbq('track', 'PageView');
            </script>
            <noscript><img height='1' width='1' style='display:none'
            src='https://www.facebook.com/tr?id={$safeId}&ev=PageView&noscript=1'
            /></noscript>
            <!-- End Facebook Pixel Code -->
        ";
    }
}
