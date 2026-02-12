<?php

namespace App\Services\Integrations\Providers;

class DS_LinkedinInsightProvider extends AbstractIntegrationProvider
{
    public function getProviderSlug(): string
    {
        return 'linkedin_insight';
    }

    public function getMetadata(): array
    {
        return [
            'name' => 'LinkedIn Insight', 
            'icon' => 'fab fa-linkedin', 
            'color' => 'blue', 
            'category' => 'tracking', 
            'desc_key' => 'admin.tracking', 
            'sub_key' => 'admin.linkedin_desc',
        ];
    }

    public function getFormFields(): array
    {
        return [
            'partner_id' => [
                'label' => 'Partner ID',
                'type' => 'text',
                'rule' => 'required|string'
            ],
        ];
    }

    
}
