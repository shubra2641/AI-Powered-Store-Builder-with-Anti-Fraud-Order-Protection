<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DS_RecaptchaRule;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];

        if (captcha_active()) {
            $rules['g-recaptcha-response'] = ['required', new DS_RecaptchaRule];
        }

        return $rules;
    }
}
