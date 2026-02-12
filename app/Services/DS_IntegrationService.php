<?php

namespace App\Services;

use App\Models\DS_Integration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\DS_PaymentGateway;
use App\Services\Payments\DS_PaymentGatewayService;
use App\Services\Integrations\DS_IntegrationFactory;

class DS_IntegrationService
{
    protected $paymentGatewayService;

    public function __construct(DS_PaymentGatewayService $paymentGatewayService)
    {
        $this->paymentGatewayService = $paymentGatewayService;
    }

    /**
     * Get all integrations for a specific user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserIntegrations(int $userId): Collection
    {
        return DS_Integration::where('user_id', $userId)->get()->keyBy('service');
    }

    /**
     * Toggle the active status of an integration.
     *
     * @param int $userId
     * @param string $service
     * @param bool $status
     * @return DS_Integration
     */
    public function toggleIntegration(int $userId, string $service, bool $status)
    {
        return DB::transaction(function () use ($userId, $service, $status) {
            if ($this->paymentGatewayService->isPaymentGateway($service)) {
                $gateway = DS_PaymentGateway::where('slug', $service)->first();
                if ($gateway) {
                    $gateway->is_active = $status;
                    $gateway->save();
                    return $gateway;
                }
                throw new \Exception("Payment gateway must be configured before enabling.");
            }

            return DS_Integration::updateOrCreate(
                ['user_id' => $userId, 'service' => $service],
                ['is_active' => $status]
            );
        });
    }

    /**
     * Update settings for an integration.
     *
     * @param int $userId
     * @param string $service
     * @param array $settings
     * @return DS_Integration
     */
    public function updateSettings(int $userId, string $service, array $settings): DS_Integration
    {
        return DB::transaction(function () use ($userId, $service, $settings) {
            $integration = DS_Integration::firstOrNew(['user_id' => $userId, 'service' => $service]);
            
            $currentSettings = $integration->settings ?? [];
            $integration->settings = array_merge($currentSettings, $settings);
            $integration->save();

            return $integration;
        });
    }

    /**
     * Get available integrations configuration.
     *
     * @return array
     */
    public function getAvailableIntegrations(): array
    {
        $payments = $this->paymentGatewayService->getAvailableGateways();
        
        $supportedServices = DS_IntegrationFactory::getSupportedServices();
        $integrations = [];

        foreach ($supportedServices as $slug) {
            try {
                $provider = DS_IntegrationFactory::make($slug);
                $metadata = $provider->getMetadata();
                $metadata['fields'] = $provider->getFormFields();
                
                $integrations[$slug] = $metadata;
            } catch (\Exception $e) {
                continue;
            }
        }

        return array_merge($payments, $integrations);
    }
}
