<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'product_id',
        'weight_label',
        'weight_grams',
        'price_paise',
        'compare_at_price_paise',
        'stock_quantity',
        'is_available',
        'sku',
    ];

    protected $casts = [
        'weight_grams' => 'integer',
        'price_paise' => 'integer',
        'compare_at_price_paise' => 'integer',
        'stock_quantity' => 'integer',
        'is_available' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
