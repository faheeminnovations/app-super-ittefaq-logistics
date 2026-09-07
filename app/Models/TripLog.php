<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'sr',
        'date',
        'vehicle_no',
        'gp_number',
        'delivery_point',
        'vehicle_category',
        'business_category',
        'km',
        'rate',
        'frt',
        'fuel',
        'driver_name',
        'load_id',
        'freight_bill_no',
        'billing_month',
        'billing_year',
        'billing_month_number',
        'cluster',
        'customer_name',
        'loading_point',
        'unloading_point',
        'phone_number',
        'quantity',
        'guarantor',
        'rent_paid',
        'payment_details',
        'receiving_details',
        'status',
        'expenses',
    ];

    protected $casts = [
        'date' => 'date',
        'km' => 'decimal:2',
        'rate' => 'decimal:2',
        'frt' => 'decimal:2',
        'billing_year' => 'integer',
        'billing_month_number' => 'integer',
    ];

    /**
     * Auto-calculate FRT (Freight) when KM and Rate are set
     */
    public function setKmAttribute($value)
    {
        $this->attributes['km'] = $value;
        $this->calculateFrt();
    }

    public function setRateAttribute($value)
    {
        $this->attributes['rate'] = $value;
        $this->calculateFrt();
    }

    protected function calculateFrt()
    {
        if (isset($this->attributes['km']) && isset($this->attributes['rate'])) {
            $this->attributes['frt'] = $this->attributes['km'] * $this->attributes['rate'];
        }
    }

    /**
     * Relationship with Invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Scope for filtering by billing month
     */
    public function scopeByMonth($query, $month, $year)
    {
        return $query->where('billing_month', $month)
                    ->where('billing_year', $year);
    }

    /**
     * Scope for filtering by vehicle
     */
    public function scopeByVehicle($query, $vehicleNo)
    {
        return $query->where('vehicle_no', $vehicleNo);
    }

    /**
     * Scope for filtering by driver
     */
    public function scopeByDriver($query, $driverName)
    {
        return $query->where('driver_name', $driverName);
    }

    /**
     * Get total freight for a query
     */
    public function scopeTotalFreight($query)
    {
        // Return the query builder, not the sum
        return $query;
    }

    /**
     * Get total KM for a query
     */
    public function scopeTotalKm($query)
    {
        // Return the query builder, not the sum
        return $query;
    }
}