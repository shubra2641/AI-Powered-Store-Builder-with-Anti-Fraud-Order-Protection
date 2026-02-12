<?php

namespace App\Services\Tracking;

/**
 * Class GoogleTagManagerService
 *
 * Handles the rendering of Google Tag Manager (GTM) tracking code.
 *
 * @package App\Services\Tracking
 */
class GoogleTagManagerService extends AbstractPixelService
{
    /**
     * @return string
     */
    protected function getServiceSlug(): string
    {
        return 'google_tag_manager';
    }

    /**
     * Render the Google Tag Manager code.
     * Includes both Head script and Noscript part.
     *
     * @return string
     */
    public function render(): string
    {
        if (!$this->isActive()) {
            return '';
        }

        $gtmId = $this->settings['gtm_id'] ?? null;

        if (!$gtmId) {
            return '';
        }

        $safeId = e($gtmId);

        return "
            <!-- Google Tag Manager -->
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','{$safeId}');</script>
            <!-- End Google Tag Manager -->

            <!-- Google Tag Manager (noscript) -->
            <noscript><iframe src='https://www.googletagmanager.com/ns.html?id={$safeId}'
            height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->
        ";
    }
}
