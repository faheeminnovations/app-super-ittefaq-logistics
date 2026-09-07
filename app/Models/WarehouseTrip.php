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
        'fuel_type',
        'driver_name',
        'load_id',
        'freight_bill_no',
        'billing_month',
        'warehouse_location',
        'gl_number',
        'business_area',
        'invoice_number',
        'warehouse_invoice_id',
        'notes',
        'status',
        'vehicle_id',
        'driver_id',
        'customer_id',
    ];

    protected $casts = [
        'trip_date' => 'date',
        'kilometers' => 'decimal:2',
        'rate_per_km' => 'decimal:2',
        'freight' => 'decimal:2',
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

    // Accessors for formatted currency values
    public function getFormattedFreightAttribute()
    {
        return CurrencyHelper::formatCurrency($this->freight);
    }

    public function getFormattedRatePerKmAttribute()
    {
        return CurrencyHelper::formatCurrency($this->rate_per_km);
    }

    // Auto-generate trip number
    public static function generateTripNumber()
    {
        $prefix = 'TRP';
        $year = date('Y');
        $month = date('m');
        
        $lastTrip = self::where('trip_number', 'like', "{$prefix}-{$year}-{$month}-%")
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
}