<?php

namespace App\Services\Tracking;

class CustomScriptService extends AbstractPixelService
{
    /**
     * Render the tracking script.
     *
     * @return string
     */
    public function render(): string
    {
        $headScript = $this->config['head_script'] ?? '';
        $bodyScript = $this->config['body_script'] ?? '';

        return $headScript . "\n" . $bodyScript;
    }

    /**
     * Get the integration slug (e.g., 'facebook_pixel').
     *
     * @return string
     */
    public function getSlug(): string
    {
        return 'custom_script';
    }

    /**
     * Get the provider name (e.g., 'Facebook').
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Custom Script';
    }

    /**
     * Get the default settings required for this pixel.
     *
     * @return array
     */
    public function getDefaultSettings(): array
    {
        return [
            'head_script' => '',
            'body_script' => '',
        ];
    }
}
