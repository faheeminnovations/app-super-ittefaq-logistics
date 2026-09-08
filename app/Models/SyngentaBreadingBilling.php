<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyngentaBreadingBilling extends Model
{
    protected $fillable = [
        'serial_number',
        'date',
        'vehicle_number',
        'delivery_point',
        'kilometers',
        'rate',
        'amount',
        'status',
        'billing_month',
    ];

    protected $casts = [
        'date' => 'date',
        'kilometers' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function scopeByMonth($query, $month)
    {
        return $query->where('billing_month', $month);
    }
}
