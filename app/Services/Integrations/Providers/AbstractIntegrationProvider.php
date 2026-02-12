<?php

namespace App\Services\Integrations\Providers;

use App\Contracts\DS_IntegrationProviderInterface;

abstract class AbstractIntegrationProvider implements DS_IntegrationProviderInterface
{
    protected array $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function validateConfig(): bool
    {
        return !empty($this->settings);
    }

    public function getFormFields(): array
    {
        return [];
    }

    public function getMetadata(): array
    {
        return [];
    }

    public function getClient()
    {
        return null; // Default implementation
    }
}
