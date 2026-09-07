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
        'cpc_expiry',
        'phone',
        'status',
        'address',
        'licence_expiry',
    ];

    protected $casts = [
        'cpc_expiry' => 'date',
        'licence_expiry' => 'date',
    ];

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
