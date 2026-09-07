<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySettings extends Model
{
    protected $fillable = [
        'company_name',
        'address',
        'contact_phone',
        'contact_email',
        'ntn',
        'vendor_code',
        'current_invoice_number',
    ];

    protected $casts = [
        'current_invoice_number' => 'integer',
    ];

    /**
     * Get the current settings (singleton pattern)
     */
    public static function getCurrent()
    {
        return self::firstOrCreate([], [
            'company_name' => 'SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY',
            'address' => 'Rizvi Chowk, Bypass Okara Road Depalpur',
            'contact_phone' => '0300-6967450',
            'contact_email' => 'zahidafzal5152@gmail.com',
            'ntn' => '4252472-5',
            'vendor_code' => '0006781511',
            'current_invoice_number' => 1,
        ]);
    }

    /**
     * Increment invoice number
     */
    public function incrementInvoiceNumber()
    {
        $this->increment('current_invoice_number');
        return $this->current_invoice_number;
    }
}