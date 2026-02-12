<?php

namespace App\Services\Integrations\Providers;

class DS_WhatsappProvider extends AbstractIntegrationProvider
{
    public function getProviderSlug(): string
    {
        return 'whatsapp';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'WhatsApp Business', 
            'icon' => 'fab fa-whatsapp', 
            'color' => 'green', 
            'category' => 'communication', 
            'desc_key' => 'admin.messaging', 
            'sub_key' => 'admin.whatsapp_desc',
        ];
    }

    public function getFormFields(): array
    {
        return [
            'phone_number_id' => [
                'label' => 'Phone Number ID',
                'type' => 'text',
                'rule' => 'required|string'
            ],
            'access_token' => [
                'label' => 'Access Token',
                'type' => 'text',
                'rule' => 'required|string'
            ],
        ];
    }

    
    
    public function sendMessage($to, $message)
    {
        
    }
}
