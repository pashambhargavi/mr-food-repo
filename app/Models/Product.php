<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'slug',
        'category_id',
        'name_en',
        'name_te',
        'name_hi',
        'description_en',
        'description_te',
        'description_hi',
        'image_url',
        'dietary_type',
        'is_veg',
        'shelf_life_days',
        'storage_en',
        'storage_te',
        'storage_hi',
        'ingredients_en',
        'ingredients_te',
        'ingredients_hi',
        'allergens',
        'is_active',
        'is_featured',
        'is_bestseller',
        'rating',
        'review_count',
    ];

    protected $casts = [
        'is_veg' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'shelf_life_days' => 'integer',
        'rating' => 'float',
        'review_count' => 'integer',
        'allergens' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
