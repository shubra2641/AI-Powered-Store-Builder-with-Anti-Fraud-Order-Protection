<?php

namespace App\Services\Tracking;

/**
 * Class TwitterPixelService
 *
 * Handles the rendering of Twitter (X) Pixel tracking code.
 *
 * @package App\Services\Tracking
 */
class TwitterPixelService extends AbstractPixelService
{
    /**
     * @return string
     */
    protected function getServiceSlug(): string
    {
        return 'twitter_pixel';
    }

    /**
     * Render the Twitter Pixel code.
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
            <!-- Twitter universal website tag code -->
            <script>
            !function(e,t,n,s,u,a){e.twq||(s=e.twq=function(){s.exe?s.exe.apply(s,arguments):s.queue.push(arguments);
            },s.version='1.1',s.queue=[],u=t.createElement(n),u.async=!0,u.src='//static.ads-twitter.com/uwt.js',
            a=t.getElementsByTagName(n)[0],a.parentNode.insertBefore(u,a))}(window,document,'script');
            // Insert Twitter Pixel ID and Standard Event Free View Content
            twq('init','{$safeId}');
            twq('track','PageView');
            </script>
            <!-- End Twitter universal website tag code -->
        ";
    }
}
