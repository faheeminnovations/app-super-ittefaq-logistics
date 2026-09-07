<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CurrencyHelper;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'billing_month',
        'billing_year',
        'billing_month_number',
        'service_provider_name',
        'service_provider_address',
        'service_provider_ntn',
        'service_provider_strn',
        'client_name',
        'client_address',
        'client_ntn',
        'client_strn',
        'warehouse',
        'gl_number',
        'business_area',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'amount_in_words',
        'status',
        'notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'invoice_date' => 'date',
        'verified_at' => 'datetime',
        'billing_year' => 'integer',
        'billing_month_number' => 'integer',
    ];

    /**
     * Relationship with Trip Logs
     */
    public function tripLogs()
    {
        return $this->hasMany(TripLog::class);
    }

    /**
     * Get formatted subtotal with currency
     */
    public function getFormattedSubtotalAttribute()
    {
        return CurrencyHelper::formatCurrency($this->subtotal);
    }

    /**
     * Get formatted tax amount with currency
     */
    public function getFormattedTaxAmountAttribute()
    {
        return CurrencyHelper::formatCurrency($this->tax_amount);
    }

    /**
     * Get formatted total amount with currency
     */
    public function getFormattedTotalAmountAttribute()
    {
        return CurrencyHelper::formatCurrency($this->total_amount);
    }

    /**
     * Calculate invoice totals from trip logs
     */
    public function calculateTotals()
    {
        $this->subtotal = $this->tripLogs()->sum('frt');
        $this->tax_amount = $this->subtotal * ($this->tax_rate / 100);
        $this->total_amount = $this->subtotal + $this->tax_amount;
        $this->save();
    }

    /**
     * Mark invoice as verified
     */
    public function markAsVerified($verifiedBy)
    {
        $this->verified_by = $verifiedBy;
        $this->verified_at = now();
        $this->status = 'sent';
        $this->save();
    }
}
