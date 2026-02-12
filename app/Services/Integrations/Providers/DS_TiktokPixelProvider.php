<?php

namespace App\Services\Integrations\Providers;

class DS_TiktokPixelProvider extends AbstractIntegrationProvider
{
    public function getProviderSlug(): string
    {
        return 'tiktok_pixel';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'TikTok Pixel', 
            'icon' => 'fab fa-tiktok', 
            'color' => 'black', 
            'category' => 'tracking', 
            'desc_key' => 'admin.tracking', 
            'sub_key' => 'admin.tiktok_pixel_desc',
        ];
    }

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
                'rule' => 'nullable|string'
            ],
        ];
    }

    
}
