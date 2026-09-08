<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::paginate(15);
        $allVehicles = Vehicle::all();

        return view('pages.vehicles', [
            'vehicles' => $vehicles,
            'totalVehicles' => $allVehicles->count(),
            'availableVehicles' => $allVehicles->where('status', 'available')->count(),
            'onTripVehicles' => $allVehicles->where('status', 'on_trip')->count(),
            'maintenanceVehicles' => $allVehicles->where('status', 'maintenance')->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.vehicles-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'reg_no' => 'required|string|max:20|unique:vehicles,reg_no',
                'type' => 'required|string|max:50',
                'make_model' => 'nullable|string|max:100',
                'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
                'fitness_certificate_expiry' => 'nullable|date',
                'status' => 'nullable|in:available,on_trip,maintenance,out_of_service',
                'work_type' => 'nullable|in:company,private,both',
                'fuel_capacity' => 'nullable|numeric|min:0',
                'vin' => 'nullable|string|max:50',
                'notes' => 'nullable|string',
                'vehicle_category' => 'nullable|string|max:50',
                'route_permit' => 'nullable|date',
                'token_tax' => 'nullable|date',
                'insurance' => 'nullable|date',
            ]);

            // Remove null values to let database defaults apply
            $validated = array_filter($validated, function($value) {
                return $value !== null && $value !== '';
            });

            Vehicle::create($validated);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Vehicle created successfully.']);
            }

            return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error creating vehicle: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error creating vehicle: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['vehicle' => $vehicle]);
        }
        
        return view('pages.vehicles-show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($vehicle);
        }
        
        return view('pages.vehicles-edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        try {
            $validated = $request->validate([
                'reg_no' => 'required|string|max:20|unique:vehicles,reg_no,' . $id,
                'type' => 'required|string|max:50',
                'make_model' => 'nullable|string|max:100',
                'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
                'fitness_certificate_expiry' => 'nullable|date',
                'status' => 'nullable|in:available,on_trip,maintenance,out_of_service',
                'work_type' => 'nullable|in:company,private,both',
                'fuel_capacity' => 'nullable|numeric|min:0',
                'vin' => 'nullable|string|max:50',
                'notes' => 'nullable|string',
                'vehicle_category' => 'nullable|string|max:50',
                'route_permit' => 'nullable|date',
                'token_tax' => 'nullable|date',
                'insurance' => 'nullable|date',
            ]);

            // Remove null values to let database defaults apply
            $validated = array_filter($validated, function($value) {
                return $value !== null && $value !== '';
            });

            $vehicle->update($validated);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Vehicle updated successfully.']);
            }

            return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error updating vehicle: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error updating vehicle: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // Check if vehicle has associated trips
        if ($vehicle->trips()->exists()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete vehicle with associated trips. Please delete the trips first.'], 400);
            }
            return redirect()->route('vehicles.index')->with('error', 'Cannot delete vehicle with associated trips. Please delete the trips first.');
        }

        // Check if vehicle has associated maintenance records
        if ($vehicle->maintenance()->exists()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete vehicle with associated maintenance records.'], 400);
            }
            return redirect()->route('vehicles.index')->with('error', 'Cannot delete vehicle with associated maintenance records.');
        }

        try {
            $vehicle->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Vehicle deleted successfully.']);
            }

            return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete vehicle due to database constraints.'], 400);
            }
            return redirect()->route('vehicles.index')->with('error', 'Cannot delete vehicle due to database constraints.');
        }
    }

    public function export(Request $request)
    {
        $vehicles = Vehicle::all();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vehicles_export_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($vehicles) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Vehicle Number', 'Make/Model', 'Year', 'Type', 'Status', 'Fuel Type', 'Capacity', 'Current Mileage']);
            
            foreach ($vehicles as $vehicle) {
                fputcsv($file, [
                    $vehicle->id,
                    $vehicle->vehicle_number,
                    $vehicle->make_model,
                    $vehicle->year,
                    $vehicle->type,
                    $vehicle->status,
                    $vehicle->fuel_type,
                    $vehicle->capacity,
                    $vehicle->current_mileage,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
