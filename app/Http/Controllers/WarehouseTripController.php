<?php

namespace App\Http\Controllers;

use App\Models\WarehouseTrip;
use App\Models\WarehouseExpenseEntry;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Customer;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class WarehouseTripController extends Controller
{
    /**
     * Display a listing of warehouse trips.
     */
    public function index(Request $request)
    {
        $vehicleNumber = $request->get('vehicle_number');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $warehouseLocation = $request->get('warehouse_location');

        $query = WarehouseTrip::with(['vehicle', 'driver', 'customer']);

        // Apply vehicle number filter if provided
        if ($vehicleNumber) {
            $query->where('vehicle_number', 'like', '%' . $vehicleNumber . '%');
        }

        // Apply warehouse location filter if provided
        if ($warehouseLocation) {
            $query->where(function($q) use ($warehouseLocation) {
                $q->where('warehouse_location', $warehouseLocation)
                  ->orWhereNull('warehouse_location');
            });
        }

        // Apply date range filter if provided
        $query->byDateRange($dateFrom, $dateTo);

        $trips = $query->orderBy('trip_date')->paginate(50);

        // Clone query for totals calculation
        $totalsQuery = WarehouseTrip::query();

        if ($vehicleNumber) {
            $totalsQuery->where('vehicle_number', 'like', '%' . $vehicleNumber . '%');
        }
        if ($warehouseLocation) {
            $totalsQuery->where(function($q) use ($warehouseLocation) {
                $q->where('warehouse_location', $warehouseLocation)
                  ->orWhereNull('warehouse_location');
            });
        }
        $totalsQuery->byDateRange($dateFrom, $dateTo);

        $allTrips = $totalsQuery->get();

        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        $customers = Customer::all();
        $warehouses = Warehouse::active()->get();

        // Calculate totals
        $totalKm = $allTrips->sum('kilometers');
        $totalFreight = $allTrips->sum('freight');
        $totalIncome = $allTrips->sum('total_income');
        $totalExpense = $allTrips->sum('total_expense');
        $netAmount = $totalIncome - $totalExpense;

        return view('pages.warehouse-trips', [
            'trips' => $trips,
            'warehouseLocation' => $warehouseLocation,
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'customers' => $customers,
            'warehouses' => $warehouses,
            'totalKm' => $totalKm,
            'totalFreight' => $totalFreight,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netAmount' => $netAmount,
            'totalTrips' => $allTrips->count(),
            'billingMonth' => date('F-Y'),
        ]);
    }

    /**
     * Store a newly created warehouse trip.
     */
    public function store(Request $request)
    {
        // Log incoming request for debugging
        \Log::info('WarehouseTrip store called', ['request' => $request->all()]);
        
        try {
            $validated = $request->validate([
                'trip_date' => 'required|date',
                'vehicle_number' => 'required|string|max:50',
                'gp_number' => 'required|string|max:50',
                'delivery_point' => 'required|string|max:255',
                'vehicle_type' => 'required|string|max:10',
                'kilometers' => 'required|numeric|min:0',
                'rate_per_km' => 'required|numeric|min:0',
                'driver_name' => 'nullable|string|max:255',
                'freight_bill_no' => 'nullable|string|max:50',
                'billing_month' => 'nullable|string|max:50',
                'warehouse_location' => 'nullable|string|max:100',
                'business_category' => 'required|string|max:255',
                'gl_number' => 'nullable|string|max:50',
                'business_area' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'status' => 'required|in:pending,completed,billed',
                'vehicle_id' => 'nullable|exists:vehicles,id',
                'driver_id' => 'nullable|exists:drivers,id',
                'customer_id' => 'nullable|exists:customers,id',
                'expense_entries' => 'nullable|array',
                'expense_entries.*.expense_category' => 'nullable|string',
                'expense_entries.*.payment_type' => 'nullable|string',
                'expense_entries.*.amount' => 'nullable|numeric',
                'expense_entries.*.description' => 'nullable|string',
            ]);

            \Log::info('Validation passed', ['validated' => $validated]);

            // Auto-generate trip number
            $validated['trip_number'] = WarehouseTrip::generateTripNumber();

            // Auto-calculate billing month from trip date if not provided
            if (empty($validated['billing_month'])) {
                $date = Carbon::parse($validated['trip_date']);
                $validated['billing_month'] = $date->format('F-Y');
            }

            // Calculate freight automatically
            $validated['freight'] = $validated['kilometers'] * $validated['rate_per_km'];

            // Handle expense entries
            $expenseEntries = $validated['expense_entries'] ?? [];
            $totalExpense = 0;
            $interestIncome = 0;

            // Filter out empty expense entries and calculate total expense
            $validExpenseEntries = [];
            foreach ($expenseEntries as $entry) {
                if (isset($entry['amount']) && is_numeric($entry['amount']) && floatval($entry['amount']) > 0) {
                    $validExpenseEntries[] = $entry;
                    $totalExpense += floatval($entry['amount']);
                    
                    // Calculate interest income for credit expenses (jo where ki jo pay a rh awo ay)
                    if (isset($entry['payment_type']) && $entry['payment_type'] === 'credit') {
                        // Add 2% interest on credit expenses
                        $interestIncome += floatval($entry['amount']) * 0.02;
                    }
                }
            }

            // Set financial values
            $validated['total_income'] = $validated['freight'] + $interestIncome;
            $validated['total_expense'] = $totalExpense;
            $validated['net_amount'] = $validated['total_income'] - $validated['total_expense'];

            // Remove expense entries from validated data to avoid database issues
            unset($validated['expense_entries']);

            \Log::info('About to create trip', ['data' => $validated]);

            $trip = WarehouseTrip::create($validated);
            \Log::info('Trip created successfully', ['trip_id' => $trip->id]);

            // Save expense entries after trip is created
            try {
                foreach ($validExpenseEntries as $entry) {
                    WarehouseExpenseEntry::create([
                        'warehouse_trip_id' => $trip->id,
                        'expense_category' => $entry['expense_category'] ?? 'other',
                        'payment_type' => $entry['payment_type'] ?? null,
                        'amount' => floatval($entry['amount']),
                        'description' => $entry['description'] ?? null,
                        'expense_date' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error saving expense entries:', ['message' => $e->getMessage()]);
                // Continue even if expense entries fail
            }

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Trip added successfully.']);
            }

            return redirect()->back()->with('success', 'Trip added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error', ['errors' => $e->errors()]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Validation failed: ' . implode(', ', $e->errors())], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating trip', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error saving trip: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Error saving trip: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified warehouse trip.
     */
    public function show($id)
    {
        $trip = WarehouseTrip::with(['vehicle', 'driver', 'customer'])->findOrFail($id);
        
        return view('pages.warehouse-trips-show', ['trip' => $trip]);
    }

    /**
     * Get trip data for editing.
     */
    public function getEditData($id)
    {
        \Log::info('getEditData called', ['id' => $id]);

        $trip = WarehouseTrip::with(['vehicle', 'driver', 'customer', 'expenseEntries'])->findOrFail($id);

        // Format trip_date for the form
        $tripData = $trip->toArray();
        if ($trip->trip_date) {
            $tripData['trip_date'] = $trip->trip_date->format('Y-m-d');
        }

        \Log::info('Trip data for edit', ['trip' => $tripData]);

        return response()->json([
            'trip' => $tripData,
            'success' => true
        ], 200);
    }

    /**
     * Update the specified warehouse trip.
     */
    public function update(Request $request, $id)
    {
        $trip = WarehouseTrip::findOrFail($id);

        \Log::info('Update trip request', [
            'id' => $id,
            'request_data' => $request->all(),
            'has_trip_date' => $request->has('trip_date'),
            'trip_date_value' => $request->input('trip_date')
        ]);

        $validated = $request->validate([
            'trip_date' => 'required|date',
            'vehicle_number' => 'required|string|max:50',
            'gp_number' => 'required|string|max:50',
            'delivery_point' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:10',
            'kilometers' => 'required|numeric|min:0',
            'rate_per_km' => 'required|numeric|min:0',
            'driver_name' => 'nullable|string|max:255',
            'freight_bill_no' => 'nullable|string|max:50',
            'billing_month' => 'nullable|string|max:50',
            'warehouse_location' => 'nullable|string|max:100',
            'gl_number' => 'nullable|string|max:50',
            'business_category' => 'nullable|string|max:255',
            'business_area' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,billed',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'customer_id' => 'nullable|exists:customers,id',
            'expense_entries' => 'nullable|array',
            'expense_entries.*.expense_category' => 'nullable|string',
            'expense_entries.*.payment_type' => 'nullable|string',
            'expense_entries.*.amount' => 'nullable|numeric',
            'expense_entries.*.description' => 'nullable|string',
        ]);

        // Auto-calculate billing month from trip date if not provided
        if (empty($validated['billing_month'])) {
            $date = Carbon::parse($validated['trip_date']);
            $validated['billing_month'] = $date->format('F-Y');
        }

        // Recalculate freight
        $validated['freight'] = $validated['kilometers'] * $validated['rate_per_km'];

        // Handle expense entries
        $expenseEntries = $validated['expense_entries'] ?? [];
        $totalExpense = 0;
        $interestIncome = 0;

        // Filter out empty expense entries and calculate total expense
        $validExpenseEntries = [];
        foreach ($expenseEntries as $entry) {
            if (isset($entry['amount']) && is_numeric($entry['amount']) && floatval($entry['amount']) > 0) {
                $validExpenseEntries[] = $entry;
                $totalExpense += floatval($entry['amount']);
                
                // Calculate interest income for credit expenses
                if (isset($entry['payment_type']) && $entry['payment_type'] === 'credit') {
                    // Add 2% interest on credit expenses
                    $interestIncome += floatval($entry['amount']) * 0.02;
                }
            }
        }

        // Set financial values
        $validated['total_income'] = $validated['freight'] + $interestIncome;
        $validated['total_expense'] = $totalExpense;
        $validated['net_amount'] = $validated['total_income'] - $validated['total_expense'];

        // Remove expense entries from validated data to avoid database issues
        unset($validated['expense_entries']);

        $trip->update($validated);

        // Delete existing expense entries
        $trip->expenseEntries()->delete();

        // Save new expense entries
        try {
            foreach ($validExpenseEntries as $entry) {
                WarehouseExpenseEntry::create([
                    'warehouse_trip_id' => $trip->id,
                    'expense_category' => $entry['expense_category'] ?? 'other',
                    'payment_type' => $entry['payment_type'] ?? null,
                    'amount' => floatval($entry['amount']),
                    'description' => $entry['description'] ?? null,
                    'expense_date' => now(),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error saving expense entries:', ['message' => $e->getMessage()]);
            // Continue even if expense entries fail
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Trip updated successfully.']);
        }

        return redirect()->back()->with('success', 'Trip updated successfully.');
    }

    /**
     * Remove the specified warehouse trip.
     */
    public function destroy($id)
    {
        try {
            $trip = WarehouseTrip::findOrFail($id);

            // Delete expense entries first
            $trip->expenseEntries()->delete();

            // Delete the trip
            $trip->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Trip deleted successfully.']);
            }

            return redirect()->back()->with('success', 'Trip deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting trip:', ['message' => $e->getMessage()]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete trip: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete trip: ' . $e->getMessage());
        }
    }

    /**
     * Export warehouse trips to Excel.
     */
    public function export(Request $request)
    {
        $vehicleNumber = $request->get('vehicle_number');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $warehouseLocation = $request->get('warehouse_location');

        $query = WarehouseTrip::with(['vehicle', 'driver', 'customer']);

        // Apply vehicle number filter if provided
        if ($vehicleNumber) {
            $query->where('vehicle_number', 'like', '%' . $vehicleNumber . '%');
        }

        // Apply warehouse location filter if provided
        if ($warehouseLocation) {
            $query->where('warehouse_location', $warehouseLocation);
        }

        // Apply date range filter if provided
        $query->byDateRange($dateFrom, $dateTo);

        $trips = $query->orderBy('trip_date')->get();

        // Create Excel file using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set company header
        $sheet->setCellValue('A1', 'SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY');
        $sheet->setCellValue('A2', 'Rizvi Chowk, Bypass Okara Road');
        $sheet->setCellValue('A3', 'Contact Detail: 0300-6967450');
        $sheet->setCellValue('A4', 'NTN : 4252472-5');
        $sheet->setCellValue('A5', 'Invoice No : 0000');

        // Set filter info
        $filterInfo = [];
        if ($vehicleNumber) $filterInfo[] = "Vehicle: {$vehicleNumber}";
        if ($dateFrom && $dateTo) $filterInfo[] = "Date: " . Carbon::parse($dateFrom)->format('d/m/Y') . " to " . Carbon::parse($dateTo)->format('d/m/Y');
        if ($warehouseLocation) $filterInfo[] = "Warehouse: {$warehouseLocation}";

        $sheet->setCellValue('A6', 'Filter: ' . (count($filterInfo) > 0 ? implode(' | ', $filterInfo) : 'All Time'));

        // Set column headers
        $sheet->setCellValue('A7', 'Sr');
        $sheet->setCellValue('B7', 'Date');
        $sheet->setCellValue('C7', 'Vhl No');
        $sheet->setCellValue('D7', 'GP#');
        $sheet->setCellValue('E7', 'Drop/Delivery Point');
        $sheet->setCellValue('F7', 'Vhl');
        $sheet->setCellValue('G7', 'Km');
        $sheet->setCellValue('H7', 'Rate');
        $sheet->setCellValue('I7', 'FRT');

        // Fill data
        $row = 8;
        $sr = 1;
        foreach ($trips as $trip) {
            $sheet->setCellValue('A' . $row, $sr++);
            $sheet->setCellValue('B' . $row, $trip->trip_date ? $trip->trip_date->format('d/m/Y') : 'N/A');
            $sheet->setCellValue('C' . $row, $trip->vehicle_number);
            $sheet->setCellValue('D' . $row, $trip->gp_number);
            $sheet->setCellValue('E' . $row, $trip->delivery_point);
            $sheet->setCellValue('F' . $row, $trip->vehicle_type);
            $sheet->setCellValue('G' . $row, $trip->kilometers);
            $sheet->setCellValue('H' . $row, $trip->rate_per_km);
            $sheet->setCellValue('I' . $row, $trip->freight);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set headers for download
        $filename = "warehouse_trips_" . date('Y-m-d') . ".xlsx";

        // Save to temp file
        $tempFile = tempnam(sys_get_temp_dir(), 'warehouse_trips_');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFile);

        // Return file download response
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Generate invoice for billing month.
     */
    public function generateInvoice(Request $request)
    {
        // Set default warehouse location to Depalpur
        $defaultWarehouseLocation = 'Depalpur';

        // Handle both GET parameters and JSON body
        if ($request->isJson()) {
            $data = $request->json()->all();
            $billingMonth = $data['billing_month'] ?? date('F-Y');
            $warehouseLocation = $data['warehouse_location'] ?? $defaultWarehouseLocation;
            $dateFrom = $data['date_from'] ?? null;
            $dateTo = $data['date_to'] ?? null;
            $vehicleNumber = $data['vehicle_number'] ?? null;
        } else {
            $billingMonth = $request->get('billing_month', date('F-Y'));
            $warehouseLocation = $request->get('warehouse_location', $defaultWarehouseLocation);
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $vehicleNumber = $request->get('vehicle_number');
        }

        \Log::info('Generate invoice called', [
            'billing_month' => $billingMonth,
            'warehouse_location' => $warehouseLocation,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'vehicle_number' => $vehicleNumber,
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson()
        ]);

        // Build query
        $query = WarehouseTrip::with(['vehicle', 'driver', 'customer', 'expenseEntries']);

        // Apply vehicle number filter if provided
        if ($vehicleNumber) {
            $query->where('vehicle_number', 'like', '%' . $vehicleNumber . '%');
        }

        // Apply warehouse location filter only if vehicle is NOT filtered and warehouse is provided
        if (!$vehicleNumber && $warehouseLocation) {
            $query->where(function($q) use ($warehouseLocation) {
                $q->where('warehouse_location', $warehouseLocation)
                  ->orWhereNull('warehouse_location');
            });
        }

        // Apply date range filter if provided
        if ($dateFrom && $dateTo) {
            $query->whereBetween('trip_date', [$dateFrom, $dateTo]);
        } elseif ($vehicleNumber) {
            // If vehicle filter is provided but no date range, don't filter by date or billing_month
            // Just use the vehicle filter
        } else {
            // Only use billing_month if no other filters are provided
            $query->byBillingMonth($billingMonth);
        }

        $trips = $query->orderBy('trip_date')->get();

        if ($trips->isEmpty()) {
            \Log::info('No trips found for invoice generation', [
                'warehouse_location' => $warehouseLocation,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'vehicle_number' => $vehicleNumber,
                'billing_month' => $billingMonth
            ]);

            // Determine appropriate error message
            $errorMessage = 'No trips found';
            if ($dateFrom && $dateTo) {
                $errorMessage .= ' for the selected date range';
            } elseif ($vehicleNumber) {
                $errorMessage .= ' for this vehicle';
            } else {
                $errorMessage .= ' for this billing period';
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage]);
            }
            return redirect()->back()->with('error', $errorMessage);
        }
        
        // Mark all trips as billed
        foreach ($trips as $trip) {
            $trip->update(['status' => 'billed']);
        }
        
        // Calculate totals
        $totalKm = $trips->sum('kilometers');
        $totalFreight = $trips->sum('freight');
        
        // Calculate income and expense totals
        $totalIncome = $trips->sum('total_income') ?? $totalFreight;
        $totalExpense = $trips->sum('total_expense') ?? 0;
        $netAmount = $totalIncome - $totalExpense;
        
        // Get expense entries breakdown with payment types
        $expenseEntries = [];
        foreach ($trips as $trip) {
            foreach ($trip->expenseEntries as $expense) {
                $expenseEntries[] = [
                    'category' => $expense->expense_category,
                    'description' => $expense->description,
                    'payment_type' => $expense->payment_type,
                    'amount' => $expense->amount,
                ];
            }
        }

        // Get expense categories breakdown for summary
        $expenseCategories = [];
        foreach ($trips as $trip) {
            foreach ($trip->expenseEntries as $expense) {
                $category = $expense->expense_category;
                if (!isset($expenseCategories[$category])) {
                    $expenseCategories[$category] = 0;
                }
                $expenseCategories[$category] += $expense->amount;
            }
        }
        
        // Generate invoice number
        $invoiceNumber = 'INV-' . strtoupper(substr($billingMonth, 0, 3)) . '-' . date('Y') . '-' . str_pad(count($trips), 4, '0', STR_PAD_LEFT);
        
        \Log::info('Invoice data prepared', [
            'trips_count' => $trips->count(),
            'total_km' => $totalKm,
            'total_freight' => $totalFreight,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_amount' => $netAmount,
            'invoice_number' => $invoiceNumber
        ]);
        
        // Determine invoice type (with or without expenses)
        $invoiceType = $request->get('invoice_type', 'basic');
        $viewName = $invoiceType === 'with_expenses' ? 'pages.warehouse-invoice-with-expenses' : 'pages.warehouse-invoice';
        
        // Return invoice view or JSON response
        if ($request->ajax() || $request->wantsJson()) {
            $html = view($viewName, [
                'trips' => $trips,
                'billingMonth' => $billingMonth,
                'warehouseLocation' => $warehouseLocation,
                'totalKm' => $totalKm,
                'totalFreight' => $totalFreight,
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'netAmount' => $netAmount,
                'expenseCategories' => $expenseCategories,
                'expenseEntries' => $expenseEntries,
                'invoiceNumber' => $invoiceNumber,
            ])->render();
            
            \Log::info('Returning JSON response for invoice');
            
            return response()->json([
                'success' => true, 
                'message' => 'Invoice generated successfully for ' . count($trips) . ' trips.',
                'invoice_html' => $html
            ]);
        }
        
        return view($viewName, [
            'trips' => $trips,
            'billingMonth' => $billingMonth,
            'warehouseLocation' => $warehouseLocation,
            'totalKm' => $totalKm,
            'totalFreight' => $totalFreight,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netAmount' => $netAmount,
            'expenseCategories' => $expenseCategories,
            'invoiceNumber' => $invoiceNumber,
        ]);
    }
}