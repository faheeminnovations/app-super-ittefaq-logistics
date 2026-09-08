<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'reg_no',
        'type',
        'make_model',
        'year',
        'fitness_certificate_expiry',
        'status',
        'work_type',
        'fuel_capacity',
        'vin',
        'notes',
        'vehicle_category',
        'route_permit',
        'token_tax',
        'insurance',
    ];

    protected $casts = [
        'fitness_certificate_expiry' => 'date',
        'route_permit' => 'date',
        'token_tax' => 'date',
        'insurance' => 'date',
        'fuel_capacity' => 'decimal:2',
    ];

    public function getPlateNumberAttribute()
    {
        return $this->reg_no;
    }

    /**
     * Relationship with trips
     */
    public function trips(): HasMany
    {
        return $this->hasMany(\App\Models\Trip::class);
    }

    /**
     * Relationship with maintenance records
     */
    public function maintenance(): HasMany
    {
        return $this->hasMany(\App\Models\Maintenance::class);
    }

    /**
     * Relationship with trip logs
     */
    public function tripLogs(): HasMany
    {
        return $this->hasMany(\App\Models\TripLog::class, 'vehicle_no', 'reg_no');
    }

    /**
     * Scope for active vehicles
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope for vehicles by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('vehicle_category', $category);
    }
}
