<?php

namespace App\Services\Integrations\Providers;

class DS_TwitterPixelProvider extends AbstractIntegrationProvider
{
    public function getProviderSlug(): string
    {
        return 'twitter_pixel';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'Twitter Pixel', 
            'icon' => 'fab fa-twitter', 
            'color' => 'blue', 
            'category' => 'tracking', 
            'desc_key' => 'admin.tracking', 
            'sub_key' => 'admin.twitter_pixel_desc',
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
        ];
    }

    
}
