<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialIncome extends Model
{
    protected $fillable = [
        'date',
        'total_income',
        'vehicle',
        'driver',
        'expense',
        'expense_category',
        'loading_point',
        'uploading_point',
        'fuel',
        'food',
        'toll',
        'driver_payment',
        'description',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'total_income' => 'decimal:2',
        'expense' => 'decimal:2',
        'fuel' => 'decimal:2',
        'food' => 'decimal:2',
        'toll' => 'decimal:2',
        'driver_payment' => 'decimal:2',
    ];

    /**
     * Set default values for optional fields
     */
    protected $attributes = [
        'expense' => 0,
        'fuel' => 0,
        'food' => 0,
        'toll' => 0,
        'driver_payment' => 0,
        'status' => 'pending',
    ];
}
