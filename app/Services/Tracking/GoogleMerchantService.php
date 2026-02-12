<?php

namespace App\Services\Tracking;

/**
 * Class GoogleMerchantService
 *
 * Handles the rendering of Google Merchant Center site verification meta tag.
 *
 * @package App\Services\Tracking
 */
class GoogleMerchantService extends AbstractPixelService
{
    /**
     * @return string
     */
    protected function getServiceSlug(): string
    {
        return 'google_merchant';
    }

    /**
     * Render the Google Merchant verification tag.
     *
     * @return string
     */
    public function render(): string
    {
        if (!$this->isActive()) {
            return '';
        }

        $merchantId = $this->settings['merchant_id'] ?? null;

        if (!$merchantId) {
            return '';
        }

        $safeId = e($merchantId);

        return "
            <!-- Google Merchant Center -->
            <meta name='google-site-verification' content='{$safeId}' />
            <!-- End Google Merchant Center -->
        ";
    }
}
