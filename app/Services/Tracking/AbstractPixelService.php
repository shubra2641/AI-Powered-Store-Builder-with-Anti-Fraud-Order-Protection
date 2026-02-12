<?php

namespace App\Services\Tracking;

use App\Contracts\DS_PixelServiceInterface;
use App\Models\DS_Integration;

/**
 * Class AbstractPixelService
 *
 * Provides shared logic for fetching integration settings.
 *
 * @package App\Services\Tracking
 */
abstract class AbstractPixelService implements DS_PixelServiceInterface
{
    /**
     * @var array
     */
    protected array $settings = [];

    /**
     * AbstractPixelService constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    /**
     * Get the integration service slug.
     *
     * @return string
     */
    abstract protected function getServiceSlug(): string;

    /**
     * Public method to access slug for manager orchestration.
     * 
     * @return string
     */
    public function getSlugForManager(): string
    {
        return $this->getServiceSlug();
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return !empty($this->settings);
    }
}
