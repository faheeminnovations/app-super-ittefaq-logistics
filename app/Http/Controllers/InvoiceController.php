<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\TripLog;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('tripLogs')->orderBy('created_at', 'desc')->paginate(15);
        $allInvoices = Invoice::all();

        return view('pages.invoices', [
            'invoices' => $invoices,
            'totalInvoices' => $allInvoices->count(),
            'totalAmount' => $allInvoices->sum('total_amount'),
            'draftInvoices' => $allInvoices->where('status', 'draft')->count(),
            'sentInvoices' => $allInvoices->where('status', 'sent')->count(),
            'paidInvoices' => $allInvoices->where('status', 'paid')->count(),
        ]);
    }

    public function create()
    {
        return view('pages.invoices-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number|max:50',
            'invoice_date' => 'required|date',
            'billing_month' => 'required|string',
            'billing_year' => 'required|integer',
            'billing_month_number' => 'required|integer|between:1,12',
            'warehouse' => 'nullable|string',
            'gl_number' => 'nullable|string',
            'business_area' => 'nullable|string',
            'service_provider_name' => 'nullable|string',
            'service_provider_address' => 'nullable|string',
            'service_provider_ntn' => 'nullable|string',
            'service_provider_strn' => 'nullable|string',
            'client_name' => 'nullable|string',
            'client_address' => 'nullable|string',
            'client_ntn' => 'nullable|string',
            'client_strn' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'draft';
        $validated['subtotal'] = 0;
        $validated['tax_amount'] = 0;
        $validated['total_amount'] = 0;

        Invoice::create($validated);
        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(string $id)
    {
        $invoice = Invoice::with('tripLogs')->findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['invoice' => $invoice]);
        }
        
        return view('pages.invoices-show', compact('invoice'));
    }

    public function edit(string $id)
    {
        $invoice = Invoice::with('tripLogs')->findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($invoice);
        }
        
        return view('pages.invoices-edit', compact('invoice'));
    }

    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $id . '|max:50',
            'invoice_date' => 'required|date',
            'billing_month' => 'required|string',
            'billing_year' => 'required|integer',
            'billing_month_number' => 'required|integer|between:1,12',
            'warehouse' => 'nullable|string',
            'gl_number' => 'nullable|string',
            'business_area' => 'nullable|string',
            'service_provider_name' => 'nullable|string',
            'service_provider_address' => 'nullable|string',
            'service_provider_ntn' => 'nullable|string',
            'service_provider_strn' => 'nullable|string',
            'client_name' => 'nullable|string',
            'client_address' => 'nullable|string',
            'client_ntn' => 'nullable|string',
            'client_strn' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,cancelled',
            'notes' => 'nullable|string',
            'verified_by' => 'nullable|string',
        ]);

        $invoice->update($validated);
        
        // Recalculate totals if trip logs exist
        if ($invoice->tripLogs()->exists()) {
            $invoice->calculateTotals();
        }
        
        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function export(Request $request)
    {
        $invoices = Invoice::with('tripLogs')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="invoices_export_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($invoices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Invoice Number', 'Billing Month', 'Date', 'Client', 'Subtotal', 'Tax Amount', 'Total Amount', 'Status', 'Trip Count']);
            
            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->id,
                    $invoice->invoice_number,
                    $invoice->billing_month,
                    $invoice->invoice_date,
                    $invoice->client_name,
                    $invoice->subtotal,
                    $invoice->tax_amount,
                    $invoice->total_amount,
                    $invoice->status,
                    $invoice->tripLogs->count(),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Mark invoice as verified
     */
    public function markAsVerified(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $validated = $request->validate([
            'verified_by' => 'required|string',
        ]);
        
        $invoice->markAsVerified($validated['verified_by']);
        
        return redirect()->route('invoices.show', $id)
            ->with('success', 'Invoice marked as verified successfully.');
    }
    
    /**
     * Calculate invoice totals from trip logs
     */
    public function calculateTotals(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->calculateTotals();
        
        return redirect()->route('invoices.show', $id)
            ->with('success', 'Invoice totals calculated successfully.');
    }
}
