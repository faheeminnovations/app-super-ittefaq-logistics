<?php

namespace App\Http\Controllers;

use App\Models\WarehouseTrip;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Customer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WarehouseTripController extends Controller
{
    /**
     * Display a listing of warehouse trips.
     */
    public function index(Request $request)
    {
        $billingMonth = $request->get('billing_month', date('F-Y'));
        $warehouseLocation = $request->get('warehouse_location', 'DEPALPUR');
        
        $trips = WarehouseTrip::with(['vehicle', 'driver', 'customer'])
            ->byBillingMonth($billingMonth)
            ->where('warehouse_location', $warehouseLocation)
            ->orderBy('trip_date')
            ->paginate(50);
        
        $allTrips = WarehouseTrip::byBillingMonth($billingMonth)
            ->where('warehouse_location', $warehouseLocation)
            ->get();
        
        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        $customers = Customer::all();
        
        // Calculate totals
        $totalKm = $allTrips->sum('kilometers');
        $totalFreight = $allTrips->sum('freight');
        
        return view('pages.warehouse-trips', [
            'trips' => $trips,
            'billingMonth' => $billingMonth,
            'warehouseLocation' => $warehouseLocation,
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'customers' => $customers,
            'totalKm' => $totalKm,
            'totalFreight' => $totalFreight,
            'totalTrips' => $allTrips->count(),
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
                'fuel_type' => 'nullable|string|max:50',
                'driver_name' => 'nullable|string|max:255',
                'load_id' => 'nullable|string|max:50',
                'freight_bill_no' => 'nullable|string|max:50',
                'billing_month' => 'required|string|max:50',
                'warehouse_location' => 'required|string|max:100',
                'gl_number' => 'nullable|string|max:50',
                'business_area' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'status' => 'required|in:pending,completed,billed',
                'vehicle_id' => 'nullable|exists:vehicles,id',
                'driver_id' => 'nullable|exists:drivers,id',
                'customer_id' => 'nullable|exists:customers,id',
            ]);

            \Log::info('Validation passed', ['validated' => $validated]);

            // Auto-generate trip number
            $validated['trip_number'] = WarehouseTrip::generateTripNumber();

            // Calculate freight automatically
            $validated['freight'] = $validated['kilometers'] * $validated['rate_per_km'];

            \Log::info('About to create trip', ['data' => $validated]);

            $trip = WarehouseTrip::create($validated);
            \Log::info('Trip created successfully', ['trip_id' => $trip->id]);

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
        
        $trip = WarehouseTrip::with(['vehicle', 'driver', 'customer'])->findOrFail($id);
        
        \Log::info('Trip data for edit', ['trip' => $trip->toArray()]);
        
        return response()->json([
            'trip' => $trip,
            'success' => true
        ], 200);
    }

    /**
     * Update the specified warehouse trip.
     */
    public function update(Request $request, $id)
    {
        $trip = WarehouseTrip::findOrFail($id);

        $validated = $request->validate([
            'trip_date' => 'required|date',
            'vehicle_number' => 'required|string|max:50',
            'gp_number' => 'required|string|max:50',
            'delivery_point' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:10',
            'kilometers' => 'required|numeric|min:0',
            'rate_per_km' => 'required|numeric|min:0',
            'fuel_type' => 'nullable|string|max:50',
            'driver_name' => 'nullable|string|max:255',
            'load_id' => 'nullable|string|max:50',
            'freight_bill_no' => 'nullable|string|max:50',
            'billing_month' => 'required|string|max:50',
            'warehouse_location' => 'required|string|max:100',
            'gl_number' => 'nullable|string|max:50',
            'business_area' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,billed',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        // Recalculate freight
        $validated['freight'] = $validated['kilometers'] * $validated['rate_per_km'];

        $trip->update($validated);

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
        $trip = WarehouseTrip::findOrFail($id);
        $trip->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Trip deleted successfully.']);
        }

        return redirect()->back()->with('success', 'Trip deleted successfully.');
    }

    /**
     * Export warehouse trips to Excel.
     */
    public function export(Request $request)
    {
        $billingMonth = $request->get('billing_month', date('F-Y'));
        $warehouseLocation = $request->get('warehouse_location', 'DEPALPUR');
        
        $trips = WarehouseTrip::with(['vehicle', 'driver', 'customer'])
            ->byBillingMonth($billingMonth)
            ->where('warehouse_location', $warehouseLocation)
            ->orderBy('trip_date')
            ->get();
        
        // Create Excel file using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set company header
        $sheet->setCellValue('A1', 'SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY');
        $sheet->setCellValue('A2', 'Rizvi Chowk, Bypass Okara Road');
        $sheet->setCellValue('A3', 'Contact Detail: 0300-6967450');
        $sheet->setCellValue('A4', 'NTN : 4252472-5');
        $sheet->setCellValue('A5', 'Invoice No : 0000');
        $sheet->setCellValue('A6', 'Billing Month : ' . $billingMonth);
        
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
        $filename = "warehouse_trips_{$billingMonth}.xlsx";
        
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
        // Handle both GET parameters and JSON body
        if ($request->isJson()) {
            $data = $request->json()->all();
            $billingMonth = $data['billing_month'] ?? date('F-Y');
            $warehouseLocation = $data['warehouse_location'] ?? 'DEPALPUR';
        } else {
            $billingMonth = $request->get('billing_month', date('F-Y'));
            $warehouseLocation = $request->get('warehouse_location', 'DEPALPUR');
        }
        
        \Log::info('Generate invoice called', [
            'billing_month' => $billingMonth,
            'warehouse_location' => $warehouseLocation,
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson()
        ]);
        
        // Get all trips for the current month/location (not just pending ones)
        $trips = WarehouseTrip::with(['vehicle', 'driver', 'customer'])
            ->byBillingMonth($billingMonth)
            ->where('warehouse_location', $warehouseLocation)
            ->orderBy('trip_date')
            ->get();
        
        if ($trips->isEmpty()) {
            \Log::info('No trips found for invoice generation');
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No trips found for this billing period.']);
            }
            return redirect()->back()->with('error', 'No trips found for this billing period.');
        }
        
        // Mark all trips as billed
        foreach ($trips as $trip) {
            $trip->update(['status' => 'billed']);
        }
        
        // Calculate totals
        $totalKm = $trips->sum('kilometers');
        $totalFreight = $trips->sum('freight');
        
        // Generate invoice number
        $invoiceNumber = 'INV-' . strtoupper(substr($billingMonth, 0, 3)) . '-' . date('Y') . '-' . str_pad(count($trips), 4, '0', STR_PAD_LEFT);
        
        \Log::info('Invoice data prepared', [
            'trips_count' => $trips->count(),
            'total_km' => $totalKm,
            'total_freight' => $totalFreight,
            'invoice_number' => $invoiceNumber
        ]);
        
        // Return invoice view or JSON response
        if ($request->ajax() || $request->wantsJson()) {
            $html = view('pages.warehouse-invoice', [
                'trips' => $trips,
                'billingMonth' => $billingMonth,
                'warehouseLocation' => $warehouseLocation,
                'totalKm' => $totalKm,
                'totalFreight' => $totalFreight,
                'invoiceNumber' => $invoiceNumber,
            ])->render();
            
            \Log::info('Returning JSON response for invoice');
            
            return response()->json([
                'success' => true, 
                'message' => 'Invoice generated successfully for ' . count($trips) . ' trips.',
                'invoice_html' => $html
            ]);
        }
        
        return view('pages.warehouse-invoice', [
            'trips' => $trips,
            'billingMonth' => $billingMonth,
            'warehouseLocation' => $warehouseLocation,
            'totalKm' => $totalKm,
            'totalFreight' => $totalFreight,
            'invoiceNumber' => $invoiceNumber,
        ]);
    }
}