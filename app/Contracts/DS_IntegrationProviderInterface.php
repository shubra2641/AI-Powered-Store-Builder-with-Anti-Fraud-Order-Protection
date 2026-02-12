<?php

namespace App\Contracts;

interface DS_IntegrationProviderInterface
{
    /**
     * Get the integration settings.
     *
     * @return array
     */
    public function getSettings(): array;

    /**
     * Get the provider slug (e.g. 'whatsapp', 'sendgrid').
     *
     * @return string
     */
    public function getProviderSlug(): string;

    /**
     * Validate if the current settings are sufficient to initialize the service.
     *
     * @return bool
     */
    public function validateConfig(): bool;
    
    /**
     * Get the integration form fields definition.
     *
     * @return array
     */
    public function getFormFields(): array;

    /**
     * Get the integration metadata (name, icon, category, description keys).
     *
     * @return array
     */
    public function getMetadata(): array;
    
    /**
     * Get the underlying client instance (e.g. Guzzle client, SDK instance).
     *
     * @return mixed
     */
    public function getClient();
}
