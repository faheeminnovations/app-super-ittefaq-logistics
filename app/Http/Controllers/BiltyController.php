<?php

namespace App\Http\Controllers;

use App\Models\Bilty;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Job;
use Illuminate\Http\Request;

class BiltyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bilties = Bilty::with(['customer', 'vehicle', 'driver', 'job'])->paginate(15);
        $allBilties = Bilty::all();
        $customers = Customer::all();
        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        $jobs = Job::where('status', '!=', 'delivered')->get();

        return view('pages.bilties', [
            'bilties' => $bilties,
            'totalBilties' => $allBilties->count(),
            'pendingBilties' => $allBilties->where('status', 'pending')->count(),
            'inTransitBilties' => $allBilties->where('status', 'in_transit')->count(),
            'deliveredBilties' => $allBilties->where('status', 'delivered')->count(),
            'cancelledBilties' => $allBilties->where('status', 'cancelled')->count(),
            'customers' => $customers,
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'jobs' => $jobs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::where('status', 'active')->get();
        $vehicles = Vehicle::where('status', 'available')->get();
        $drivers = Driver::where('status', 'on_duty')->get();
        $jobs = Job::where('status', '!=', 'delivered')->get();

        return view('pages.bilties-create', compact('customers', 'vehicles', 'drivers', 'jobs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'bilty_date' => 'required|date',
                'from_location' => 'required|string|max:255',
                'to_location' => 'required|string|max:255',
                'vehicle_number' => 'nullable|string|max:50',
                'driver_name' => 'nullable|string|max:255',
                'card_number' => 'nullable|string|max:50',
                'driver_phone' => 'nullable|string|max:20',
                'sender_name' => 'required|string|max:255',
                'sender_phone' => 'nullable|string|max:20',
                'receiver_name' => 'required|string|max:255',
                'receiver_phone' => 'nullable|string|max:20',
                'goods_description' => 'required|string',
                'quantity' => 'nullable|integer|min:0',
                'quantity_unit' => 'nullable|string|max:50',
                'total_amount' => 'required|numeric|min:0',
                'advance_amount' => 'nullable|numeric|min:0',
                'rent_amount' => 'nullable|numeric|min:0',
                'scale' => 'nullable|numeric|min:0',
                'status' => 'required|in:pending,in_transit,delivered,cancelled',
                'notes' => 'nullable|string',
                'customer_id' => 'nullable|exists:customers,id',
                'vehicle_id' => 'nullable|exists:vehicles,id',
                'driver_id' => 'nullable|exists:drivers,id',
                'job_id' => 'nullable|exists:transport_jobs,id',
                'registration_number' => 'nullable|string|max:50',
                'contact_details' => 'nullable|string',
            ]);

            // Auto-generate bilty number
            $validated['bilty_number'] = Bilty::generateBiltyNumber();

            // Calculate remaining balance
            $advanceAmount = $validated['advance_amount'] ?? 0;
            $validated['remaining_balance'] = $validated['total_amount'] - $advanceAmount;

            Bilty::create($validated);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Bilty created successfully.', 'bilty' => $validated]);
            }

            return redirect()->route('bilties.index')->with('success', 'Bilty created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error creating bilty: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error creating bilty: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($bilty)
    {
        $biltyData = Bilty::with(['customer', 'vehicle', 'driver', 'job'])->findOrFail($bilty);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'bilty' => $biltyData,
                'success' => true
            ], 200);
        }
        
        return view('pages.bilties-show', ['bilty' => $biltyData]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($bilty)
    {
        $biltyData = Bilty::with(['customer', 'vehicle', 'driver', 'job'])->findOrFail($bilty);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['bilty' => $biltyData, 'success' => true], 200);
        }
        
        $customers = Customer::all();
        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        $jobs = Job::all();

        return view('pages.bilties-edit', ['bilty' => $biltyData, 'customers' => $customers, 'vehicles' => $vehicles, 'drivers' => $drivers, 'jobs' => $jobs]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $bilty)
    {
        $biltyData = Bilty::findOrFail($bilty);

        try {
            $validated = $request->validate([
                'bilty_number' => 'nullable|string|unique:bilties,bilty_number,' . $bilty . '|max:50',
                'bilty_date' => 'required|date',
                'from_location' => 'required|string|max:255',
                'to_location' => 'required|string|max:255',
                'vehicle_number' => 'nullable|string|max:50',
                'driver_name' => 'nullable|string|max:255',
                'card_number' => 'nullable|string|max:50',
                'driver_phone' => 'nullable|string|max:20',
                'sender_name' => 'required|string|max:255',
                'sender_phone' => 'nullable|string|max:20',
                'receiver_name' => 'required|string|max:255',
                'receiver_phone' => 'nullable|string|max:20',
                'goods_description' => 'required|string',
                'quantity' => 'nullable|integer|min:0',
                'quantity_unit' => 'nullable|string|max:50',
                'total_amount' => 'required|numeric|min:0',
                'advance_amount' => 'nullable|numeric|min:0',
                'rent_amount' => 'nullable|numeric|min:0',
                'scale' => 'nullable|numeric|min:0',
                'status' => 'required|in:pending,in_transit,delivered,cancelled',
                'notes' => 'nullable|string',
                'customer_id' => 'nullable|exists:customers,id',
                'vehicle_id' => 'nullable|exists:vehicles,id',
                'driver_id' => 'nullable|exists:drivers,id',
                'job_id' => 'nullable|exists:transport_jobs,id',
                'registration_number' => 'nullable|string|max:50',
                'contact_details' => 'nullable|string',
            ]);

            // Recalculate remaining balance
            $advanceAmount = $validated['advance_amount'] ?? 0;
            $validated['remaining_balance'] = $validated['total_amount'] - $advanceAmount;

            $biltyData->update($validated);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Bilty updated successfully.']);
            }

            return redirect()->route('bilties.index')->with('success', 'Bilty updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error updating bilty: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error updating bilty: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($bilty)
    {
        $biltyData = Bilty::findOrFail($bilty);
        $biltyData->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Bilty deleted successfully.']);
        }

        return redirect()->route('bilties.index')->with('success', 'Bilty deleted successfully.');
    }

    /**
     * Export bilties to CSV
     */
    public function export(Request $request)
    {
        $bilties = Bilty::with(['customer', 'vehicle', 'driver', 'job'])->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bilties_export_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($bilties) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Bilty Number', 'Date', 'From', 'To', 'Sender', 'Receiver', 'Vehicle', 'Driver', 'Goods', 'Quantity', 'Total Amount', 'Advance', 'Balance', 'Status']);
            
            foreach ($bilties as $bilty) {
                fputcsv($file, [
                    $bilty->id,
                    $bilty->bilty_number,
                    $bilty->bilty_date,
                    $bilty->from_location,
                    $bilty->to_location,
                    $bilty->sender_name,
                    $bilty->receiver_name,
                    $bilty->vehicle_number,
                    $bilty->driver_name,
                    $bilty->goods_description,
                    $bilty->quantity,
                    $bilty->total_amount,
                    $bilty->advance_amount,
                    $bilty->remaining_balance,
                    $bilty->status,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Print traditional bilty receipt
     */
    public function print($bilty)
    {
        $biltyData = Bilty::with(['customer', 'vehicle', 'driver', 'job'])->findOrFail($bilty);
        return view('pages.bilties-print', ['bilty' => $biltyData]);
    }
}
