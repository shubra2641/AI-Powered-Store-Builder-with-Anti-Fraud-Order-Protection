<?php


namespace App\Services;

use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use LogicException;
use App\Services\DS_BulkDeleteResult;
use App\Notifications\SystemNotification;

/**
 * Class LanguageService
 * Provides language management and caching functionality.
 */
class LanguageService
{
    /**
     * Get all active languages from cache or DB.
     * 
     * @return Collection
     */
    public function getAllLanguages(): Collection
    {
        return Cache::rememberForever('active_languages', function () {
            return Language::all();
        });
    }

    /**
     * Get the default language.
     */
    public function getDefaultLanguage(): ?Language
    {
        return Cache::rememberForever('default_language', function () {
            return Language::where('is_default', true)->first() ?? Language::first();
        });
    }

    /**
     * Get language by code.
     */
    public function getLanguageByCode(string $code): ?Language
    {
        return Language::where('code', $code)->first();
    }

    /**
     * Clear language cache.
     */
    public function clearCache(): void
    {
        Cache::forget('active_languages');
        Cache::forget('default_language');
    }

    /**
     * Create a new language.
     * 
     * @param array $data
     * @return Language
     */
    public function createLanguage(array $data): Language
    {
        $language = Language::create($data);
        $this->clearCache();
        return $language;
    }

    /**
     * Update an existing language.
     * 
     * @param Language $language
     * @param array $data
     * @return Language
     */
    public function updateLanguage(Language $language, array $data): Language
    {
        $language->update($data);
        $this->clearCache();
        return $language;
    }

    /**
     * Delete a language.
     * 
     * @param Language $language
     * @return bool
     * @throws LogicException
     */
    public function deleteLanguage(Language $language): bool
    {
        if ($language->is_default) {
            throw new LogicException(__('admin.cannot_delete_default_language'));
        }

        $success = $language->delete();
        if ($success) {
            $this->clearCache();
        }
        return $success;
    }

    /**
     * Set a language as default.
     * 
     * @param Language $language
     * @return void
     */
    public function setDefaultLanguage(Language $language): void
    {
        DB::transaction(function () use ($language) {
            Language::where('is_default', true)->update(['is_default' => false]);
            $language->update(['is_default' => true]);
        });
        
        $this->clearCache();
    }

    /**
     * Switch the application language safely.
     *
     * @param string $code
     * @return bool
     */
    public function switchLanguage(string $code): bool
    {
        $language = Language::where('code', $code)->first();

        if (!$language) {
            return false;
        }

        session(['locale' => $code]);
        app()->setLocale($code);

        if (auth()->check()) {
            $user = auth()->user();
            $user->update([
                'language_id' => $language->id
            ]);

            $user->notify(new SystemNotification(
                __('admin.language_switched'),
                __('admin.language_switched_success', ['name' => $language->name]),
                null,
                'success'
            ));
        }
        $this->clearCache();

        return true;
    }

    /**
     * Bulk delete languages by IDs (skips default language).
     *
     * @param  array<int>  $ids List of language IDs.
     * @return DS_BulkDeleteResult Result enum.
     */
    public function bulkDeleteLanguages(array $ids): DS_BulkDeleteResult
    {
        $defaultLangIds = Language::whereIn('id', $ids)->where('is_default', true)->pluck('id')->toArray();
        $requestedCount = count($ids);
        
        if ($requestedCount > 0 && count($defaultLangIds) === $requestedCount) {
             return DS_BulkDeleteResult::NONE_DEFAULT;
        }

        $count = 0;

        DB::transaction(function () use ($ids, &$count) {
            $count = Language::whereIn('id', $ids)
                ->where('is_default', false)
                ->delete();
        });

        if ($count > 0) {
            $this->clearCache();
        }

        if ($count < $requestedCount) {
            return DS_BulkDeleteResult::PARTIAL;
        }

        return DS_BulkDeleteResult::SUCCESS;
    }
}
