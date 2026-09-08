<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drivers = Driver::paginate(15);
        $allDrivers = Driver::all();

        return view('pages.drivers', [
            'drivers' => $drivers,
            'totalDrivers' => $allDrivers->count(),
            'onDutyDrivers' => $allDrivers->where('status', 'on_duty')->count(),
            'onTripDrivers' => $allDrivers->where('status', 'on_trip')->count(),
            'onLeaveDrivers' => $allDrivers->where('status', 'on_leave')->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.drivers-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'licence_no' => 'nullable|string|max:50',
                'category' => 'nullable|in:Bike,Car/Jeep,LTV,LTVPSV,HTV,HTVPSV',
                'phone' => 'nullable|string|max:20',
                'status' => 'nullable|in:on_trip,on_duty,on_leave,suspended,licence_expired',
                'address' => 'nullable|string',
                'licence_expiry' => 'nullable|date',
            ];

            // Add unique validation for licence_no only if it's provided
            if ($request->filled('licence_no')) {
                $rules['licence_no'] .= '|unique:drivers,licence_no';
            }

            $validated = $request->validate($rules);

            // Remove null values to let database defaults apply
            $validated = array_filter($validated, function($value) {
                return $value !== null && $value !== '';
            });

            Driver::create($validated);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Driver created successfully.']);
            }

            return redirect()->route('drivers.index')->with('success', 'Driver created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error creating driver: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error creating driver: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $driver = Driver::findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['driver' => $driver]);
        }
        
        return view('pages.drivers-show', compact('driver'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $driver = Driver::findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($driver);
        }
        
        return view('pages.drivers-edit', compact('driver'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $driver = Driver::findOrFail($id);

        try {
            $rules = [
                'name' => 'required|string|max:255',
                'licence_no' => 'nullable|string|max:50',
                'category' => 'nullable|in:Bike,Car/Jeep,LTV,LTVPSV,HTV,HTVPSV',
                'phone' => 'nullable|string|max:20',
                'status' => 'nullable|in:on_trip,on_duty,on_leave,suspended,licence_expired',
                'address' => 'nullable|string',
                'licence_expiry' => 'nullable|date',
            ];

            // Add unique validation for licence_no only if it's provided
            if ($request->filled('licence_no')) {
                $rules['licence_no'] .= '|unique:drivers,licence_no,' . $id;
            }

            $validated = $request->validate($rules);

            // Remove null values to let database defaults apply
            $validated = array_filter($validated, function($value) {
                return $value !== null && $value !== '';
            });

            $driver->update($validated);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Driver updated successfully.']);
            }

            return redirect()->route('drivers.index')->with('success', 'Driver updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error updating driver: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error updating driver: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $driver = Driver::findOrFail($id);

        // Check if driver has associated trips
        if ($driver->trips()->exists()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete driver with associated trips. Please delete the trips first.'], 400);
            }
            return redirect()->route('drivers.index')->with('error', 'Cannot delete driver with associated trips. Please delete the trips first.');
        }

        try {
            $driver->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Driver deleted successfully.']);
            }

            return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete driver due to database constraints.'], 400);
            }
            return redirect()->route('drivers.index')->with('error', 'Cannot delete driver due to database constraints.');
        }
    }

    public function export(Request $request)
    {
        $drivers = Driver::all();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="drivers_export_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($drivers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Phone', 'License Number', 'Licence Type', 'License Expiry', 'Status', 'Address']);
            
            foreach ($drivers as $driver) {
                fputcsv($file, [
                    $driver->id,
                    $driver->name,
                    $driver->phone,
                    $driver->licence_no,
                    $driver->category,
                    $driver->licence_expiry,
                    $driver->status,
                    $driver->address,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
