<?php

namespace App\Http\Controllers;

use App\Models\WarehouseInvoice;
use App\Models\WarehouseTrip;
use App\Models\CompanySettings;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WarehouseInvoiceController extends Controller
{
    /**
     * Display a listing of warehouse invoices
     */
    public function index(Request $request)
    {
        $query = WarehouseInvoice::query();

        // Filter by month if provided
        if ($request->has('month') && $request->has('year')) {
            $query->where('billing_month', $request->month)
                  ->where('billing_year', $request->year);
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create()->month($i)->format('F');
        }

        $statuses = ['draft', 'pending', 'sent', 'paid', 'cancelled'];

        return view('warehouse.invoices.index', compact(
            'invoices',
            'months',
            'statuses'
        ));
    }

    /**
     * Show the form for creating a new warehouse invoice
     */
    public function create()
    {
        // Get unbilled warehouse trips
        $unbilledTrips = WarehouseTrip::whereNull('warehouse_invoice_id')
            ->orderBy('trip_date', 'desc')
            ->get();

        return view('warehouse.invoices.create', compact('unbilledTrips'));
    }

    /**
     * Store a newly created warehouse invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'billing_month' => 'required|string',
            'billing_year' => 'required|integer',
            'warehouse_location' => 'required|string',
            'gl_number' => 'required|string',
            'business_area' => 'required|string',
            'service_provider_name' => 'required|string',
            'service_provider_address' => 'required|string',
            'service_provider_ntn' => 'required|string',
            'client_name' => 'required|string',
            'client_address' => 'required|string',
            'client_ntn' => 'nullable|string',
            'tax_rate' => 'required|numeric|min:0',
            'selected_trips' => 'required|array',
            'selected_trips.*' => 'exists:warehouse_trips,id',
            'notes' => 'nullable|string',
        ]);

        // Auto-generate invoice number
        $validated['invoice_number'] = WarehouseInvoice::generateInvoiceNumber();
        $validated['billing_month_number'] = Carbon::parse($validated['billing_month'])->month;
        $validated['status'] = 'draft';

        // Create invoice
        $invoice = WarehouseInvoice::create($validated);

        // Link selected trips to invoice
        foreach ($validated['selected_trips'] as $tripId) {
            $trip = WarehouseTrip::find($tripId);
            if ($trip) {
                $trip->warehouse_invoice_id = $invoice->id;
                $trip->invoice_number = $invoice->invoice_number;
                $trip->status = 'billed';
                $trip->save();
            }
        }

        // Calculate totals
        $invoice->calculateTotals();

        return redirect()->route('warehouse.invoices.index')
            ->with('success', 'Warehouse invoice created successfully.');
    }

    /**
     * Display the specified warehouse invoice
     */
    public function show(WarehouseInvoice $invoice)
    {
        $invoice->load('warehouseTrips');
        return view('warehouse.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified warehouse invoice
     */
    public function edit(WarehouseInvoice $invoice)
    {
        if ($invoice->status === 'sent' || $invoice->status === 'paid') {
            return redirect()->route('warehouse.invoices.show', $invoice)
                ->with('error', 'Cannot edit sent or paid invoices.');
        }

        $unbilledTrips = WarehouseTrip::whereNull('warehouse_invoice_id')
            ->orWhere('warehouse_invoice_id', $invoice->id)
            ->orderBy('trip_date', 'desc')
            ->get();

        return view('warehouse.invoices.edit', compact('invoice', 'unbilledTrips'));
    }

    /**
     * Update the specified warehouse invoice
     */
    public function update(Request $request, WarehouseInvoice $invoice)
    {
        if ($invoice->status === 'sent' || $invoice->status === 'paid') {
            return redirect()->route('warehouse.invoices.show', $invoice)
                ->with('error', 'Cannot edit sent or paid invoices.');
        }

        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'billing_month' => 'required|string',
            'billing_year' => 'required|integer',
            'warehouse_location' => 'required|string',
            'gl_number' => 'required|string',
            'business_area' => 'required|string',
            'service_provider_name' => 'required|string',
            'service_provider_address' => 'required|string',
            'service_provider_ntn' => 'required|string',
            'client_name' => 'required|string',
            'client_address' => 'required|string',
            'client_ntn' => 'nullable|string',
            'tax_rate' => 'required|numeric|min:0',
            'selected_trips' => 'required|array',
            'selected_trips.*' => 'exists:warehouse_trips,id',
            'notes' => 'nullable|string',
        ]);

        $validated['billing_month_number'] = Carbon::parse($validated['billing_month'])->month;

        // Update invoice
        $invoice->update($validated);

        // Remove all existing trip associations
        WarehouseTrip::where('warehouse_invoice_id', $invoice->id)
            ->update(['warehouse_invoice_id' => null, 'invoice_number' => null, 'status' => 'pending']);

        // Link selected trips to invoice
        foreach ($validated['selected_trips'] as $tripId) {
            $trip = WarehouseTrip::find($tripId);
            if ($trip) {
                $trip->warehouse_invoice_id = $invoice->id;
                $trip->invoice_number = $invoice->invoice_number;
                $trip->status = 'billed';
                $trip->save();
            }
        }

        // Recalculate totals
        $invoice->calculateTotals();

        return redirect()->route('warehouse.invoices.show', $invoice)
            ->with('success', 'Warehouse invoice updated successfully.');
    }

    /**
     * Remove the specified warehouse invoice
     */
    public function destroy(WarehouseInvoice $invoice)
    {
        if ($invoice->status === 'sent' || $invoice->status === 'paid') {
            return redirect()->route('warehouse.invoices.index')
                ->with('error', 'Cannot delete sent or paid invoices.');
        }

        // Unlink all trips
        WarehouseTrip::where('warehouse_invoice_id', $invoice->id)
            ->update(['warehouse_invoice_id' => null, 'invoice_number' => null, 'status' => 'pending']);

        $invoice->delete();

        return redirect()->route('warehouse.invoices.index')
            ->with('success', 'Warehouse invoice deleted successfully.');
    }

    /**
     * Mark invoice as sent
     */
    public function markAsSent(WarehouseInvoice $invoice)
    {
        $invoice->status = 'sent';
        $invoice->save();

        return redirect()->route('warehouse.invoices.show', $invoice)
            ->with('success', 'Invoice marked as sent.');
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(WarehouseInvoice $invoice)
    {
        $invoice->status = 'paid';
        $invoice->save();

        return redirect()->route('warehouse.invoices.show', $invoice)
            ->with('success', 'Invoice marked as paid.');
    }
}
