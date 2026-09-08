<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Helpers\CurrencyHelper;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'contact_email',
        'contact_person',
        'contact_phone',
        'city',
        'credit_limit',
        'balance',
        'status',
        'phone',
        'address',
        'guarantor',
        'business_type',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    /**
     * Get formatted credit limit with currency
     */
    public function getFormattedCreditLimitAttribute()
    {
        return CurrencyHelper::formatCurrency($this->credit_limit);
    }

    /**
     * Get formatted balance with currency
     */
    public function getFormattedBalanceAttribute()
    {
        return CurrencyHelper::formatCurrency($this->balance);
    }
}
