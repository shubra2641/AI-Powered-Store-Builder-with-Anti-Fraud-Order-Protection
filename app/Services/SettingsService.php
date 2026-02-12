<?php

namespace App\Services;

use App\Models\DS_Setting;
use App\Models\Language;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;
use App\Mail\BaseEmailMailable;
use App\Models\EmailTemplate;
use App\Models\User;

/**
 * Class SettingsService
 * Manages system-wide settings with long-term caching.
 */
class SettingsService
{
    use \App\Traits\DS_UploadHelper;

    protected const CACHE_KEY = 'ds_settings_all';

    /**
     * Get the current language ID based on app locale.
     * 
     * @return int|null
     */
    public function getCurrentLanguageId(): ?int
    {
        $locale = app()->getLocale();
        return Cache::rememberForever('ds_lang_id_' . $locale, function () use ($locale) {
            return Language::where('code', $locale)->value('id');
        });
    }

    /**
     * Get all settings for a specific language or global ones.
     * 
     * @param int|null $languageId
     * @return Collection
     */
    public function getAllSettings(?int $languageId = null): Collection
    {
        $cacheKey = self::CACHE_KEY . '_' . ($languageId ?? 'global');

        return Cache::rememberForever($cacheKey, function () use ($languageId) {
            return DS_Setting::where('language_id', $languageId)->pluck('value', 'key');
        });
    }

    /**
     * Get a specific setting value with multi-level fallback.
     * Hierarchy: current language -> default language -> global -> default value.
     * 
     * @param string $key
     * @param mixed $default
     * @param int|null $languageId
     * @return mixed
     */
    public function get(string $key, mixed $default = null, $languageId = 'default'): mixed
    {
        $langId = ($languageId === 'default') ? $this->getCurrentLanguageId() : $languageId;

        if ($langId) {
            $langSettings = $this->getAllSettings($langId);
            if ($langSettings->has($key) && !empty($langSettings->get($key))) {
                return $langSettings->get($key);
            }
        }

        $defaultLangId = Cache::rememberForever('ds_default_lang_id', function() {
            return Language::where('is_default', true)->value('id');
        });

        if ($defaultLangId && $defaultLangId !== $langId) {
            $defaultLangSettings = $this->getAllSettings($defaultLangId);
            if ($defaultLangSettings->has($key) && !empty($defaultLangSettings->get($key))) {
                return $defaultLangSettings->get($key);
            }
        }

        $globalSettings = $this->getAllSettings(null);
        return $globalSettings->get($key, $default);
    }

    /**
     * Get Logo URL from settings.
     * 
     * @return string|null
     */
    public function logoUrl(): ?string
    {
        $path = $this->get('site_logo');
        return $path ? Storage::url($path) : null;
    }

    /**
     * Get Favicon URL from settings.
     * 
     * @return string|null
     */
    public function faviconUrl(): ?string
    {
        $path = $this->get('site_favicon');
        return $path ? Storage::url($path) : null;
    }

    /**
     * Update or create multiple settings.
     * 
     * @param array $settings Key-value pairs of settings.
     * @param string $group
     * @param int|null $languageId
     * @return void
     */
    public function updateSettings(array $settings, string $group = 'general', ?int $languageId = null): void
    {
        foreach ($settings as $key => $value) {
            DS_Setting::updateOrCreate(
                ['key' => $key, 'language_id' => $languageId],
                ['value' => $value, 'group' => $group]
            );
        }

        $this->clearCache($languageId);
        
        if ($group === 'smtp') {
             $this->setSmtpConfig(collect($settings));
        }
    }

    /**
     * Update settings handling file uploads automatically.
     * 
     * @param array $data
     * @param string $group
     * @param int|null $languageId
     * @return void
     */
    public function updateSettingsWithUploads(array $data, string $group = 'general', ?int $languageId = null): void
    {
        if ($group === 'general' && !$languageId) {
            foreach (['site_logo', 'site_favicon'] as $fileKey) {
                if (isset($data[$fileKey]) && $data[$fileKey] instanceof \Illuminate\Http\UploadedFile) {
                    $oldFile = $this->get($fileKey);
                    $data[$fileKey] = $this->uploadFile($data[$fileKey], 'settings', $oldFile);
                }
            }
        }

        $this->updateSettings($data, $group, $languageId);
    }

    /**
     * Clear the settings cache for a specific language or global.
     * 
     * @param int|null $languageId
     * @return void
     */
    public function clearCache(?int $languageId = null): void
    {
        Cache::forget(self::CACHE_KEY . '_' . ($languageId ?? 'global'));
    }

    /**
     * Set SMTP configuration dynamically from settings.
     * 
     * @param Collection|null $settings Pre-loaded settings collection.
     * @return void
     */
    public function setSmtpConfig(?Collection $settings = null): void
    {
        $settings = $settings ?? $this->getAllSettings(null);

        $host = $settings->get('smtp_host');
        $port = $settings->get('smtp_port');
        $username = $settings->get('smtp_username');
        $password = $settings->get('smtp_password');

        if ($host && $port && $username && $password) {
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', $host);
            Config::set('mail.mailers.smtp.port', $port);
            Config::set('mail.mailers.smtp.encryption', $settings->get('smtp_encryption'));
            Config::set('mail.mailers.smtp.username', $username);
            Config::set('mail.mailers.smtp.password', $password);
            Config::set('mail.from.address', $settings->get('mail_from_address', 'hello@example.com'));
            Config::set('mail.from.name', $settings->get('mail_from_name', config('app.name')));
        } else {
            Config::set('mail.default', 'log');
        }
    }

    /**
     * Send a test SMTP email to a user.
     * 
     * @param User $user
     * @return void
     */
    public function sendTestEmail(User $user): void
    {
        $this->setSmtpConfig();

        $siteName = $this->get('site_name', config('app.name'));
        $langId = $this->getCurrentLanguageId();

        $template = EmailTemplate::where('slug', 'smtp_test')
            ->where('language_id', $langId)
            ->first();
        if (!$template) {
            $template = EmailTemplate::where('slug', 'smtp_test')->first();
        }

        if (!$template) {
            throw new \Exception("SMTP test email template not found in database.");
        }

        try {
            Mail::to($user->email)->send(new BaseEmailMailable($template, [
                'site_name' => $siteName
            ]));
        } catch (\Exception $e) {
            throw new \Exception("Failed to send test email: " . $e->getMessage());
        }
    }
}
