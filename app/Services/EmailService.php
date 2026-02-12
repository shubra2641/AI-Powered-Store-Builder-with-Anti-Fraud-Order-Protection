<?php

namespace App\Services;

use App\Mail\BaseEmailMailable;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\Language;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\SettingsService;
use Exception;

/**
 * Class EmailService
 * Manages database-driven email templates and queueing.
 */
class EmailService
{
    public function __construct(
        protected SettingsService $settingsService
    ) {}

    /**
     * Send email using a database template.
     * 
     * @param User $user
     * @param string $templateSlug
     * @param array<string, mixed> $data
     * @return void
     */
    public function sendTemplateEmail(User $user, string $templateSlug, array $data = []): void
    {
        $languageId = $user->language_id ?? $this->getDefaultLanguageId();
        
        $template = EmailTemplate::where('slug', $templateSlug)
            ->where('language_id', $languageId)
            ->first();

        if (!$template) {
            $template = EmailTemplate::where('slug', $templateSlug)->first();
        }

        if ($template) {
            try {
                $this->settingsService->setSmtpConfig();
                Mail::to($user->email)->queue(new BaseEmailMailable($template, array_merge(['user' => $user], $data)));
            } catch (Exception $e) {
                Log::error("Failed to queue email [{$templateSlug}] to [{$user->email}]: " . $e->getMessage());
            }
        }
    }

    /**
     * Get default language ID.
     * 
     * @return int
     */
    protected function getDefaultLanguageId(): int
    {
        return Language::where('is_default', true)->first()?->id ?? 1;
    }
}
