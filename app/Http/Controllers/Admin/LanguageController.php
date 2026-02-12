<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLanguageRequest;
use App\Http\Requests\Admin\UpdateLanguageRequest;
use App\Models\Language;
use App\Services\LanguageService;
use App\Services\DS_BulkDeleteResult;
use App\Traits\DS_TranslationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use LogicException;

class LanguageController extends Controller
{
    use DS_TranslationHelper;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected LanguageService $languageService
    ) {}

    /**
     * Display a listing of languages.
     */
    public function index(): View
    {
        $languages = $this->languageService->getAllLanguages();
        return view('admin.languages.index', compact('languages'));
    }

    /**
     * Store a newly created language in storage.
     *
     * @param StoreLanguageRequest $request
     * @return RedirectResponse
     */
    public function store(StoreLanguageRequest $request): RedirectResponse
    {
        $this->languageService->createLanguage($request->validated());

        $this->notifySuccess('admin.language_created_success');
        return redirect()->route('admin.languages.index');
    }

    /**
     * Update the specified language in storage.
     *
     * @param UpdateLanguageRequest $request
     * @param Language $language
     * @return RedirectResponse
     */
    public function update(UpdateLanguageRequest $request, Language $language): RedirectResponse
    {
        $this->languageService->updateLanguage($language, $request->validated());

        $this->notifySuccess('admin.language_updated_success');
        return redirect()->route('admin.languages.index');
    }

    /**
     * Remove the specified language from storage.
     *
     * @param Language $language
     * @return RedirectResponse
     */
    public function destroy(Language $language): RedirectResponse
    {
        try {
            $this->languageService->deleteLanguage($language);
            $this->notifySuccess('admin.language_deleted_success');
        } catch (LogicException $e) {
            $this->notifyError($e->getMessage());
        }

        return redirect()->route('admin.languages.index');
    }

    /**
     * Set the specified language as default.
     *
     * @param Language $language
     * @return RedirectResponse
     */
    public function setDefault(Language $language): RedirectResponse
    {
        $this->languageService->setDefaultLanguage($language);

        $this->notifySuccess('admin.default_language_set_success');
        return redirect()->route('admin.languages.index');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        /** @var array<int> $ids */
        $ids = (array) $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.languages.index');
        }

        $result = $this->languageService->bulkDeleteLanguages($ids);

        match ($result) {
            DS_BulkDeleteResult::SUCCESS => $this->notifySuccess('admin.bulk_deleted_success'),
            DS_BulkDeleteResult::PARTIAL => session()->flash('bulk_modal_message', __('admin.bulk_deleted_with_skips')),
            DS_BulkDeleteResult::NONE_DEFAULT => session()->flash('bulk_modal_message', __('admin.cannot_delete_default_language')),
            default => session()->flash('bulk_modal_message', __('admin.cannot_delete_default_language')),
        };

        return redirect()->route('admin.languages.index');
    }
}
