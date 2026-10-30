<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'order_number',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'fulfilment_type',
        'status',
        'payment_method',
        'payment_status',
        'payment_id',
        'subtotal_paise',
        'discount_paise',
        'delivery_fee_paise',
        'tax_paise',
        'total_paise',
        'coupon_code',
        'delivery_address',
        'pickup_slot',
        'delivery_notes',
        'cancellation_reason',
    ];

    protected $casts = [
        'subtotal_paise' => 'integer',
        'discount_paise' => 'integer',
        'delivery_fee_paise' => 'integer',
        'tax_paise' => 'integer',
        'total_paise' => 'integer',
        'delivery_address' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            if (!$order->id) {
                $order->id = (string) Str::uuid();
            }
            if (!$order->order_number) {
                $order->order_number = 'MITHAI-2026-' . rand(1000, 9999);
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'asc');
    }
}
