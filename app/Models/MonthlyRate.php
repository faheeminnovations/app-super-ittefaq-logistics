<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyRate extends Model
{
    protected $fillable = [
        'vehicle_category',
        'rate_per_km',
        'billing_month',
        'billing_year',
        'billing_month_number',
    ];

    protected $casts = [
        'rate_per_km' => 'decimal:2',
        'billing_year' => 'integer',
        'billing_month_number' => 'integer',
    ];
}
