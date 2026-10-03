<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\CurrencyHelper;
use App\Models\WarehouseInvoice;

class WarehouseTrip extends Model
{
    use SoftDeletes;

    protected $table = 'warehouse_trips';

    protected $fillable = [
        'trip_number',
        'trip_date',
        'vehicle_number',
        'gp_number',
        'delivery_point',
        'vehicle_type',
        'kilometers',
        'rate_per_km',
        'freight',
        'driver_name',
        'freight_bill_no',
        'billing_month',
        'warehouse_location',
        'gl_number',
        'business_area',
        'business_category',
        'invoice_number',
        'warehouse_invoice_id',
        'notes',
        'status',
        'vehicle_id',
        'driver_id',
        'customer_id',
        'total_income',
        'total_expense',
        'net_amount',
    ];

    protected $casts = [
        'trip_date' => 'date',
        'kilometers' => 'decimal:2',
        'rate_per_km' => 'decimal:2',
        'freight' => 'decimal:2',
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    // Relationships
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouseInvoice()
    {
        return $this->belongsTo(WarehouseInvoice::class);
    }

    public function expenseEntries()
    {
        return $this->hasMany(WarehouseExpenseEntry::class);
    }

    // Accessors for formatted currency values
    public function getFormattedFreightAttribute()
    {
        return CurrencyHelper::formatCurrency($this->freight);
    }

    public function getFormattedRatePerKmAttribute()
    {
        return CurrencyHelper::formatCurrency($this->rate_per_km);
    }

    // Accessors for calculated fields
    public function getTotalExpenseAttribute($value)
    {
        // Calculate total expense from expense entries if not set
        if ($value === null || $value === 0) {
            return $this->expenseEntries()->sum('amount');
        }
        return $value;
    }

    public function getNetAmountAttribute($value)
    {
        // Calculate net amount if not set
        if ($value === null || $value === 0) {
            return $this->total_income - $this->total_expense;
        }
        return $value;
    }

    // Calculate net amount (profit/loss)
    public function calculateNetAmount()
    {
        $this->total_expense = $this->expenseEntries()->sum('amount');
        $this->net_amount = $this->total_income - $this->total_expense;
        return $this->net_amount;
    }

    // Recalculate all financial amounts
    public function recalculateFinancials()
    {
        $this->total_income = $this->freight;
        $this->total_expense = $this->expenseEntries()->sum('amount');
        $this->net_amount = $this->total_income - $this->total_expense;
        $this->save();
    }

    // Auto-generate trip number
    public static function generateTripNumber()
    {
        $prefix = 'TRP';
        $year = date('Y');
        $month = date('m');
        
        $lastTrip = self::withTrashed()
            ->where('trip_number', 'like', "{$prefix}-{$year}-{$month}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastTrip) {
            $lastNumber = (int) substr($lastTrip->trip_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}-{$year}-{$month}-{$newNumber}";
    }

    // Calculate freight automatically
    public function calculateFreight()
    {
        $this->freight = $this->kilometers * $this->rate_per_km;
        return $this->freight;
    }

    // Scope for pending trips
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for completed trips
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Scope for billed trips
    public function scopeBilled($query)
    {
        return $query->where('status', 'billed');
    }

    // Scope for specific billing month
    public function scopeByBillingMonth($query, $month)
    {
        return $query->where('billing_month', $month);
    }

    // Scope for date range filter
    public function scopeByDateRange($query, $dateFrom = null, $dateTo = null)
    {
        if ($dateFrom) {
            $query->where('trip_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('trip_date', '<=', $dateTo);
        }
        return $query;
    }
}