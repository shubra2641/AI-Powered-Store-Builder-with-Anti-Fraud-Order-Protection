<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $direction
 * @property string $status
 * @property bool $is_default
 */
class Language extends Model
{
    protected $fillable = [
        'name',
        'code',
        'direction',
        'status',
        'is_default',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function emailTemplates(): HasMany
    {
        return $this->hasMany(EmailTemplate::class);
    }
}
