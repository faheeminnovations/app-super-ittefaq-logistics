<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CurrencyHelper;

class TripClaim extends Model
{
    protected $fillable = [
        'trip_date',
        'vehicle_number',
        'route_from',
        'route_to',
        'agreed_amount',
        'return_date',
        'claimed_amount',
        'claim_details',
        'status',
        'billing_month',
    ];

    protected $casts = [
        'trip_date' => 'date',
        'return_date' => 'date',
        'agreed_amount' => 'decimal:2',
        'claimed_amount' => 'decimal:2',
    ];

    public function getFormattedTripDateAttribute()
    {
        return $this->trip_date ? $this->trip_date->format('d-M-y') : '-';
    }

    public function getFormattedReturnDateAttribute()
    {
        return $this->return_date ? $this->return_date->format('d-M-y') : '-';
    }

    public function getFormattedAgreedAmountAttribute()
    {
        return CurrencyHelper::formatAsPKRLower($this->agreed_amount ?? 0);
    }

    public function getFormattedClaimedAmountAttribute()
    {
        return CurrencyHelper::formatAsPKRLower($this->claimed_amount ?? 0);
    }

    public function scopeByMonth($query, $month)
    {
        return $query->where('billing_month', $month);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByVehicle($query, $vehicleNumber)
    {
        return $query->where('vehicle_number', $vehicleNumber);
    }
}
