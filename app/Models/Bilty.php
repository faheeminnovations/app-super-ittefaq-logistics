<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\CurrencyHelper;

class Bilty extends Model
{
    use SoftDeletes;

    protected $table = 'bilties';

    protected $fillable = [
        'bilty_number',
        'bilty_date',
        'from_location',
        'to_location',
        'vehicle_number',
        'driver_name',
        'card_number',
        'sender_name',
        'sender_phone',
        'receiver_name',
        'receiver_phone',
        'goods_description',
        'quantity',
        'quantity_unit',
        'total_amount',
        'advance_amount',
        'remaining_balance',
        'rent_amount',
        'status',
        'notes',
        'customer_id',
        'vehicle_id',
        'driver_id',
        'job_id',
        'registration_number',
        'contact_details',
    ];

    protected $casts = [
        'bilty_date' => 'date',
        'total_amount' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'rent_amount' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    // Accessors for formatted currency values
    public function getFormattedTotalAmountAttribute()
    {
        return CurrencyHelper::formatCurrency($this->total_amount);
    }

    public function getFormattedAdvanceAmountAttribute()
    {
        return CurrencyHelper::formatCurrency($this->advance_amount);
    }

    public function getFormattedRemainingBalanceAttribute()
    {
        return CurrencyHelper::formatCurrency($this->remaining_balance);
    }

    public function getFormattedRentAmountAttribute()
    {
        return CurrencyHelper::formatCurrency($this->rent_amount);
    }

    // Scope for active bilties
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    // Scope for delivered bilties
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    // Scope for pending bilties
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Auto-generate bilty number
    public static function generateBiltyNumber()
    {
        $prefix = 'BLT';
        $year = date('Y');
        $month = date('m');
        
        $lastBilty = self::where('bilty_number', 'like', "{$prefix}-{$year}-{$month}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastBilty) {
            $lastNumber = (int) substr($lastBilty->bilty_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}-{$year}-{$month}-{$newNumber}";
    }

    // Calculate remaining balance automatically
    public function calculateRemainingBalance()
    {
        $this->remaining_balance = $this->total_amount - $this->advance_amount;
        return $this->remaining_balance;
    }
}
