<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CurrencyHelper;

class WarehouseInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'billing_month',
        'billing_year',
        'billing_month_number',
        'warehouse_location',
        'gl_number',
        'business_area',
        'service_provider_name',
        'service_provider_address',
        'service_provider_ntn',
        'client_name',
        'client_address',
        'client_ntn',
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
     * Relationship with Warehouse Trips
     */
    public function warehouseTrips()
    {
        return $this->hasMany(WarehouseTrip::class);
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
     * Calculate invoice totals from warehouse trips
     */
    public function calculateTotals()
    {
        $this->subtotal = $this->warehouseTrips()->sum('freight');
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

    /**
     * Auto-generate invoice number
     */
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        
        $lastInvoice = self::where('invoice_number', 'like', "{$prefix}-{$year}-{$month}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}-{$year}-{$month}-{$newNumber}";
    }
}
