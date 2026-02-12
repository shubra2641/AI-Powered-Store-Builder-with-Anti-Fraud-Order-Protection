<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DS_Setting
 * Stores system settings as key-value pairs.
 * 
 * @property string $key
 * @property string|null $value
 * @property string $group
 * @property bool $is_public
 */
class DS_Setting extends Model
{
    protected $table = 'ds_settings';

    protected $fillable = [
        'key',
        'value',
        'group',
        'language_id',
        'is_public',
    ];

    /**
     * Get the language associated with the setting.
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }
}
