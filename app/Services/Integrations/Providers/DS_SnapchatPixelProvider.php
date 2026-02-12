<?php

namespace App\Services\Integrations\Providers;

class DS_SnapchatPixelProvider extends AbstractIntegrationProvider
{
    public function getProviderSlug(): string
    {
        return 'snapchat_pixel';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'Snapchat Pixel', 
            'icon' => 'fab fa-snapchat', 
            'color' => 'yellow', 
            'category' => 'tracking', 
            'desc_key' => 'admin.tracking', 
            'sub_key' => 'admin.snap_pixel_desc',
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
