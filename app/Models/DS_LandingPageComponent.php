<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DS_LandingPageComponent extends Model
{
    protected $table = 'ds_landing_page_components';

    protected $fillable = [
        'name',
        'category',
        'thumbnail',
        'blade_template',
        'config_schema',
    ];

    protected $appends = ['thumbnail_url'];

    protected $casts = [
        'config_schema' => 'array',
    ];

    /**
     * Get the absolute URL for the thumbnail.
     *
     * @return string
     */
    public function getThumbnailUrlAttribute(): string
    {
        $value = $this->attributes['thumbnail'] ?? null;
        
        if (empty($value)) {
            return 'https://images.unsplash.com/photo-1557683316-973673baf926?auto=format&fit=crop&q=80&w=800';
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return asset($value);
    }
}
