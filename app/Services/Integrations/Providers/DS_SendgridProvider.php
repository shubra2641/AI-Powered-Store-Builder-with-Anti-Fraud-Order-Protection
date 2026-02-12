<?php

namespace App\Services\Integrations\Providers;

class DS_SendgridProvider extends AbstractIntegrationProvider
{
    public function getProviderSlug(): string
    {
        return 'sendgrid';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'SendGrid', 
            'icon' => 'fas fa-envelope', 
            'color' => 'purple', 
            'category' => 'communication', 
            'desc_key' => 'admin.email', 
            'sub_key' => 'admin.sendgrid_desc',
        ];
    }

    public function getFormFields(): array
    {
        return [
            'api_key' => [
                'label' => 'API Key',
                'type' => 'text',
                'placeholder' => 'SG...',
                'rule' => 'required|string'
            ],
            'from_email' => [
                'label' => 'From Email',
                'type' => 'email',
                'rule' => 'required|email'
            ],
        ];
    }

    
    
    public function sendEmail($to, $subject, $content)
    {
        
    }
}
