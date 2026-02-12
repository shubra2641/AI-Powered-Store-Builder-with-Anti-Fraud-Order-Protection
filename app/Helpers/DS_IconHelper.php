<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class DS_IconHelper
{
    /**
     * Get a curated list of Font Awesome 6 icons.
     * 
     * @return array
     */
    public static function getIcons(): array
    {
        $path = resource_path('data/icons.json');
        
        if (File::exists($path)) {
            return json_decode(File::get($path), true) ?? [];
        }

        return [];
    }
}
