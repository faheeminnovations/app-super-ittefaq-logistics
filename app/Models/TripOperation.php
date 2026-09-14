<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\CurrencyHelper;

class TripOperation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'trip_number',
        'trip_date',
        'vehicle_number',
        'gp_number',
        'delivery_point',
        'vehicle_category',
        'vehicle_type',
        'kilometers',
        'rate_per_km',
        'freight',
        'fuel_type',
        'fuel',
        'fuel_payment_type',
        'fuel_payment_amount',
        'driver_name',
        'driver_id',
        'load_id',
        'freight_bill_no',
        'billing_month',
        'billing_year',
        'billing_month_number',
        'sr',
        'business_category',
        'customer_name',
        'customer_id',
        'warehouse_location',
        'gl_number',
        'business_area',
        'warehouse_id',
        'loading_point',
        'unloading_point',
        'phone_number',
        'quantity',
        'guarantor',
        'rent_paid',
        'payment_details',
        'receiving_details',
        'expenses',
        'initial_amount',
        'amount_changed',
        'invoice_id',
        'invoice_number',
        'status',
        'trip_status',
        'notes',
        'vehicle_id',
        'wizard_steps_completed',
        'current_wizard_step',
    ];

    protected $casts = [
        'trip_date' => 'date',
        'kilometers' => 'decimal:2',
        'rate_per_km' => 'decimal:2',
        'freight' => 'decimal:2',
        'fuel_payment_amount' => 'decimal:2',
        'rent_paid' => 'decimal:2',
        'expenses' => 'decimal:2',
        'initial_amount' => 'decimal:2',
        'amount_changed' => 'decimal:2',
        'billing_year' => 'integer',
        'billing_month_number' => 'integer',
        'quantity' => 'integer',
        'sr' => 'integer',
        'current_wizard_step' => 'integer',
        'wizard_steps_completed' => 'array',
    ];

    // Relationships
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Accessors for formatted currency values
    public function getFormattedFreightAttribute()
    {
        return CurrencyHelper::formatCurrency($this->freight);
    }

    public function getFormattedRatePerKmAttribute()
    {
        return CurrencyHelper::formatCurrency($this->rate_per_km);
    }

    // Auto-generate trip number
    public static function generateTripNumber()
    {
        $prefix = 'TRP';
        $year = date('Y');
        $month = date('m');

        // Get the maximum existing trip number for this month (including soft-deleted due to unique constraint)
        $maxTripNumber = self::withTrashed()
            ->where('trip_number', 'like', "{$prefix}-{$year}-{$month}-%")
            ->max('trip_number');

        if ($maxTripNumber) {
            $lastNumber = (int) substr($maxTripNumber, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $tripNumber = "{$prefix}-{$year}-{$month}-{$newNumber}";

        // Ensure unique trip number with fallback to timestamp
        $maxAttempts = 50;
        $attempts = 0;

        while (self::withTrashed()->where('trip_number', $tripNumber)->exists() && $attempts < $maxAttempts) {
            $attempts++;
            $lastNumber = (int) substr($tripNumber, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $tripNumber = "{$prefix}-{$year}-{$month}-{$newNumber}";
        }

        // If still not unique after many attempts, use timestamp with random
        if (self::withTrashed()->where('trip_number', $tripNumber)->exists()) {
            $timestamp = date('His') . rand(100, 999);
            $tripNumber = "{$prefix}-{$year}-{$month}-{$timestamp}";
        }

        return $tripNumber;
    }

    // Calculate freight automatically
    public function calculateFreight()
    {
        $this->freight = $this->kilometers * $this->rate_per_km;
        return $this->freight;
    }

    // Scope for pending trips
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for in_progress trips
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    // Scope for completed trips
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Scope for billed trips
    public function scopeBilled($query)
    {
        return $query->where('status', 'billed');
    }

    // Scope for cancelled trips
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Scope for specific billing month
    public function scopeByBillingMonth($query, $month)
    {
        return $query->where('billing_month', $month);
    }

    // Scope for specific warehouse location
    public function scopeByWarehouseLocation($query, $location)
    {
        return $query->where('warehouse_location', $location);
    }

    // Scope for specific business category
    public function scopeByBusinessCategory($query, $category)
    {
        return $query->where('business_category', $category);
    }

    // Scope for specific wizard step
    public function scopeByWizardStep($query, $step)
    {
        return $query->where('current_wizard_step', $step);
    }

    // Mark wizard step as completed
    public function markWizardStepCompleted($step)
    {
        $completedSteps = $this->wizard_steps_completed ?? [];
        if (!in_array($step, $completedSteps)) {
            $completedSteps[] = $step;
            $this->wizard_steps_completed = $completedSteps;
        }

        // Always move to the next step (step + 1), but cap at 5
        $nextStep = $step + 1;
        if ($nextStep > 5) {
            $nextStep = 5; // Stay at step 5 when complete
        }

        $this->current_wizard_step = $nextStep;
        $this->save();
    }

    // Get wizard progress percentage
    public function getWizardProgressAttribute()
    {
        $totalSteps = 5; // Define total wizard steps
        $completedSteps = count($this->wizard_steps_completed ?? []);

        // Also consider current_wizard_step for better progress tracking
        $currentStep = $this->current_wizard_step ?? 1;
        if ($currentStep > 5) {
            $currentStep = 5; // Cap at 5 since that's the last step
        }

        // Use the maximum of completed steps or current step - 1 for progress
        $progressStep = max($completedSteps, $currentStep - 1);
        return ($progressStep / $totalSteps) * 100;
    }
}
