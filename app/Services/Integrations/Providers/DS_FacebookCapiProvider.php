<?php

namespace App\Services\Integrations\Providers;

/**
 * Class DS_FacebookCapiProvider
 *
 * Handles Facebook Conversion API (CAPI) integration settings.
 *
 * @package App\Services\Integrations\Providers
 */
class DS_FacebookCapiProvider extends AbstractIntegrationProvider
{
    /**
     * @return string
     */
    public function getProviderSlug(): string
    {
        return 'facebook_capi';
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return [
            'name' => 'Facebook CAPI', 
            'icon' => 'fab fa-facebook-f', 
            'color' => 'blue', 
            'category' => 'tracking', 
            'desc_key' => 'admin.fb_capi', 
            'sub_key' => 'admin.fb_capi_desc',
        ];
    }

    /**
     * @return array
     */
    public function getFormFields(): array
    {
        return [
            'pixel_id' => [
                'label' => 'Pixel ID',
                'type' => 'text',
                'rule' => 'required|string'
            ],
            'access_token' => [
                'label' => 'Access Token',
                'type' => 'text',
                'rule' => 'required|string'
            ],
            'test_event_code' => [
                'label' => 'Test Event Code (Optional)',
                'type' => 'text',
                'rule' => 'nullable|string'
            ],
        ];
    }

    /**
     * @return null
     */
    
}
