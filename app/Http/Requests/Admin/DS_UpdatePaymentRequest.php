<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DS_UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'is_active'    => 'required|boolean',
            'environment'  => 'nullable|string|in:sandbox_test,live_prod',
            'credentials'  => 'nullable|array',
        ];
    }
}
