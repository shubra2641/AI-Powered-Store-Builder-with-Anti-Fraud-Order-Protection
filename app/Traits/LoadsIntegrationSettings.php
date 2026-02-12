<?php

namespace App\Traits;

use App\Models\DS_Integration;

trait LoadsIntegrationSettings
{
    /**
     * @var array
     */
    protected array $settings = [];

    /**
     * @var bool
     */
    protected bool $active = false;

    /**
     * Load settings from the database.
     *
     * @param int|null $userId
     * @param string $serviceSlug
     */
    protected function loadSettings(?int $userId, string $serviceSlug): void
    {
        if (!$userId) {
            return;
        }

        $integration = DS_Integration::where('user_id', $userId)
            ->where('service', $serviceSlug)
            ->where('is_active', true)
            ->first();

        if ($integration) {
            $this->settings = $integration->settings ?? [];
            $this->active = true;
        }
    }

    /**
     * Check if the service is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Get the current settings.
     *
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }
}
