<?php

namespace App\Services\Integrations\Providers;

class DS_FacebookPixelProvider extends AbstractIntegrationProvider
{
    public function getProviderSlug(): string
    {
        return 'facebook_pixel';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'Facebook Pixel', 
            'icon' => 'fab fa-facebook', 
            'color' => 'blue', 
            'category' => 'tracking', 
            'desc_key' => 'admin.tracking', 
            'sub_key' => 'admin.fb_pixel_desc',
        ];
    }

    public function getFormFields(): array
    {
        return [
            'pixel_id' => [
                'label' => 'Pixel ID',
                'type' => 'text',
                'rule' => 'required|string|regex:/^\d+$/'
            ],
        ];
    }

    
}
