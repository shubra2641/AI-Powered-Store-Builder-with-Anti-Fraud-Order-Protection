<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $page_id
 * @property int $language_id
 * @property string $title
 * @property string $content
 */
class DS_PageTranslation extends Model
{
    protected $table = 'ds_page_translations';

    protected $fillable = [
        'page_id',
        'language_id',
        'title',
        'content',
    ];

    /**
     * Get the page that owns the translation.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(DS_Page::class, 'page_id');
    }

    /**
     * Get the language of the translation.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
