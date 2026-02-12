<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\SettingsService;

class PlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'translated_name'           => 'required|array',
            'translated_name.*'         => 'required|string|max:255',
            'translated_description'    => 'nullable|array',
            'translated_description.*'  => 'nullable|string',
            'price'                     => 'required|numeric|min:0',
            'currency'                  => 'nullable|string|max:3',
            'duration_days'             => 'required|integer|min:1',
            'trial_days'                => 'nullable|integer|min:0',
            'is_active'                 => 'nullable|boolean',
            'is_featured'               => 'nullable|boolean',
            'quotas'                    => 'required|array',
            'quotas.*'                  => 'nullable', 
            'quotas.payment_gateways'   => 'nullable|array',
            'quotas.payment_gateways.*' => 'string|in:stripe,paypal,razorpay,bank_transfer',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $settings = app(SettingsService::class);
        $rawQuotas = $this->input('quotas', []);
        $quotas = is_array($rawQuotas) ? $rawQuotas : [];

        $checkboxFeatures = [
            'whatsapp', 'whatsapp_messages', 'fb_pixel', 'snap_pixel', 'twitter_pixel', 
            'tiktok_pixel', 'ga_pixel', 'google_merchant', 'google_tag_manager', 'fb_capi', 'recaptcha', 'seo', 'remove_branding'
        ];

        foreach ($checkboxFeatures as $feature) {
            $quotas[$feature] = isset($quotas[$feature]) ? 1 : 0;
        }

        $numericQuotas = ['ai_pages', 'drag_drop_pages', 'custom_domains', 'storage_gb', 'orders', 'products', 'support_messages'];
        foreach ($numericQuotas as $q) {
            if (isset($quotas[$q])) {
                $quotas[$q] = (float) $quotas[$q];
            }
        }

        $this->merge([
            'is_active'   => $this->has('is_active'),
            'is_featured' => $this->has('is_featured'),
            'price'       => (int) (round($this->input('price', 0) * 100)),
            'currency'    => $settings->get('site_currency', 'USD'),
            'quotas'      => $quotas,
        ]);
    }

    /**
     * Map the translated fields back to model attributes after successful validation.
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();

        if (is_null($key)) {
            $validated['name'] = $validated['translated_name'] ?? [];
            $validated['description'] = $validated['translated_description'] ?? [];
            unset($validated['translated_name'], $validated['translated_description']);
        }

        return $validated;
    }
}
