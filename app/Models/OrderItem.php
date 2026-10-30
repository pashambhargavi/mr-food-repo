<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OrderItem extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'product_name',
        'variant_id',
        'variant_weight',
        'unit_price_paise',
        'quantity',
        'total_price_paise',
        'image_url',
    ];

    protected $casts = [
        'unit_price_paise' => 'integer',
        'quantity' => 'integer',
        'total_price_paise' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($item) {
            if (!$item->id) {
                $item->id = (string) Str::uuid();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
