<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = [
        'name',
        'licence_no',
        'category',
        'phone',
        'status',
        'address',
        'licence_expiry',
    ];

    protected $casts = [
        'licence_expiry' => 'date',
    ];

    /**
     * Relationship with trips
     */
    public function trips(): HasMany
    {
        return $this->hasMany(\App\Models\Trip::class);
    }

    /**
     * Relationship with trip logs
     */
    public function tripLogs(): HasMany
    {
        return $this->hasMany(\App\Models\TripLog::class, 'driver_name', 'name');
    }

    /**
     * Scope for active drivers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'on_duty');
    }
}
