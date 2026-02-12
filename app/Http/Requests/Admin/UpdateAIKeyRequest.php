<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateAIKeyRequest extends FormRequest
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
        return [
            'provider' => 'required|string|in:gemini,chatgpt,groq,claude,perplexity',
            'model' => 'required|string|max:100',
            'api_key' => 'required|string|max:255',
            'max_tokens' => 'required|integer|min:1',
        ];
    }
}
