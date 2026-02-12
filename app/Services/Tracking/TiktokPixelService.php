<?php

namespace App\Services\Tracking;

/**
 * Class TiktokPixelService
 *
 * Handles the rendering of TikTok Pixel tracking code.
 *
 * @package App\Services\Tracking
 */
class TiktokPixelService extends AbstractPixelService
{
    /**
     * @return string
     */
    protected function getServiceSlug(): string
    {
        return 'tiktok_pixel';
    }

    /**
     * Render the TikTok Pixel code.
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
            <!-- TikTok Pixel Code -->
            <script>
            !function (w, d, t) {
              w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=['page','track','identify','instances','debug','on','off','once','ready','alias','group','trackSelf','unidentify'],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i='https://analytics.tiktok.com/i18n/pixel/events.js';ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n;var o=d.createElement('script');o.type='text/javascript',o.async=!0,o.src=i+'?sdkid='+e+'&lib='+t;var a=d.getElementsByTagName('script')[0];a.parentNode.insertBefore(o,a)};
              ttq.load('{$safeId}');
              ttq.page();
            }(window, document, 'ttq');
            </script>
            <!-- End TikTok Pixel Code -->
        ";
    }
}
