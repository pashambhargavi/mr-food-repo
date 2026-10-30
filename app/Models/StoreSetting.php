<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'shop_name',
        'shop_name_te',
        'shop_name_hi',
        'tagline',
        'tagline_te',
        'tagline_hi',
        'announcement_bar_text',
        'phone',
        'whatsapp',
        'email',
        'address',
        'google_maps_url',
        'opening_hours',
        'opening_hours_te',
        'opening_hours_hi',
        'min_order_paise',
        'delivery_fee_paise',
        'free_delivery_threshold_paise',
        'delivery_pincodes',
        'cod_enabled',
        'upi_enabled',
        'upi_id',
        'gstin',
        'fssai_license',
    ];

    protected $casts = [
        'min_order_paise' => 'integer',
        'delivery_fee_paise' => 'integer',
        'free_delivery_threshold_paise' => 'integer',
        'delivery_pincodes' => 'array',
        'cod_enabled' => 'boolean',
        'upi_enabled' => 'boolean',
    ];
}
