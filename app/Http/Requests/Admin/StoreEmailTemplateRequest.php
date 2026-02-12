<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:email_templates,slug',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string',
            'language_id' => 'required|exists:languages,id',
        ];
    }
}
