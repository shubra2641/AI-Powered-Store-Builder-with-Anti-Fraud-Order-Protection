<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendBulkEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template_id' => 'nullable|exists:email_templates,id',
            'subject' => 'required_without:template_id|nullable|string|max:255',
            'content' => 'required_without:template_id|nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ];
    }
}
