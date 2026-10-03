<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseExpenseEntry extends Model
{
    protected $fillable = [
        'warehouse_trip_id',
        'expense_category',
        'payment_type',
        'amount',
        'description',
        'expense_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function warehouseTrip()
    {
        return $this->belongsTo(WarehouseTrip::class);
    }
}
