<?php

namespace App\Contracts;

/**
 * Interface DS_PixelServiceInterface
 *
 * Defines the contract for all tracking pixel services.
 * Follows SRP by focusing only on rendering the tracking script.
 *
 * @package App\Contracts
 */
interface DS_PixelServiceInterface
{
    /**
     * Render the tracking pixel script tag.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Check if the pixel service is active and correctly configured.
     *
     * @return bool
     */
    public function isActive(): bool;
}
