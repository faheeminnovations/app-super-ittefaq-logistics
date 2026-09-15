<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Driver;
use Illuminate\Support\Facades\Storage;

class DriversController extends Controller
{
    public function index()
    {
        $drivers = Driver::all();
        $totalDrivers = $drivers->count();
        $onDutyDrivers = $drivers->where('status', 'on_duty')->count();
        $onTripDrivers = $drivers->where('status', 'on_trip')->count();
        $onLeaveDrivers = $drivers->where('status', 'on_leave')->count();

        return view('pages.drivers', compact('drivers', 'totalDrivers', 'onDutyDrivers', 'onTripDrivers', 'onLeaveDrivers'));
    }

    public function store(Request $request)
    {
        $hasNewColumns = $this->checkNewColumnsExist();
        
        $validationRules = [
            'name' => 'required|string|max:255',
            'licence_no' => 'nullable|string|max:255',
            'cnic' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'licence_expiry' => 'nullable|date',
        ];
        
        $validated = $request->validate($validationRules);

        $data = $request->except(['_token', 'driver_picture', 'cnic_picture', 'cnic_picture_back', 'license_picture', 'license_picture_back']);

        // Check if new columns exist in database before adding them to data
        $hasNewColumns = $this->checkNewColumnsExist();
        
        // Remove new fields from data if columns don't exist
        if (!$hasNewColumns) {
            unset($data['cnic_picture_back'], $data['license_picture_back']);
        }

        // Handle file uploads
        if ($request->hasFile('driver_picture')) {
            try {
                $file = $request->file('driver_picture');
                $path = $file->store('driver_pictures', 'public');
                $data['driver_picture'] = $path;
                \Log::info('Driver picture uploaded successfully: ' . $path);
            } catch (\Exception $e) {
                \Log::error('Driver picture upload failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Driver picture upload failed: ' . $e->getMessage()], 500);
            }
        }

        if ($request->hasFile('cnic_picture')) {
            try {
                $file = $request->file('cnic_picture');
                $path = $file->store('cnic_pictures', 'public');
                $data['cnic_picture'] = $path;
                \Log::info('CNIC picture uploaded successfully: ' . $path);
            } catch (\Exception $e) {
                \Log::error('CNIC picture upload failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'CNIC picture upload failed: ' . $e->getMessage()], 500);
            }
        }

        if ($hasNewColumns && $request->hasFile('cnic_picture_back')) {
            try {
                $file = $request->file('cnic_picture_back');
                $path = $file->store('cnic_pictures', 'public');
                $data['cnic_picture_back'] = $path;
                \Log::info('CNIC picture back uploaded successfully: ' . $path);
            } catch (\Exception $e) {
                \Log::error('CNIC picture back upload failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'CNIC picture back upload failed: ' . $e->getMessage()], 500);
            }
        }

        if ($request->hasFile('license_picture')) {
            try {
                $file = $request->file('license_picture');
                $path = $file->store('license_pictures', 'public');
                $data['license_picture'] = $path;
                \Log::info('License picture uploaded successfully: ' . $path);
            } catch (\Exception $e) {
                \Log::error('License picture upload failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'License picture upload failed: ' . $e->getMessage()], 500);
            }
        }

        if ($hasNewColumns && $request->hasFile('license_picture_back')) {
            try {
                $file = $request->file('license_picture_back');
                $path = $file->store('license_pictures', 'public');
                $data['license_picture_back'] = $path;
                \Log::info('License picture back uploaded successfully: ' . $path);
            } catch (\Exception $e) {
                \Log::error('License picture back upload failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'License picture back upload failed: ' . $e->getMessage()], 500);
            }
        }

        try {
            \Log::info('Creating driver with data: ', $data);
            $driver = Driver::create($data);
            \Log::info('Driver created successfully with ID: ' . $driver->id);

            // Always return JSON response since this is called via AJAX
            return response()->json(['success' => true, 'message' => 'Driver created successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Validation failed: ' . $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating driver: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Error creating driver: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check if new columns exist in database
     */
    private function checkNewColumnsExist()
    {
        try {
            $columns = \Schema::getColumnListing('drivers');
            return in_array('cnic_picture_back', $columns) && in_array('license_picture_back', $columns);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if new fields exist (for frontend)
     */
    public function checkNewFields()
    {
        return response()->json([
            'has_new_fields' => $this->checkNewColumnsExist()
        ]);
    }

    public function edit($id)
    {
        $driver = Driver::findOrFail($id);
        return response()->json($driver);
    }

    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $hasNewColumns = $this->checkNewColumnsExist();
        
        $validationRules = [
            'name' => 'required|string|max:255',
            'licence_no' => 'nullable|string|max:255',
            'cnic' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'licence_expiry' => 'nullable|date',
        ];
        
        $validated = $request->validate($validationRules);

        $data = $request->except(['_token', '_method', 'driver_picture', 'cnic_picture', 'cnic_picture_back', 'license_picture', 'license_picture_back']);

        // Check if new columns exist in database
        $hasNewColumns = $this->checkNewColumnsExist();
        
        // Remove new fields from data if columns don't exist
        if (!$hasNewColumns) {
            unset($data['cnic_picture_back'], $data['license_picture_back']);
        }

        // Handle file uploads
        if ($request->hasFile('driver_picture')) {
            try {
                // Delete old file if exists
                if ($driver->driver_picture) {
                    Storage::disk('public')->delete($driver->driver_picture);
                }
                $file = $request->file('driver_picture');
                $path = $file->store('driver_pictures', 'public');
                $data['driver_picture'] = $path;
                \Log::info('Driver picture updated successfully: ' . $path);
            } catch (\Exception $e) {
                \Log::error('Driver picture upload failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Driver picture upload failed: ' . $e->getMessage()], 500);
            }
        }

        if ($request->hasFile('cnic_picture')) {
            try {
                // Delete old file if exists
                if ($driver->cnic_picture) {
                    Storage::disk('public')->delete($driver->cnic_picture);
                }
                $file = $request->file('cnic_picture');
                $path = $file->store('cnic_pictures', 'public');
                $data['cnic_picture'] = $path;
                \Log::info('CNIC picture updated successfully: ' . $path);
            } catch (\Exception $e) {
                \Log::error('CNIC picture upload failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'CNIC picture upload failed: ' . $e->getMessage()], 500);
            }
        }

        if ($hasNewColumns && $request->hasFile('cnic_picture_back')) {
            try {
                // Delete old file if exists
                if ($driver->cnic_picture_back) {
                    Storage::disk('public')->delete($driver->cnic_picture_back);
                }
                $file = $request->file('cnic_picture_back');
                $path = $file->store('cnic_pictures', 'public');
                $data['cnic_picture_back'] = $path;
                \Log::info('CNIC picture back updated successfully: ' . $path);
            } catch (\Exception $e) {
                \Log::error('CNIC picture back upload failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'CNIC picture back upload failed: ' . $e->getMessage()], 500);
            }
        }

        if ($request->hasFile('license_picture')) {
            try {
                // Delete old file if exists
                if ($driver->license_picture) {
                    Storage::disk('public')->delete($driver->license_picture);
                }
                $file = $request->file('license_picture');
                $path = $file->store('license_pictures', 'public');
                $data['license_picture'] = $path;
                \Log::info('License picture updated successfully: ' . $path);
            } catch (\Exception $e) {
                \Log::error('License picture upload failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'License picture upload failed: ' . $e->getMessage()], 500);
            }
        }

        if ($hasNewColumns && $request->hasFile('license_picture_back')) {
            try {
                // Delete old file if exists
                if ($driver->license_picture_back) {
                    Storage::disk('public')->delete($driver->license_picture_back);
                }
                $file = $request->file('license_picture_back');
                $path = $file->store('license_pictures', 'public');
                $data['license_picture_back'] = $path;
                \Log::info('License picture back updated successfully: ' . $path);
            } catch (\Exception $e) {
                \Log::error('License picture back upload failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'License picture back upload failed: ' . $e->getMessage()], 500);
            }
        }

        try {
            \Log::info('Updating driver with ID: ' . $driver->id . ' data: ', $data);
            $driver->update($data);
            \Log::info('Driver updated successfully with ID: ' . $driver->id);

            return response()->json(['success' => true, 'message' => 'Driver updated successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error during update: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Validation failed: ' . $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating driver: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Error updating driver: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $driver = Driver::findOrFail($id);
        return response()->json(['driver' => $driver]);
    }

    public function destroy($id)
    {
        $driver = Driver::findOrFail($id);

        // Delete associated files
        if ($driver->driver_picture) {
            Storage::disk('public')->delete($driver->driver_picture);
        }
        if ($driver->cnic_picture) {
            Storage::disk('public')->delete($driver->cnic_picture);
        }
        if ($this->checkNewColumnsExist() && $driver->cnic_picture_back) {
            Storage::disk('public')->delete($driver->cnic_picture_back);
        }
        if ($driver->license_picture) {
            Storage::disk('public')->delete($driver->license_picture);
        }
        if ($this->checkNewColumnsExist() && $driver->license_picture_back) {
            Storage::disk('public')->delete($driver->license_picture_back);
        }

        $driver->delete();

        return response()->json(['success' => true, 'message' => 'Driver deleted successfully']);
    }

    public function export()
    {
        $drivers = Driver::all();
        $csv = fopen('php://temp', 'r+');

        $hasNewColumns = $this->checkNewColumnsExist();
        $headers = ['ID', 'Name', 'Licence No', 'CNIC', 'Category', 'Phone', 'Status', 'Address', 'Licence Expiry', 'CNIC Picture', 'License Picture'];
        
        if ($hasNewColumns) {
            $headers = array_merge($headers, ['CNIC Picture Back', 'License Picture Back']);
        }
        
        fputcsv($csv, $headers);

        foreach ($drivers as $driver) {
            $row = [
                $driver->id,
                $driver->name,
                $driver->licence_no,
                $driver->cnic,
                $driver->category,
                $driver->phone,
                $driver->status,
                $driver->address,
                $driver->licence_expiry,
                $driver->cnic_picture,
                $driver->license_picture,
            ];
            
            if ($hasNewColumns) {
                $row[] = $driver->cnic_picture_back ?? '';
                $row[] = $driver->license_picture_back ?? '';
            }
            
            fputcsv($csv, $row);
        }

        rewind($csv);
        $csvContent = stream_get_contents($csv);
        fclose($csv);

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="drivers_export_' . date('Y-m-d') . '.csv"');
    }
}
