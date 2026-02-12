<?php

namespace App\Services\Tracking;

/**
 * Class SnapchatPixelService
 *
 * Handles the rendering of Snapchat Pixel tracking code.
 *
 * @package App\Services\Tracking
 */
class SnapchatPixelService extends AbstractPixelService
{
    /**
     * @return string
     */
    protected function getServiceSlug(): string
    {
        return 'snapchat_pixel';
    }

    /**
     * Render the Snapchat Pixel code.
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
            <!-- Snapchat Pixel Code -->
            <script type='text/javascript'>
            (function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function()
            {a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};
            a.queue=[];var s='script';var r=t.createElement(s);r.async=!0;
            r.src=n;var u=t.getElementsByTagName(s)[0];
            u.parentNode.insertBefore(r,u)})(window,document,
            'https://sc-static.net/scevent.js');
            snaptr('init', '{$safeId}');
            snaptr('track', 'PAGE_VIEW');
            </script>
            <!-- End Snapchat Pixel Code -->
        ";
    }
}
