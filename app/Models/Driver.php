<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = [
        'name',
        'licence_no',
        'cnic',
        'driver_picture',
        'cnic_picture',
        'cnic_picture_back',
        'license_picture',
        'license_picture_back',
        'category',
        'phone',
        'status',
        'address',
        'licence_expiry',
    ];

    /**
     * Boot method to handle dynamic fillable fields based on database schema
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Remove fields that don't exist in database schema
            $columns = \Schema::getColumnListing('drivers');
            $model->attributes = array_intersect_key($model->attributes, array_flip($columns));
        });
    }

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
