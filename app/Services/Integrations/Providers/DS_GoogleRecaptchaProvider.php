<?php

namespace App\Services\Integrations\Providers;

class DS_GoogleRecaptchaProvider extends AbstractIntegrationProvider
{
    public function getProviderSlug(): string
    {
        return 'google_recaptcha';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'Google ReCaptcha', 
            'icon' => 'fas fa-robot', 
            'color' => 'blue', 
            'category' => 'security', 
            'desc_key' => 'admin.security', 
            'sub_key' => 'admin.recaptcha_desc',
        ];
    }

    public function getFormFields(): array
    {
        return [
            'version' => [
                'label' => 'reCAPTCHA Version',
                'type' => 'select',
                'options' => [
                    'v2_checkbox' => 'v2 Checkbox',
                    'v2_invisible' => 'v2 Invisible',
                    'v3' => 'v3 (Score based)',
                ],
                'rule' => 'required|string|in:v2_checkbox,v2_invisible,v3'
            ],
            'site_key' => [
                'label' => 'Site Key',
                'type' => 'text',
                'rule' => 'required|string'
            ],
            'secret_key' => [
                'label' => 'Secret Key',
                'type' => 'text',
                'rule' => 'required|string'
            ],
        ];
    }

    
}
