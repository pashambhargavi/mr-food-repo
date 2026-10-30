<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OrderStatusHistory extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'order_id',
        'status',
        'note',
        'created_at',
    ];

    protected static function booted()
    {
        static::creating(function ($h) {
            if (!$h->id) {
                $h->id = (string) Str::uuid();
            }
            if (!$h->created_at) {
                $h->created_at = now();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
