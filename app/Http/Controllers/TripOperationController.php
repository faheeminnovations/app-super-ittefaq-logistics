<?php

namespace App\Http\Controllers;

use App\Models\TripOperation;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Customer;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TripOperationController extends Controller
{
    /**
     * Display a listing of trip operations.
     */
    public function index(Request $request)
    {
        $query = TripOperation::query();

        // Filter by month if provided
        if ($request->filled('month') && $request->filled('year')) {
            $monthNumber = $request->month;
            $year = $request->year;
            $monthName = Carbon::create()->month($monthNumber)->format('F');
            $billingMonth = $monthName . '-' . $year;
            $query->where('billing_month', $billingMonth)
                  ->where('billing_year', $year)
                  ->where('billing_month_number', $monthNumber);
        }

        // Filter by warehouse location if provided
        if ($request->filled('warehouse_location')) {
            $query->where('warehouse_location', $request->warehouse_location);
        }

        // Filter by business category if provided
        if ($request->filled('business_category')) {
            $query->where('business_category', $request->business_category);
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tripOperations = $query->with(['vehicle', 'driver', 'customer', 'warehouse'])
            ->orderBy('trip_date', 'desc')
            ->paginate(50);

        // Get all trip operations for totals calculation
        $allOperations = $query->get();
        $totalKm = $allOperations->sum('kilometers');
        $totalFreight = $allOperations->sum('freight');

        // Get filter options
        $vehicles = Vehicle::active()->pluck('reg_no', 'reg_no');
        $drivers = Driver::active()->pluck('name', 'name');
        $customers = Customer::all();
        $warehouses = Warehouse::active()->get();

        // Default categories
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

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create()->month($i)->format('F');
        }

        return view('trip-operations.index', compact(
            'tripOperations',
            'vehicles',
            'drivers',
            'customers',
            'warehouses',
            'categories',
            'months',
            'totalKm',
            'totalFreight'
        ));
    }

    /**
     * Show the form for creating a new trip operation (wizard start).
     */
    public function create()
    {
        $vehicles = Vehicle::active()->pluck('reg_no', 'reg_no');
        $drivers = Driver::active()->pluck('name', 'name');
        $customers = Customer::all();
        $warehouses = Warehouse::active()->get();
        $vehicleCategories = ['1T', '2T', '4T', '8T'];

        // Default categories
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

        $tripStatuses = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];

        return view('trip-operations.create', compact(
            'vehicles',
            'drivers',
            'customers',
            'warehouses',
            'vehicleCategories',
            'categories',
            'tripStatuses'
        ));
    }

    /**
     * Store wizard step data.
     */
    public function storeWizardStep(Request $request)
    {
        try {
            $step = $request->input('step');
            $tripId = $request->input('trip_id');

            // Validate based on current step
            $validationRules = $this->getStepValidationRules($step);
            $validated = $request->validate($validationRules);

        // Auto-calculate billing information
        if (isset($validated['trip_date'])) {
            $date = Carbon::parse($validated['trip_date']);
            $validated['billing_month'] = $date->format('F-Y');
            $validated['billing_year'] = $date->year;
            $validated['billing_month_number'] = $date->month;
        }

        // Auto-calculate freight
        if (isset($validated['kilometers']) && isset($validated['rate_per_km'])) {
            $validated['freight'] = $validated['kilometers'] * $validated['rate_per_km'];
        }

        // Generate trip number if it's a new trip
        if (!$tripId) {
            $validated['trip_number'] = TripOperation::generateTripNumber();
            $validated['current_wizard_step'] = 1;
            $validated['wizard_steps_completed'] = [];
            $tripOperation = TripOperation::create($validated);
        } else {
            $tripOperation = TripOperation::findOrFail($tripId);
            $tripOperation->update($validated);
        }

        // Move to next step
        $nextStep = $step + 1;
        if ($nextStep > 5) {
            $nextStep = 5; // Stay at step 5
        }

        $tripOperation->current_wizard_step = $nextStep;

        // Mark step as completed
        $completedSteps = $tripOperation->wizard_steps_completed ?? [];
        if (!in_array($step, $completedSteps)) {
            $completedSteps[] = $step;
        }
        $tripOperation->wizard_steps_completed = $completedSteps;
        $tripOperation->save();

        return response()->json([
            'success' => true,
            'trip_id' => $tripOperation->id,
            'current_step' => $nextStep,
            'is_complete' => $step === 5,
            'message' => 'Step saved successfully'
        ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get validation rules for each wizard step.
     */
    private function getStepValidationRules($step)
    {
        switch ($step) {
            case 1: // Basic Information
                return [
                    'trip_date' => 'required|date',
                    'vehicle_number' => 'nullable|string|max:50',
                    'driver_name' => 'nullable|string|max:255',
                    'delivery_point' => 'required|string|max:255',
                    'gp_number' => 'nullable|string|max:50',
                ];

            case 2: // Distance and Rate
                return [
                    'vehicle_category' => 'nullable|string|max:10',
                    'vehicle_type' => 'nullable|string|max:50',
                    'kilometers' => 'required|numeric|min:0|max:999999999.99',
                    'rate_per_km' => 'required|numeric|min:0|max:999999999.99',
                ];

            case 3: // Fuel and Expenses
                return [
                    'fuel_type' => 'nullable|string|max:50',
                    'fuel' => 'nullable|string|max:255',
                    'fuel_payment_type' => 'nullable|in:credit,cash',
                    'fuel_payment_amount' => 'nullable|numeric|min:0|max:999999999.99',
                    'expenses' => 'nullable|numeric|min:0|max:999999999.99',
                ];

            case 4: // Business Details
                return [
                    'business_category' => 'required|string|max:255',
                    'customer_name' => 'nullable|string|max:255',
                    'warehouse_location' => 'nullable|string|max:100',
                    'gl_number' => 'nullable|string|max:50',
                    'load_id' => 'nullable|string|max:50',
                    'freight_bill_no' => 'nullable|string|max:50',
                ];

            case 5: // Additional Details (Final Step)
                return [
                    'loading_point' => 'nullable|string|max:255',
                    'unloading_point' => 'nullable|string|max:255',
                    'phone_number' => 'nullable|string|max:50',
                    'quantity' => 'nullable|integer|min:0',
                    'guarantor' => 'nullable|string|max:255',
                    'rent_paid' => 'nullable|numeric|min:0|max:999999999.99',
                    'payment_details' => 'nullable|string',
                    'receiving_details' => 'nullable|string',
                    'initial_amount' => 'nullable|numeric|min:0|max:999999999.99',
                    'amount_changed' => 'nullable|numeric|min:0|max:999999999.99',
                    'notes' => 'nullable|string',
                    'status' => 'required|in:pending,in_progress,completed,billed,cancelled',
                ];

            default:
                return [];
        }
    }

    /**
     * Display the specified trip operation.
     */
    public function show($id)
    {
        $tripOperation = TripOperation::with(['vehicle', 'driver', 'customer', 'warehouse', 'invoice'])
            ->findOrFail($id);

        return view('trip-operations.show', compact('tripOperation'));
    }

    /**
     * Show the form for editing the specified trip operation.
     */
    public function edit($id)
    {
        $tripOperation = TripOperation::with(['vehicle', 'driver', 'customer', 'warehouse'])
            ->findOrFail($id);

        $vehicles = Vehicle::active()->pluck('reg_no', 'reg_no');
        $drivers = Driver::active()->pluck('name', 'name');
        $customers = Customer::all();
        $warehouses = Warehouse::active()->get();
        $vehicleCategories = ['1T', '2T', '4T', '8T'];

        // Default categories
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

        $tripStatuses = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];

        return view('trip-operations.edit', compact(
            'tripOperation',
            'vehicles',
            'drivers',
            'customers',
            'warehouses',
            'vehicleCategories',
            'categories',
            'tripStatuses'
        ));
    }

    /**
     * Update the specified trip operation.
     */
    public function update(Request $request, $id)
    {
        $tripOperation = TripOperation::findOrFail($id);

        $validated = $request->validate([
            'trip_date' => 'required|date',
            'vehicle_number' => 'required|string|max:50',
            'driver_name' => 'nullable|string|max:255',
            'delivery_point' => 'required|string|max:255',
            'gp_number' => 'nullable|string|max:50',
            'vehicle_category' => 'nullable|string|max:10',
            'vehicle_type' => 'nullable|string|max:50',
            'kilometers' => 'required|numeric|min:0',
            'rate_per_km' => 'required|numeric|min:0',
            'fuel_type' => 'nullable|string|max:50',
            'fuel' => 'nullable|string|max:255',
            'fuel_payment_type' => 'nullable|in:credit,cash',
            'fuel_payment_amount' => 'nullable|numeric|min:0',
            'expenses' => 'nullable|numeric|min:0',
            'business_category' => 'required|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'warehouse_location' => 'nullable|string|max:100',
            'load_id' => 'nullable|string|max:50',
            'freight_bill_no' => 'nullable|string|max:50',
            'loading_point' => 'nullable|string|max:255',
            'unloading_point' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'quantity' => 'nullable|integer|min:0',
            'guarantor' => 'nullable|string|max:255',
            'rent_paid' => 'nullable|numeric|min:0',
            'payment_details' => 'nullable|string',
            'receiving_details' => 'nullable|string',
            'initial_amount' => 'nullable|numeric|min:0',
            'amount_changed' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,billed,cancelled',
        ]);

        // Auto-calculate billing information
        $date = Carbon::parse($validated['trip_date']);
        $validated['billing_month'] = $date->format('F-Y');
        $validated['billing_year'] = $date->year;
        $validated['billing_month_number'] = $date->month;

        // Auto-calculate freight
        $validated['freight'] = $validated['kilometers'] * $validated['rate_per_km'];

        $tripOperation->update($validated);

        return redirect()->route('trip-operations.index')
            ->with('success', 'Trip operation updated successfully.');
    }

    /**
     * Remove the specified trip operation.
     */
    public function destroy($id)
    {
        $tripOperation = TripOperation::findOrFail($id);

        // Check if trip operation is associated with an invoice
        if ($tripOperation->invoice()->exists()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete trip operation that is associated with an invoice.'], 400);
            }
            return redirect()->route('trip-operations.index')
                ->with('error', 'Cannot delete trip operation that is associated with an invoice.');
        }

        try {
            $tripOperation->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Trip operation deleted successfully.']);
            }

            return redirect()->route('trip-operations.index')
                ->with('success', 'Trip operation deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete trip operation due to database constraints.'], 400);
            }
            return redirect()->route('trip-operations.index')
                ->with('error', 'Cannot delete trip operation due to database constraints.');
        }
    }

    /**
     * Get trip operation data for wizard editing.
     */
    public function getWizardData($id)
    {
        $tripOperation = TripOperation::with(['vehicle', 'driver', 'customer', 'warehouse'])
            ->findOrFail($id);

        return response()->json([
            'trip' => $tripOperation,
            'success' => true
        ]);
    }

    /**
     * Continue wizard for existing trip.
     */
    public function continueWizard($id)
    {
        $tripOperation = TripOperation::findOrFail($id);

        // Ensure current wizard step is capped at 5
        if ($tripOperation->current_wizard_step > 5) {
            $tripOperation->current_wizard_step = 5;
            $tripOperation->save();
        }

        $vehicles = Vehicle::active()->pluck('reg_no', 'reg_no');
        $drivers = Driver::active()->pluck('name', 'name');
        $customers = Customer::all();
        $warehouses = Warehouse::active()->get();
        $vehicleCategories = ['1T', '2T', '4T', '8T'];

        // Default categories
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

        $tripStatuses = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];

        return view('trip-operations.create', compact(
            'tripOperation',
            'vehicles',
            'drivers',
            'customers',
            'warehouses',
            'vehicleCategories',
            'categories',
            'tripStatuses'
        ));
    }

    /**
     * Complete wizard and finalize trip operation.
     */
    public function completeWizard(Request $request, $id)
    {
        try {
            $tripOperation = TripOperation::findOrFail($id);

            // Mark all 5 steps as completed
            $tripOperation->wizard_steps_completed = [1, 2, 3, 4, 5];
            $tripOperation->current_wizard_step = 5; // Mark as completed at step 5
            $tripOperation->save();

            return response()->json([
                'success' => true,
                'message' => 'Trip operation completed successfully',
                'trip_id' => $tripOperation->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
