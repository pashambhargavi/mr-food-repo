<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class B2bInquiry extends Model
{
    protected $fillable = [
        'company_name',
        'contact_person',
        'phone',
        'email',
        'occasion_type',
        'estimated_quantity',
        'delivery_date',
        'delivery_location',
        'special_requirements',
        'status',
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];
}
