<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\SettingsService;

/**
 * @property int $id
 * @property string $slug
 * @property bool $is_active
 */
class DS_Page extends Model
{
    protected $table = 'ds_pages';

    protected $fillable = [
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the translations for the page.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(DS_PageTranslation::class, 'page_id');
    }

    /**
     * Get a translation for a specific language.
     */
    public function translation(?int $languageId = null)
    {
        $languageId = $languageId ?? app(SettingsService::class)->getCurrentLanguageId();
        
        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('language_id', $languageId);
        }

        return $this->translations()->where('language_id', $languageId)->first();
    }
}
