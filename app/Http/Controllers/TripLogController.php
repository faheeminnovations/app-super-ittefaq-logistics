<?php

namespace App\Http\Controllers;

use App\Models\TripLog;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\CompanySettings;
use App\Models\MonthlyRate;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TripLogController extends Controller
{
    /**
     * Display a listing of trip logs with filtering
     */
    public function index(Request $request)
    {
        $query = TripLog::query();

        // Filter by month if provided
        if ($request->filled('month') && $request->filled('year')) {
            $monthNumber = $request->month;
            $year = $request->year;
            // Convert month number to month name and format to match billing_month format (e.g., "November-2025")
            $monthName = Carbon::create()->month($monthNumber)->format('F');
            $billingMonth = $monthName . '-' . $year;
            $query->where('billing_month', $billingMonth)
                  ->where('billing_year', $year)
                  ->where('billing_month_number', $monthNumber);
        }

        // Filter by business category if provided
        if ($request->filled('category')) {
            $query->where('business_category', $request->category);
        }

        // Filter by invoice if provided
        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        // Filter by vehicle if provided
        if ($request->filled('vehicle')) {
            $query->where('vehicle_no', $request->vehicle);
        }

        // Filter by driver if provided
        if ($request->filled('driver')) {
            $query->where('driver_name', $request->driver);
        }

        $tripLogs = $query->orderBy('date', 'desc')->paginate(50);

        // Check for incomplete data (missing rates or FRT)
        $incompleteTrips = TripLog::whereNull('rate')
            ->orWhereNull('frt')
            ->orWhere('rate', 0)
            ->count();

        // Get filter options
        $vehicles = Vehicle::active()->pluck('reg_no', 'reg_no');
        $drivers = Driver::active()->pluck('name', 'name');
        
        // Get unique business types from customers with LOGISTICS & TRANSPORT
        $businessTypes = Customer::where('business_type', 'LOGISTICS & TRANSPORT')
            ->pluck('business_type')
            ->unique()
            ->toArray();
        
        // Default categories if no LOGISTICS & TRANSPORT customers found
        $categories = [
            'Open Market Work' => 'Open Market Work',
            'Buyer Supply Chain' => 'Buyer Supply Chain',
            'Buyer Breading' => 'Buyer Breading',
            'Buyer Seed Supply' => 'Buyer Seed Supply',
            'Buyer Marketing Development' => 'Buyer Marketing Development',
            'Buyer S.P.R' => 'Buyer S.P.R',
            'Syngenta' => 'Syngenta',
            'Syngenta Breading' => 'Syngenta Breading'
        ];
        
        // Add LOGISTICS & TRANSPORT categories if found
        if (!empty($businessTypes)) {
            foreach ($businessTypes as $businessType) {
                $categories[$businessType] = $businessType;
            }
        }

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create()->month($i)->format('F');
        }

        return view('transport.trip-logs.index', compact(
            'tripLogs',
            'vehicles',
            'drivers',
            'categories',
            'months',
            'incompleteTrips'
        ));
    }

    /**
     * Show the form for creating a new trip log
     */
    public function create()
    {
        $vehicles = Vehicle::active()->pluck('reg_no', 'reg_no');
        $drivers = Driver::active()->pluck('name', 'name');
        $vehicleCategories = ['1T', '2T', '4T', '8T'];
        
        // Get unique business types from customers with LOGISTICS & TRANSPORT
        $businessTypes = Customer::where('business_type', 'LOGISTICS & TRANSPORT')
            ->pluck('business_type')
            ->unique()
            ->toArray();
        
        // Default categories if no LOGISTICS & TRANSPORT customers found
        $categories = [
            'Open Market Work' => 'Open Market Work',
            'Buyer Supply Chain' => 'Buyer Supply Chain',
            'Buyer Breading' => 'Buyer Breading',
            'Buyer Seed Supply' => 'Buyer Seed Supply',
            'Buyer Marketing Development' => 'Buyer Marketing Development',
            'Buyer S.P.R' => 'Buyer S.P.R',
            'Syngenta' => 'Syngenta',
            'Syngenta Breading' => 'Syngenta Breading'
        ];
        
        // Add LOGISTICS & TRANSPORT categories if found
        if (!empty($businessTypes)) {
            foreach ($businessTypes as $businessType) {
                $categories[$businessType] = $businessType;
            }
        }

        $tripStatuses = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];

        // Get customers grouped by business type
        $customers = \App\Models\Customer::all()->groupBy('business_type');

        return view('transport.trip-logs.create', compact(
            'vehicles',
            'drivers',
            'vehicleCategories',
            'categories',
            'customers',
            'tripStatuses'
        ));
    }

    /**
     * Store a newly created trip log
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'date' => 'required|date',
            'vehicle_no' => 'required|string',
            'gp_number' => 'nullable|string',
            'delivery_point' => 'nullable|string',
            'vehicle_category' => 'nullable|in:1T,2T,4T,8T',
            'business_category' => 'required|string',
            'km' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'fuel' => 'nullable|string',
            'driver_name' => 'nullable|string',
            'load_id' => 'nullable|string',
            'freight_bill_no' => 'nullable|string',
            // Additional fields for different customer types
            'cluster' => 'nullable|string',
            'customer_name' => 'nullable|string',
            'loading_point' => 'nullable|string',
            'unloading_point' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'quantity' => 'nullable|integer|min:0',
            'guarantor' => 'nullable|string',
            'rent_paid' => 'nullable|numeric|min:0',
            'payment_details' => 'nullable|string',
            'receiving_details' => 'nullable|string',
            'trip_status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'expenses' => 'nullable|numeric|min:0',
        ]);

        // Auto-calculate billing month and year
        $date = Carbon::parse($validated['date']);
        $validated['billing_month'] = $date->format('F-Y');
        $validated['billing_year'] = $date->year;
        $validated['billing_month_number'] = $date->month;

        // Auto-calculate FRT only if km and rate are provided
        if (isset($validated['km']) && isset($validated['rate']) && $validated['km'] && $validated['rate']) {
            $validated['frt'] = $validated['km'] * $validated['rate'];
        } else {
            $validated['frt'] = 0;
        }

        // Auto-generate serial number for the month with uniqueness check
        $lastSr = TripLog::where('billing_month', $validated['billing_month'])
            ->where('billing_year', $validated['billing_year'])
            ->max('sr');
        $validated['sr'] = $lastSr ? $lastSr + 1 : 1;
        
        // Ensure the generated SR is unique
        while (TripLog::where('sr', $validated['sr'])
            ->where('billing_month', $validated['billing_month'])
            ->where('billing_year', $validated['billing_year'])
            ->exists()) {
            $validated['sr']++;
        }

        TripLog::create($validated);

        return redirect()->route('trip-logs.index')
            ->with('success', 'Trip log created successfully.');
    }

    /**
     * Display the specified trip log
     */
    public function show(TripLog $tripLog)
    {
        return view('transport.trip-logs.show', compact('tripLog'));
    }

    /**
     * Show the form for editing the specified trip log
     */
    public function edit(TripLog $tripLog)
    {
        $vehicles = Vehicle::active()->pluck('reg_no', 'reg_no');
        $drivers = Driver::active()->pluck('name', 'name');
        $vehicleCategories = ['1T', '2T', '4T', '8T'];
        
        // Get unique business types from customers with LOGISTICS & TRANSPORT
        $businessTypes = Customer::where('business_type', 'LOGISTICS & TRANSPORT')
            ->pluck('business_type')
            ->unique()
            ->toArray();
        
        // Default categories if no LOGISTICS & TRANSPORT customers found
        $categories = [
            'Open Market Work' => 'Open Market Work',
            'Buyer Supply Chain' => 'Buyer Supply Chain',
            'Buyer Breading' => 'Buyer Breading',
            'Buyer Seed Supply' => 'Buyer Seed Supply',
            'Buyer Marketing Development' => 'Buyer Marketing Development',
            'Buyer S.P.R' => 'Buyer S.P.R',
            'Syngenta' => 'Syngenta',
            'Syngenta Breading' => 'Syngenta Breading'
        ];
        
        // Add LOGISTICS & TRANSPORT categories if found
        if (!empty($businessTypes)) {
            foreach ($businessTypes as $businessType) {
                $categories[$businessType] = $businessType;
            }
        }

        $tripStatuses = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];

        // Get customers grouped by business type
        $customers = \App\Models\Customer::all()->groupBy('business_type');

        return view('transport.trip-logs.edit', compact(
            'tripLog',
            'vehicles',
            'drivers',
            'vehicleCategories',
            'categories',
            'customers',
            'tripStatuses'
        ));
    }

    /**
     * Update the specified trip log
     */
    public function update(Request $request, TripLog $tripLog)
    {
        $validated = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'date' => 'required|date',
            'vehicle_no' => 'required|string',
            'gp_number' => 'nullable|string',
            'delivery_point' => 'nullable|string',
            'vehicle_category' => 'nullable|in:1T,2T,4T,8T',
            'business_category' => 'required|string',
            'km' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'fuel' => 'nullable|string',
            'driver_name' => 'nullable|string',
            'load_id' => 'nullable|string',
            'freight_bill_no' => 'nullable|string',
            // Additional fields for different customer types
            'cluster' => 'nullable|string',
            'customer_name' => 'nullable|string',
            'loading_point' => 'nullable|string',
            'unloading_point' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'quantity' => 'nullable|integer|min:0',
            'guarantor' => 'nullable|string',
            'rent_paid' => 'nullable|numeric|min:0',
            'payment_details' => 'nullable|string',
            'receiving_details' => 'nullable|string',
            'trip_status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'expenses' => 'nullable|numeric|min:0',
        ]);

        // Auto-calculate billing month and year
        $date = Carbon::parse($validated['date']);
        $validated['billing_month'] = $date->format('F-Y');
        $validated['billing_year'] = $date->year;
        $validated['billing_month_number'] = $date->month;

        // Auto-calculate FRT only if km and rate are provided
        if (isset($validated['km']) && isset($validated['rate']) && $validated['km'] && $validated['rate']) {
            $validated['frt'] = $validated['km'] * $validated['rate'];
        } else {
            $validated['frt'] = 0;
        }

        // Auto-generate serial number for the month if month/year changed
        $oldBillingMonth = $tripLog->billing_month;
        $oldBillingYear = $tripLog->billing_year;
        
        if ($oldBillingMonth != $validated['billing_month'] || $oldBillingYear != $validated['billing_year']) {
            $lastSr = TripLog::where('billing_month', $validated['billing_month'])
                ->where('billing_year', $validated['billing_year'])
                ->max('sr');
            $validated['sr'] = $lastSr ? $lastSr + 1 : 1;
            
            // Ensure the generated SR is unique
            while (TripLog::where('sr', $validated['sr'])
                ->where('billing_month', $validated['billing_month'])
                ->where('billing_year', $validated['billing_year'])
                ->exists()) {
                $validated['sr']++;
            }
        }

        $tripLog->update($validated);

        return redirect()->route('trip-logs.index')
            ->with('success', 'Trip log updated successfully.');
    }

    /**
     * Remove the specified trip log
     */
    public function destroy(TripLog $tripLog)
    {
        // Check if trip log is associated with an invoice
        if ($tripLog->invoice()->exists()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete trip log that is associated with an invoice.'], 400);
            }
            return redirect()->route('trip-logs.index')
                ->with('error', 'Cannot delete trip log that is associated with an invoice.');
        }

        try {
            $tripLog->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Trip log deleted successfully.']);
            }

            return redirect()->route('trip-logs.index')
                ->with('success', 'Trip log deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete trip log due to database constraints.'], 400);
            }
            return redirect()->route('trip-logs.index')
                ->with('error', 'Cannot delete trip log due to database constraints.');
        }
    }

    /**
     * Print trip log
     */
    public function print(TripLog $tripLog)
    {
        return view('transport.trip-logs.print', compact('tripLog'));
    }

    /**
     * Display monthly view with totals
     */
    public function monthlyView($month, $year)
    {
        $tripLogs = TripLog::byMonth($month, $year)
            ->orderBy('sr')
            ->get();

        $totalFreight = $tripLogs->sum('frt');
        $totalKm = $tripLogs->sum('km');
        $totalTrips = $tripLogs->count();

        // Get company settings for invoice header
        $companySettings = CompanySettings::first();

        return view('transport.trip-logs.monthly-view', compact(
            'tripLogs',
            'month',
            'year',
            'totalFreight',
            'totalKm',
            'totalTrips',
            'companySettings'
        ));
    }

    /**
     * Get rate for vehicle category and date
     */
    public function getRate(Request $request)
    {
        $category = $request->query('category');
        $date = $request->query('date');

        if (!$category || !$date) {
            return response()->json(['rate' => 0]);
        }

        $carbonDate = Carbon::parse($date);
        $monthName = $carbonDate->format('F');
        $year = $carbonDate->year;
        $monthNumber = $carbonDate->month;
        $billingMonth = $monthName . '-' . $year;

        // Try to find rate by vehicle category first
        $monthlyRate = MonthlyRate::where('vehicle_category', $category)
            ->where('billing_month', $billingMonth)
            ->where('billing_year', $year)
            ->where('billing_month_number', $monthNumber)
            ->first();

        $rate = $monthlyRate ? $monthlyRate->rate_per_km : 0;

        return response()->json(['rate' => $rate]);
    }
}