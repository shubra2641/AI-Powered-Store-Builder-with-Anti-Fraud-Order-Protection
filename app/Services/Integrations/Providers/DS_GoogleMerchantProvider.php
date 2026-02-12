<?php

namespace App\Services\Integrations\Providers;

class DS_GoogleMerchantProvider extends AbstractIntegrationProvider
{
    public function getProviderSlug(): string
    {
        return 'google_merchant';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'Google Merchant', 
            'icon' => 'fas fa-shopping-bag', 
            'color' => 'blue', 
            'category' => 'shopping', 
            'desc_key' => 'admin.shopping', 
            'sub_key' => 'admin.g_merchant_desc',
        ];
    }

    public function getFormFields(): array
    {
        return [
            'merchant_id' => [
                'label' => 'Merchant ID',
                'type' => 'text',
                'rule' => 'required|string'
            ],
        ];
    }

    
}
