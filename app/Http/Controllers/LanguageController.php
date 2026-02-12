<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Services\LanguageService;
use App\Traits\DS_TranslationHelper;
use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    use DS_TranslationHelper;

    /**
     * Switch application language.
     */
    public function switch(string $code, LanguageService $languageService): RedirectResponse
    {
        $languageService->switchLanguage($code);

        $this->notifySuccess('admin.language_switched_success', [
            'name' => $languageService->getLanguageByCode($code)?->name ?? $code
        ]);

        return redirect()->back();
    }
}
