<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Services\Profile\ProfileService;
use App\Traits\DS_TranslationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class ProfileController extends Controller
{
    use DS_TranslationHelper;

    public function __construct(
        protected ProfileService $profileService
    ) {}

    /**
     * Show the profile edit modal (via redirect back).
     */
    public function edit(): RedirectResponse
    {
        return back()->with('open_profile_modal', true);
    }

    /**
     * Update the user's profile.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        try {
            $this->profileService->updateProfile(
                Auth::user(),
                $request->validated()
            );

            $this->notifySuccess('auth.profile_updated_success');
        } catch (Exception $e) {
            $this->notifyError('auth.profile_update_failed');
        }

        return back();
    }
}
