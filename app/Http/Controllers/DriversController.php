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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'licence_no' => 'nullable|string|max:255',
            'cnic' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'licence_expiry' => 'nullable|date',
            'driver_picture' => 'nullable|image|max:2048',
            'cnic_picture' => 'nullable|image|max:2048',
            'cnic_picture_back' => 'nullable|image|max:2048',
            'license_picture' => 'nullable|image|max:2048',
            'license_picture_back' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['_token', 'driver_picture', 'cnic_picture', 'cnic_picture_back', 'license_picture', 'license_picture_back']);

        // Handle file uploads
        if ($request->hasFile('driver_picture')) {
            $file = $request->file('driver_picture');
            $path = $file->store('driver_pictures', 'public');
            $data['driver_picture'] = $path;
        }

        if ($request->hasFile('cnic_picture')) {
            $file = $request->file('cnic_picture');
            $path = $file->store('cnic_pictures', 'public');
            $data['cnic_picture'] = $path;
        }

        if ($request->hasFile('cnic_picture_back')) {
            $file = $request->file('cnic_picture_back');
            $path = $file->store('cnic_pictures', 'public');
            $data['cnic_picture_back'] = $path;
        }

        if ($request->hasFile('license_picture')) {
            $file = $request->file('license_picture');
            $path = $file->store('license_pictures', 'public');
            $data['license_picture'] = $path;
        }

        if ($request->hasFile('license_picture_back')) {
            $file = $request->file('license_picture_back');
            $path = $file->store('license_pictures', 'public');
            $data['license_picture_back'] = $path;
        }

        Driver::create($data);

        return redirect()->route('drivers.index')->with('success', 'Driver created successfully.');
    }

    public function edit($id)
    {
        $driver = Driver::findOrFail($id);
        return response()->json($driver);
    }

    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'licence_no' => 'nullable|string|max:255',
            'cnic' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'licence_expiry' => 'nullable|date',
            'driver_picture' => 'nullable|image|max:2048',
            'cnic_picture' => 'nullable|image|max:2048',
            'cnic_picture_back' => 'nullable|image|max:2048',
            'license_picture' => 'nullable|image|max:2048',
            'license_picture_back' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['_token', '_method', 'driver_picture', 'cnic_picture', 'cnic_picture_back', 'license_picture', 'license_picture_back']);

        // Handle file uploads
        if ($request->hasFile('driver_picture')) {
            // Delete old file if exists
            if ($driver->driver_picture) {
                Storage::disk('public')->delete($driver->driver_picture);
            }
            $file = $request->file('driver_picture');
            $path = $file->store('driver_pictures', 'public');
            $data['driver_picture'] = $path;
        }

        if ($request->hasFile('cnic_picture')) {
            // Delete old file if exists
            if ($driver->cnic_picture) {
                Storage::disk('public')->delete($driver->cnic_picture);
            }
            $file = $request->file('cnic_picture');
            $path = $file->store('cnic_pictures', 'public');
            $data['cnic_picture'] = $path;
        }

        if ($request->hasFile('cnic_picture_back')) {
            // Delete old file if exists
            if ($driver->cnic_picture_back) {
                Storage::disk('public')->delete($driver->cnic_picture_back);
            }
            $file = $request->file('cnic_picture_back');
            $path = $file->store('cnic_pictures', 'public');
            $data['cnic_picture_back'] = $path;
        }

        if ($request->hasFile('license_picture')) {
            // Delete old file if exists
            if ($driver->license_picture) {
                Storage::disk('public')->delete($driver->license_picture);
            }
            $file = $request->file('license_picture');
            $path = $file->store('license_pictures', 'public');
            $data['license_picture'] = $path;
        }

        if ($request->hasFile('license_picture_back')) {
            // Delete old file if exists
            if ($driver->license_picture_back) {
                Storage::disk('public')->delete($driver->license_picture_back);
            }
            $file = $request->file('license_picture_back');
            $path = $file->store('license_pictures', 'public');
            $data['license_picture_back'] = $path;
        }

        $driver->update($data);

        return response()->json(['success' => true, 'message' => 'Driver updated successfully']);
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
        if ($driver->cnic_picture_back) {
            Storage::disk('public')->delete($driver->cnic_picture_back);
        }
        if ($driver->license_picture) {
            Storage::disk('public')->delete($driver->license_picture);
        }
        if ($driver->license_picture_back) {
            Storage::disk('public')->delete($driver->license_picture_back);
        }

        $driver->delete();

        return response()->json(['success' => true, 'message' => 'Driver deleted successfully']);
    }

    public function export()
    {
        $drivers = Driver::all();
        $csv = fopen('php://temp', 'r+');

        fputcsv($csv, ['ID', 'Name', 'Licence No', 'CNIC', 'Category', 'Phone', 'Status', 'Address', 'Licence Expiry', 'CNIC Picture', 'CNIC Picture Back', 'License Picture', 'License Picture Back']);

        foreach ($drivers as $driver) {
            fputcsv($csv, [
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
                $driver->cnic_picture_back,
                $driver->license_picture,
                $driver->license_picture_back,
            ]);
        }

        rewind($csv);
        $csvContent = stream_get_contents($csv);
        fclose($csv);

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="drivers_export_' . date('Y-m-d') . '.csv"');
    }
}
