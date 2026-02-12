<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DS_PaymentGateway extends Model
{
    protected $table = 'ds_payment_gateways';

    protected $fillable = [
        'slug',
        'name',
        'credentials',
        'mode',
        'is_active',
        'is_test_mode',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean',
    ];
}
