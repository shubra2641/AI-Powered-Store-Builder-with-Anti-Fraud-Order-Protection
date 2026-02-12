<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAIKeyRequest;
use App\Http\Requests\Admin\UpdateAIKeyRequest;
use App\Models\DS_AIKey;
use App\Models\User;
use App\Services\AIService;
use App\Traits\DS_TranslationHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AIKeyController extends Controller
{
    use DS_TranslationHelper;

    /**
     * @var AIService
     */
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Store a newly created AI key.
     * 
     * @param StoreAIKeyRequest $request
     * @return RedirectResponse
     */
    public function store(StoreAIKeyRequest $request): RedirectResponse
    {
        $this->aiService->createKey($request->validated());

        $this->notifySuccess('admin.ai_key_added_success');

        return redirect()->back()->with('tab', 'ai');
    }

    /**
     * Update the specified AI key.
     * 
     * @param UpdateAIKeyRequest $request
     * @param DS_AIKey $aiKey
     * @return RedirectResponse
     */
    public function update(UpdateAIKeyRequest $request, DS_AIKey $aiKey): RedirectResponse
    {
        $this->aiService->updateKey($aiKey, $request->validated());

        $this->notifySuccess('admin.ai_key_updated_success');

        return redirect()->back()->with('tab', 'ai');
    }

    /**
     * Remove the specified AI key.
     * 
     * @param DS_AIKey $aiKey
     * @return RedirectResponse
     */
    public function destroy(DS_AIKey $aiKey): RedirectResponse
    {
        $this->aiService->deleteKey($aiKey);

        $this->notifySuccess('admin.ai_key_deleted_success');

        return redirect()->back()->with('tab', 'ai');
    }

    /**
     * Activate the specified AI key.
     * 
     * @param DS_AIKey $aiKey
     * @return RedirectResponse
     */
    public function activate(DS_AIKey $aiKey): RedirectResponse
    {
        try {
            $this->aiService->activateKey($aiKey);
            $this->notifySuccess('admin.ai_key_activated_success');
        } catch (\Exception $e) {
            $this->notifyError($e->getMessage());
        }

        return redirect()->back()->with('tab', 'ai');
    }

    /**
     * Test the API connection.
     * 
     * @param DS_AIKey $aiKey
     * @return JsonResponse
     */
    public function test(DS_AIKey $aiKey): JsonResponse
    {
        try {
            $result = $this->aiService->testConnection($aiKey);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
