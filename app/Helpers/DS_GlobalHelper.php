<?php

namespace App\Helpers;

use App\Services\SettingsService;

class DS_GlobalHelper
{
    /**
     * Format a flash message array.
     * 
     * @param string $message
     * @param string $type
     * @return array
     */
    public static function formatFlash(string $message, string $type = 'success'): array
    {
        return [
            'message' => __($message),
            'type'    => $type
        ];
    }
}
