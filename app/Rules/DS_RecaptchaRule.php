<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\Security\DS_CaptchaService;

class DS_RecaptchaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $captchaService = app(DS_CaptchaService::class);

        if ($captchaService->isActive()) {
            if (!$captchaService->verify($value)) {
                $fail(__('auth.recaptcha_failed'));
            }
        }
    }
}
