<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $fillable = [
        'sr',
        'date',
        'vehicle_no',
        'customer_name',
        'contact_number',
        'bags',
        'delivery_point',
        'km_covered',
        'rent',
        'advance',
        'advance_date',
        'guarantor',
        'dues',
        'status',
        'billing_month',
    ];

    protected $casts = [
        'date' => 'date',
        'advance_date' => 'date',
        'bags' => 'integer',
        'km_covered' => 'decimal:2',
        'rent' => 'decimal:2',
        'advance' => 'decimal:2',
        'dues' => 'decimal:2',
        'sr' => 'integer',
    ];
}
