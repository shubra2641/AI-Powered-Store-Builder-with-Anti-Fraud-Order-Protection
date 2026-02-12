<?php

namespace App\Services\Payments;

use App\Models\DS_PaymentGateway;

class DS_PaymentGatewayService
{
    /**
     * Create a new payment gateway with default credentials if missing.
     */
    public function createGateway(array $data): DS_PaymentGateway
    {
        if (isset($data['environment'])) {
            $data['mode'] = $data['environment'] === 'sandbox_test' ? 'sandbox' : 'live';
            $data['is_test_mode'] = $data['environment'] === 'sandbox_test' ? 1 : 0;
            unset($data['environment']);
        }

        if (!isset($data['credentials']) || empty($data['credentials'])) {
            $data['credentials'] = $this->getDefaultCredentials($data['slug']);
        }

        return DS_PaymentGateway::create($data);
    }

    /**
     * Get all programmatically supported gateways for plan configuration.
     * This is independent of admin settings.
     */
    /**
     * Get all available payment gateways with full configuration for UI.
     */
    public function getAvailableGateways(): array
    {
        $gateways = [];
        $supported = DS_PaymentFactory::getSupportedGateways();

        foreach ($supported as $slug) {
            try {
                $provider = DS_PaymentFactory::make($slug);
                $metadata = $provider->getMetadata();
                $metadata['slug'] = $slug;
                $metadata['fields'] = $provider->getFormFields();
                $gateways[$slug] = $metadata;
            } catch (\Exception $e) {
                continue;
            }
        }

        return $gateways;
    }

    /**
     * Check if the given slug is a payment gateway.
     */
    public function isPaymentGateway(string $slug): bool
    {
        $gateways = $this->getAvailableGateways();
        return array_key_exists($slug, $gateways);
    }
}
