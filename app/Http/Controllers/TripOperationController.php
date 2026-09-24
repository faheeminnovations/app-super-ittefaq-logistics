<?php

namespace App\Http\Controllers;

use App\Models\TripOperation;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\TripExpenseEntry;
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
        if ($request->filled('month')) {
            $monthNumber = $request->month;
            $query->where('billing_month_number', $monthNumber);
        }

        // Filter by year if provided
        if ($request->filled('year')) {
            $year = $request->year;
            $query->where('billing_year', $year);
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

        // Clone query for totals calculation (before pagination)
        $totalsQuery = clone $query;

        $tripOperations = $query->with(['vehicle', 'driver', 'customer', 'warehouse'])
            ->orderBy('trip_date', 'desc')
            ->paginate(50);

        // Get totals from the cloned query
        $allOperations = $totalsQuery->get();
        $totalKm = $allOperations->sum('kilometers');
        $totalFreight = $allOperations->sum('freight');
        $totalIncome = $allOperations->sum('total_income') ?? $allOperations->sum('freight');
        $totalExpense = $allOperations->sum('total_expense') ?? 0;
        $totalNetAmount = $allOperations->sum('net_amount') ?? ($totalIncome - $totalExpense);

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
            'Syngenta' => 'Syngenta'
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
            'totalFreight',
            'totalIncome',
            'totalExpense',
            'totalNetAmount'
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
            'Syngenta' => 'Syngenta'
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

            // Log the incoming data for debugging
            \Log::info('Step ' . $step . ' data - START:', ['step' => $step, 'trip_id' => $tripId]);
            \Log::info('Step ' . $step . ' data:', $request->all());

            // Validate based on current step
            $validationRules = $this->getStepValidationRules($step);
            \Log::info('Step ' . $step . ' validation rules:', $validationRules);

            try {
                $validated = $request->validate($validationRules);
                \Log::info('Step ' . $step . ' validation passed');
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Step ' . $step . ' validation failed:', $e->errors());
                throw $e;
            }

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

        // Auto-calculate total income (freight)
        $validated['total_income'] = $validated['freight'] ?? 0;

        // Handle expense entries temporarily (will be saved after trip operation)
        $expenseEntries = $validated['expense_entries'] ?? [];
        $totalExpense = 0;

        // Filter out empty expense entries and calculate total expense
        $validExpenseEntries = [];
        foreach ($expenseEntries as $entry) {
            if (isset($entry['amount']) && is_numeric($entry['amount']) && floatval($entry['amount']) > 0) {
                $validExpenseEntries[] = $entry;
                $totalExpense += floatval($entry['amount']);
            }
        }
        $expenseEntries = $validExpenseEntries;

        // Auto-calculate total expense from expense entries
        $validated['total_expense'] = $totalExpense;

        // Calculate net amount
        $validated['net_amount'] = $validated['total_income'] - $validated['total_expense'];

        // Remove expense entries from validated data to avoid database issues
        unset($validated['expense_entries']);

        // Generate trip number if it's a new trip
        if (!$tripId) {
            $maxAttempts = 20;
            $attempts = 0;
            $tripOperation = null;

            while ($attempts < $maxAttempts && !$tripOperation) {
                try {
                    $validated['trip_number'] = TripOperation::generateTripNumber();
                    $validated['current_wizard_step'] = 1;
                    $validated['wizard_steps_completed'] = [];

                    // Set default values for decimal fields to avoid database errors
                    $validated['kilometers'] = $validated['kilometers'] ?? 0;
                    $validated['rate_per_km'] = $validated['rate_per_km'] ?? 0;
                    $validated['freight'] = $validated['freight'] ?? 0;
                    $validated['expenses'] = $validated['expenses'] ?? 0;
                    $validated['rent_paid'] = $validated['rent_paid'] ?? 0;
                    $validated['initial_amount'] = $validated['initial_amount'] ?? 0;
                    $validated['amount_changed'] = $validated['amount_changed'] ?? 0;
                    $validated['quantity'] = $validated['quantity'] ?? 0;
                    $validated['total_income'] = $validated['total_income'] ?? 0;
                    $validated['total_expense'] = $validated['total_expense'] ?? 0;

                    $tripOperation = TripOperation::create($validated);

                    // Save expense entries after trip operation is created
                    try {
                        $expenseEntries = $validExpenseEntries;
                        \Log::info('Processing expense entries:', $expenseEntries);

                        foreach ($expenseEntries as $entry) {
                            TripExpenseEntry::create([
                                'trip_operation_id' => $tripOperation->id,
                                'expense_category' => $entry['expense_category'] ?? 'other',
                                'payment_type' => $entry['payment_type'] ?? null,
                                'amount' => floatval($entry['amount']),
                                'description' => $entry['description'] ?? null,
                                'expense_date' => now(),
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error saving expense entries: ' . $e->getMessage());
                        // Continue even if expense entries fail
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->getCode() == 23000 && strpos($e->getMessage(), 'trip_number') !== false) {
                        // Duplicate trip number, try again
                        $attempts++;
                        continue;
                    }
                    throw $e;
                }
            }

            if (!$tripOperation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to generate unique trip number. Please try again or contact support.'
                ], 500);
            }
        } else {
            $tripOperation = TripOperation::findOrFail($tripId);

            // Set default values for decimal fields in update operations
            $validated['kilometers'] = $validated['kilometers'] ?? $tripOperation->kilometers ?? 0;
            $validated['rate_per_km'] = $validated['rate_per_km'] ?? $tripOperation->rate_per_km ?? 0;
            $validated['freight'] = $validated['freight'] ?? $tripOperation->freight ?? 0;
            $validated['expenses'] = $validated['expenses'] ?? $tripOperation->expenses ?? 0;
            $validated['rent_paid'] = $validated['rent_paid'] ?? $tripOperation->rent_paid ?? 0;
            $validated['initial_amount'] = $validated['initial_amount'] ?? $tripOperation->initial_amount ?? 0;
            $validated['amount_changed'] = $validated['amount_changed'] ?? $tripOperation->amount_changed ?? 0;
            $validated['quantity'] = $validated['quantity'] ?? $tripOperation->quantity ?? 0;

            // Recalculate freight if kilometers or rate changed
            if (isset($validated['kilometers']) && isset($validated['rate_per_km'])) {
                $validated['freight'] = $validated['kilometers'] * $validated['rate_per_km'];
            }

            // Recalculate total income based on freight
            $validated['total_income'] = $validated['freight'] ?? $tripOperation->total_income ?? 0;

            // Handle expense entries
            $expenseEntries = $request->input('expense_entries', []);
            $totalExpense = 0;

            \Log::info('Updating expense entries for trip ' . $tripOperation->id, $expenseEntries);

            // Filter out empty expense entries
            $validExpenseEntries = [];
            foreach ($expenseEntries as $entry) {
                if (isset($entry['amount']) && is_numeric($entry['amount']) && floatval($entry['amount']) > 0) {
                    $validExpenseEntries[] = $entry;
                }
            }

            // Delete existing expense entries for this trip
            TripExpenseEntry::where('trip_operation_id', $tripOperation->id)->delete();

            // Create new expense entries
            foreach ($validExpenseEntries as $entry) {
                try {
                    TripExpenseEntry::create([
                        'trip_operation_id' => $tripOperation->id,
                        'expense_category' => $entry['expense_category'] ?? 'other',
                        'payment_type' => $entry['payment_type'] ?? null,
                        'amount' => floatval($entry['amount']),
                        'description' => $entry['description'] ?? null,
                        'expense_date' => now(),
                    ]);
                    $totalExpense += floatval($entry['amount']);
                } catch (\Exception $e) {
                    \Log::error('Error creating expense entry: ' . $e->getMessage());
                }
            }

            // Recalculate total expense based on expense entries
            $validated['total_expense'] = $totalExpense;

            // Calculate net amount
            $validated['net_amount'] = $validated['total_income'] - $validated['total_expense'];

            $tripOperation->update($validated);
        }

        // Move to next step
        $nextStep = $step + 1;
        if ($nextStep > 4) {
            $nextStep = 4; // Stay at step 4
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
            'is_complete' => $step === 4,
            'message' => 'Step saved successfully'
        ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = [];
            foreach ($errors as $field => $messages) {
                if (is_array($messages)) {
                    $errorMessages = array_merge($errorMessages, $messages);
                } else {
                    $errorMessages[] = $messages;
                }
            }
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $errorMessages)
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving step data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get validation rules for each wizard step.
     */
    private function getStepValidationRules($step)
    {
        switch ($step) {
            case 1: // Basic Information + Business Details
                return [
                    'trip_date' => 'required|date',
                    'vehicle_number' => 'nullable|string|max:50',
                    'driver_name' => 'nullable|string|max:255',
                    'delivery_point' => 'required|string|max:255',
                    'gp_number' => 'nullable|string|max:50',
                    'business_category' => 'required|string|max:255',
                    'customer_name' => 'nullable|string|max:255',
                    'warehouse_location' => 'nullable|string|max:100',
                    'gl_number' => 'nullable|string|max:50',
                    'load_id' => 'nullable|string|max:50',
                    'freight_bill_no' => 'nullable|string|max:50',
                    'loading_point' => 'nullable|string|max:255',
                    'unloading_point' => 'nullable|string|max:255',
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
                    'expense_entries' => 'nullable|array',
                    'expense_entries.*.expense_category' => 'nullable|string|in:fuel,toll,parking,driver_payment,maintenance,loading_charges,unloading_charges,other',
                    'expense_entries.*.payment_type' => 'nullable|in:credit,cash',
                    'expense_entries.*.amount' => 'nullable|numeric|min:0|max:999999999999999999.99',
                    'expense_entries.*.description' => 'nullable|string|max:255',
                ];

            case 4: // Additional Details (Final Step)
                return [
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
            'Syngenta' => 'Syngenta'
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
            'fuel_payment_type' => 'nullable|in:credit,cash',
            'expenses' => 'nullable|numeric|min:0',
            'expense_category' => 'nullable|in:fuel,toll,parking',
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
            'expense_entries' => 'nullable|array',
            'expense_entries.*.expense_category' => 'required|string|in:fuel,toll,parking,driver_payment,maintenance,loading_charges,unloading_charges,other',
            'expense_entries.*.payment_type' => 'nullable|in:credit,cash',
            'expense_entries.*.amount' => 'required|numeric|min:0|max:999999999999999999.99',
            'expense_entries.*.description' => 'nullable|string|max:255',
        ]);

        // Auto-calculate billing information
        $date = Carbon::parse($validated['trip_date']);
        $validated['billing_month'] = $date->format('F-Y');
        $validated['billing_year'] = $date->year;
        $validated['billing_month_number'] = $date->month;

        // Auto-calculate freight
        $validated['freight'] = $validated['kilometers'] * $validated['rate_per_km'];

        // Auto-calculate total income (freight)
        $validated['total_income'] = $validated['freight'];

        // Handle expense entries if provided
        $expenseEntries = $request->input('expense_entries', []);
        $totalExpense = 0;

        if (!empty($expenseEntries)) {
            // Delete existing expense entries for this trip
            TripExpenseEntry::where('trip_operation_id', $tripOperation->id)->delete();

            // Create new expense entries
            foreach ($expenseEntries as $entry) {
                if (isset($entry['amount']) && is_numeric($entry['amount']) && $entry['amount'] > 0) {
                    TripExpenseEntry::create([
                        'trip_operation_id' => $tripOperation->id,
                        'expense_category' => $entry['expense_category'] ?? 'other',
                        'payment_type' => $entry['payment_type'] ?? null,
                        'amount' => floatval($entry['amount']),
                        'description' => $entry['description'] ?? null,
                        'expense_date' => now(),
                    ]);
                    $totalExpense += floatval($entry['amount']);
                }
            }
        } else {
            // If no expense entries provided, use existing total expense
            $totalExpense = $tripOperation->total_expense ?? 0;
        }

        // Auto-calculate total expense from expense entries
        $validated['total_expense'] = $totalExpense;

        // Calculate net amount
        $validated['net_amount'] = $validated['total_income'] - $validated['total_expense'];

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
        $tripOperation = TripOperation::with(['vehicle', 'driver', 'customer', 'warehouse', 'expenseEntries'])
            ->findOrFail($id);

        // Ensure total_income and total_expense are calculated from freight and expense entries
        if (!$tripOperation->total_income && $tripOperation->freight) {
            $tripOperation->total_income = $tripOperation->freight;
        }
        if (!$tripOperation->total_expense) {
            $tripOperation->total_expense = $tripOperation->expenseEntries->sum('amount');
        }
        if (!$tripOperation->net_amount) {
            $tripOperation->net_amount = $tripOperation->total_income - $tripOperation->total_expense;
        }

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

        // Ensure current wizard step is capped at 4
        if ($tripOperation->current_wizard_step > 4) {
            $tripOperation->current_wizard_step = 4;
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
            'Syngenta' => 'Syngenta'
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = [];
            foreach ($errors as $field => $messages) {
                if (is_array($messages)) {
                    $errorMessages = array_merge($errorMessages, $messages);
                } else {
                    $errorMessages[] = $messages;
                }
            }
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $errorMessages)
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving step data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export trip operations to Excel.
     */
    public function export(Request $request)
    {
        $month = $request->get('month');
        $year = $request->get('year', date('Y'));
        $warehouseLocation = $request->get('warehouse_location');
        $businessCategory = $request->get('business_category');
        $status = $request->get('status');

        $query = TripOperation::query();

        if ($month) {
            $query->where('billing_month_number', $month);
        }

        if ($year) {
            $query->where('billing_year', $year);
        }

        if ($warehouseLocation) {
            $query->where('warehouse_location', $warehouseLocation);
        }

        if ($businessCategory) {
            $query->where('business_category', $businessCategory);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tripOperations = $query->with(['vehicle', 'driver', 'customer', 'warehouse'])
            ->orderBy('trip_date', 'desc')
            ->get();

        // Create Excel file using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set company header
        $sheet->setCellValue('A1', 'SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY');
        $sheet->setCellValue('A2', 'Rizvi Chowk, Bypass Okara Road');
        $sheet->setCellValue('A3', 'Contact Detail: 0300-6967450');
        $sheet->setCellValue('A4', 'NTN : 4252472-5');

        $monthName = $month ? Carbon::create()->month($month)->format('F') : 'All Months';
        $sheet->setCellValue('A5', 'Trip Operations Report - ' . $monthName . ' ' . $year);

        // Set column headers
        $sheet->setCellValue('A7', 'Trip #');
        $sheet->setCellValue('B7', 'Date');
        $sheet->setCellValue('C7', 'Vehicle');
        $sheet->setCellValue('D7', 'Driver');
        $sheet->setCellValue('E7', 'Delivery Point');
        $sheet->setCellValue('F7', 'Warehouse');
        $sheet->setCellValue('G7', 'Category');
        $sheet->setCellValue('H7', 'KM');
        $sheet->setCellValue('I7', 'Freight');
        $sheet->setCellValue('J7', 'Total Income');
        $sheet->setCellValue('K7', 'Total Expense');
        $sheet->setCellValue('L7', 'Net Amount');
        $sheet->setCellValue('M7', 'Status');

        // Fill data
        $row = 8;
        foreach ($tripOperations as $trip) {
            $sheet->setCellValue('A' . $row, $trip->trip_number);
            $sheet->setCellValue('B' . $row, $trip->trip_date ? $trip->trip_date->format('d/m/Y') : 'N/A');
            $sheet->setCellValue('C' . $row, strtoupper($trip->vehicle_number));
            $sheet->setCellValue('D' . $row, $trip->driver_name ?? '-');
            $sheet->setCellValue('E' . $row, $trip->delivery_point);
            $sheet->setCellValue('F' . $row, $trip->warehouse_location ?? '-');
            $sheet->setCellValue('G' . $row, $trip->business_category ?? '-');
            $sheet->setCellValue('H' . $row, number_format($trip->kilometers, 2));
            $sheet->setCellValue('I' . $row, number_format($trip->freight, 2));
            $sheet->setCellValue('J' . $row, number_format($trip->total_income ?? $trip->freight, 2));
            $sheet->setCellValue('K' . $row, number_format($trip->total_expense ?? 0, 2));
            $sheet->setCellValue('L' . $row, number_format($trip->net_amount ?? ($trip->total_income - $trip->total_expense), 2));
            $sheet->setCellValue('M' . $row, ucfirst(str_replace('_', ' ', $trip->status)));
            $row++;
        }

        // Add totals row
        $row++;
        $sheet->setCellValue('A' . $row, 'TOTALS');
        $sheet->setCellValue('H' . $row, number_format($tripOperations->sum('kilometers'), 2));
        $sheet->setCellValue('I' . $row, number_format($tripOperations->sum('freight'), 2));
        $sheet->setCellValue('J' . $row, number_format($tripOperations->sum('total_income') ?? $tripOperations->sum('freight'), 2));
        $sheet->setCellValue('K' . $row, number_format($tripOperations->sum('total_expense') ?? 0, 2));
        $sheet->setCellValue('L' . $row, number_format($tripOperations->sum('net_amount') ?? ($tripOperations->sum('total_income') - $tripOperations->sum('total_expense')), 2));

        // Auto-size columns
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set headers for download
        $filename = "trip_operations_{$monthName}_{$year}.xlsx";

        // Save to temp file
        $tempFile = tempnam(sys_get_temp_dir(), 'trip_operations_');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFile);

        // Return file download response
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
