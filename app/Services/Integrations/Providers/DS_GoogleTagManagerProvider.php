<?php

namespace App\Services\Integrations\Providers;

/**
 * Class DS_GoogleTagManagerProvider
 *
 * Handles Google Tag Manager integration settings.
 *
 * @package App\Services\Integrations\Providers
 */
class DS_GoogleTagManagerProvider extends AbstractIntegrationProvider
{
    /**
     * @return string
     */
    public function getProviderSlug(): string
    {
        return 'google_tag_manager';
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return [
            'name' => 'Google Tag Manager', 
            'icon' => 'fas fa-tags', 
            'color' => 'blue', 
            'category' => 'tracking', 
            'desc_key' => 'admin.tracking', 
            'sub_key' => 'admin.gtm_desc',
        ];
    }

    /**
     * @return array
     */
    public function getFormFields(): array
    {
        return [
            'gtm_id' => [
                'label' => 'GTM ID',
                'type' => 'text',
                'placeholder' => 'GTM-XXXXXXX',
                'rule' => 'required|string'
            ],
        ];
    }

    /**
     * @return null
     */
    
}
