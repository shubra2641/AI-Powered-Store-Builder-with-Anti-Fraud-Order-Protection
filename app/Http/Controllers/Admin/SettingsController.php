<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Language;
use App\Models\DS_AIKey;
use App\Models\User;
use App\Services\SettingsService;
use App\Services\AIService;
use App\Traits\DS_TranslationHelper;
use App\Traits\DS_UploadHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Exception;

class SettingsController extends Controller
{
    use DS_TranslationHelper;

    public function __construct(
        protected SettingsService $settingsService,
        protected AIService $aiService
    ) {}

    /**
     * Display the settings page.
     */
    public function index(): View
    {
        $languages = Language::all();
        $aiKeys = DS_AIKey::all();
        $aiModels = $this->aiService->getSupportedModels();
        $settings = $this->settingsService;
        
        return view('admin.settings.index', compact('languages', 'settings', 'aiKeys', 'aiModels'));
    }

    /**
     * Update settings for a specific group.
     *
     * @param UpdateSettingsRequest $request
     * @return RedirectResponse
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $group = $request->input('group', 'general');
        $data = $request->validated();
        $languageId = $request->input('language_id');

        unset($data['group'], $data['language_id']);

        $this->settingsService->updateSettingsWithUploads($data, $group, $languageId);

        $this->notifySuccess('admin.settings_updated_success');

        return redirect()->route('admin.settings.index', ['tab' => $group]);
    }

    /**
     * Test the SMTP connection and send a test email.
     *
     * @return JsonResponse
     */
    public function testSmtp(): JsonResponse
    {
        try {
            $this->settingsService->sendTestEmail(auth()->user());

            return response()->json([
                'success' => true,
                'message' => __('admin.smtp_test_success')
            ]);
        } catch (Exception $e) {
            Log::error('SMTP Test Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('admin.smtp_test_failed') . ': ' . $e->getMessage()
            ], 500);
        }
    }
}
