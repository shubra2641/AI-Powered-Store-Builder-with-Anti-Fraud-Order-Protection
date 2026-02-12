<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\Presenters\DS_LandingPagePresenter;

class DS_LandingPage extends Model
{
    use DS_LandingPagePresenter;

    protected $table = 'ds_landing_pages';

    protected $fillable = [
        'user_id',
        'slug',
        'builder_content',
        'cached_html',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'builder_content' => 'array',
            'cached_html' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the translations for the landing page.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(DS_LandingPageTranslation::class, 'landing_page_id');
    }

    /**
     * Get the user who owns the landing page.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
