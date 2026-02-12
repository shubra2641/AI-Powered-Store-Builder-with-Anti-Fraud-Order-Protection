<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DS_StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug'         => 'required|string|unique:ds_payment_gateways,slug',
            'name'         => 'required|string|max:255',
            'is_active'    => 'required|boolean',
            'environment'  => 'nullable|string|in:sandbox_test,live_prod',
            'credentials'  => 'nullable|array',
        ];
    }
}
