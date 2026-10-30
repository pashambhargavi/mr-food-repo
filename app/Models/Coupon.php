<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'code',
        'discount_type',
        'discount_value',
        'min_order_amount_paise',
        'max_discount_paise',
        'is_active',
        'expires_at',
        'usage_count',
    ];

    protected $casts = [
        'discount_value' => 'integer',
        'min_order_amount_paise' => 'integer',
        'max_discount_paise' => 'integer',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'usage_count' => 'integer',
    ];
}
