<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripExpenseEntry extends Model
{
    protected $fillable = [
        'trip_operation_id',
        'expense_category',
        'expense_type',
        'payment_type',
        'amount',
        'description',
        'notes',
        'expense_date',
        'receipt_number',
        'bill_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function tripOperation()
    {
        return $this->belongsTo(TripOperation::class);
    }
}
