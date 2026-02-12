<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DS_IntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Integrations\DS_IntegrationFactory;
use App\Services\Payments\DS_PaymentFactory;
use App\Models\DS_PaymentGateway;

class DS_IntegrationController extends Controller
{
    protected $integrationService;

    public function __construct(DS_IntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    public function index()
    {
        $userIntegrations = $this->integrationService->getUserIntegrations(Auth::id());
        $paymentGateways = DS_PaymentGateway::all()->keyBy('slug');
        $availableServices = $this->integrationService->getAvailableIntegrations();

        $search = request('search');
        $category = request('category');
        $status = request('status');

        $filteredIntegrations = [];

        foreach ($availableServices as $key => $service) {
            $type = $service['type'] ?? 'integration';
            $isActive = false;
            $isConfigured = false;
            $id = null;

            if ($type === 'payment') {
                if ($paymentGateways->has($key)) {
                    $gateway = $paymentGateways->get($key);
                    $isActive = $gateway->is_active;
                    $isConfigured = true;
                    $id = $gateway->id;
                }
            } else {
                $isConfigured = true;
                if ($userIntegrations->has($key)) {
                    $integrationModel = $userIntegrations->get($key);
                    $isActive = $integrationModel->is_active;
                    $settings = $integrationModel->settings ?? [];
                }
            }

            if ($search && stripos($service['name'], $search) === false) {
                continue;
            }

            if ($category && $category !== 'all' && ($service['category'] ?? '') !== $category) {
                continue;
            }

            if ($status && $status !== 'all') {
                $statusBool = $status === 'active';
                if ($isActive !== $statusBool) {
                    continue;
                }
            }

            $filteredIntegrations[] = (object) [
                'key' => $key,
                'name' => $service['name'],
                'icon' => $service['icon'],
                'color' => $service['color'],
                'desc_key' => $service['desc_key'],
                'sub_key' => $service['sub_key'],
                'fields' => $service['fields'] ?? [],
                'type' => $type,
                'category' => $service['category'] ?? 'other',
                'is_active' => $isActive,
                'is_configured' => $isConfigured,
                'settings' => $settings ?? [],
                'id' => $id,
            ];
        }

        return view('admin.integrations.index', ['integrations' => $filteredIntegrations]);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'service' => 'required|string',
            'status' => 'required|boolean',
        ]);

        try {
            $this->integrationService->toggleIntegration(Auth::id(), $request->service, $request->status);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Integration Toggle Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('admin.error_occurred')], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'service' => 'required|string',
            'settings' => 'required|array',
        ]);

        $rules = $this->getServiceValidationRules($request->service);
        if (!empty($rules)) {
             $request->validate($rules);
        }

        try {
            $this->integrationService->updateSettings(Auth::id(), $request->service, $request->settings);
            return back()->with('success', __('admin.settings_saved'));
        } catch (\Exception $e) {
            \Log::error('Integration Update Error: ' . $e->getMessage());
            return back()->with('error', __('admin.error_saving_settings'));
        }
    }

    /**
     * Get validation rules for specific services.
     */
    /**
     * Get validation rules for specific services from config.
     */
    protected function getServiceValidationRules(string $service): array
    {
        try {
            $provider = DS_IntegrationFactory::make($service);
            $fields = $provider->getFormFields();
        } catch (\Exception $e) {
            try {
                $provider = DS_PaymentFactory::make($service);
                $fields = $provider->getFormFields();
            } catch (\Exception $ex) {
                return [];
            }
        }

        $rules = [];
        foreach ($fields as $key => $fieldConfig) {
            $rules["settings.{$key}"] = $fieldConfig['rule'] ?? 'nullable';
        }

        return $rules;
    }
}
