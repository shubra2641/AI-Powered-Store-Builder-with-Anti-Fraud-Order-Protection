<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;
class UpdateSettingsRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if (!$this->has('group')) {
            $this->merge(['group' => $this->input('tab', 'general')]);
        }

        $group = $this->input('group', 'general');

        $rules = [
            'group' => 'required|string|in:general,smtp,seo,contact,ai',
            'language_id' => 'nullable|exists:languages,id',
        ];

        $groupRules = match ($group) {
            'general' => [
                'site_name' => 'sometimes|nullable|string|max:255',
                'site_description' => 'sometimes|nullable|string',
                'site_currency' => 'sometimes|nullable|string|max:10',
                'site_logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'site_favicon' => 'sometimes|image|mimes:ico,png,jpg,svg|max:512',
                'renewal_reminder_days' => 'sometimes|nullable|integer|min:0|max:30',
                'grace_period_days' => 'sometimes|nullable|integer|min:0|max:30',
            ],
            'smtp' => [
                'mail_from_name' => 'required|string|max:255',
                'mail_from_address' => 'required|email|max:255',
                'smtp_host' => 'required|string|max:255',
                'smtp_port' => 'required|numeric',
                'smtp_encryption' => 'nullable|string|in:ssl,tls,null',
                'smtp_username' => 'required|string|max:255',
                'smtp_password' => 'required|string|max:255',
            ],
            'seo' => [
                'seo_title' => 'sometimes|nullable|string|max:255',
                'seo_description' => 'sometimes|nullable|string',
                'seo_keywords' => 'sometimes|nullable|string',
            ],
            'contact' => [
                'contact_email' => 'sometimes|nullable|email|max:255',
                'contact_phone' => 'sometimes|nullable|string|max:50',
                'contact_address' => 'sometimes|nullable|string',
            ],
            'ai' => [
                'active_ai_provider' => 'sometimes|string|in:gemini,chatgpt,groq,claude,perplexity',
                '*_api_key' => 'sometimes|nullable|string|max:255',
                '*_model' => 'sometimes|nullable|string|max:100',
                '*_max_tokens' => 'sometimes|nullable|integer|min:1|max:100000',
            ],
            default => [],
        };

        return array_merge($rules, $groupRules);
    }
}
