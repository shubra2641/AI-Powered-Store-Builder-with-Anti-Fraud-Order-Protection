<?php

namespace App\Traits;

use Illuminate\Support\Facades\Session;

/**
 * Trait DS_TranslationHelper
 * Provides standardized localized flash messages.
 */
trait DS_TranslationHelper
{
    /**
     * Set a success flash message.
     */
    public function notifySuccess(string $key, array $params = []): void
    {
        Session::flash('success', __($key, $params));
    }

    /**
     * Set an error flash message.
     */
    public function notifyError(string $key, array $params = []): void
    {
        Session::flash('error', __($key, $params));
    }

    /**
     * Set an info flash message.
     */
    public function notifyInfo(string $key, array $params = []): void
    {
        Session::flash('info', __($key, $params));
    }
}
