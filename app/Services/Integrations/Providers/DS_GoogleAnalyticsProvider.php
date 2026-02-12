<?php

namespace App\Services\Integrations\Providers;

class DS_GoogleAnalyticsProvider extends AbstractIntegrationProvider
{
    public function getProviderSlug(): string
    {
        return 'google_analytics';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'Google Analytics', 
            'icon' => 'fab fa-google', 
            'color' => 'orange', 
            'category' => 'tracking', 
            'desc_key' => 'admin.tracking', 
            'sub_key' => 'admin.ga_desc',
        ];
    }

    public function getFormFields(): array
    {
        return [
            'measurement_id' => [
                'label' => 'Measurement ID',
                'type' => 'text',
                'placeholder' => 'G-...',
                'rule' => 'required|string|regex:/^G-[A-Z0-9]+$/'
            ],
        ];
    }

    
}
