<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DS_LandingPageTranslation extends Model
{
    protected $table = 'ds_landing_page_translations';

    protected $fillable = [
        'landing_page_id',
        'language_id',
        'title',
        'meta_description',
    ];

    /**
     * Get the landing page that owns the translation.
     */
    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(DS_LandingPage::class, 'landing_page_id');
    }

    /**
     * Get the language of the translation.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
