<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PasswordResetToken
 * Eloquent model for the password_reset_tokens table.
 */
class PasswordResetToken extends Model
{
    /**
     * Set the primary key as email since this table doesn't have an 'id'.
     */
    protected $primaryKey = 'email';

    /**
     * Disable increments since the PK is a string.
     */
    public $incrementing = false;

    /**
     * Disable default timestamps (created_at exists but updated_at doesn't usually in this table).
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    /**
     * Cast attributes to specific types.
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
