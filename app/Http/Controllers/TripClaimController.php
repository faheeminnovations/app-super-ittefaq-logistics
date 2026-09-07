<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TripClaim;
use Carbon\Carbon;

class TripClaimController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $claims = TripClaim::byMonth($currentMonth)
            ->orderBy('trip_date')
            ->get();
        
        return view('pages.trip-claims', compact('claims', 'currentMonth'));
    }

    public function show($id)
    {
        try {
            $claim = TripClaim::findOrFail($id);
            return response()->json($claim);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Claim not found'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'trip_date' => 'required|date',
                'vehicle_number' => 'required|string|max:50',
                'route_from' => 'required|string|max:255',
                'route_to' => 'required|string|max:255',
                'agreed_amount' => 'required|numeric|min:0',
                'billing_month' => 'required|date_format:Y-m',
            ]);

            $claim = TripClaim::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Trip claim created successfully',
                'claim' => $claim
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating claim: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'trip_date' => 'required|date',
                'vehicle_number' => 'required|string|max:50',
                'route_from' => 'required|string|max:255',
                'route_to' => 'required|string|max:255',
                'agreed_amount' => 'required|numeric|min:0',
                'billing_month' => 'required|date_format:Y-m',
            ]);

            $claim = TripClaim::findOrFail($id);
            $claim->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Trip claim updated successfully',
                'claim' => $claim
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating claim: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $claim = TripClaim::findOrFail($id);
            $claim->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Trip claim deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting claim: ' . $e->getMessage()
            ], 500);
        }
    }

    public function filter(Request $request)
    {
        try {
            $query = TripClaim::query();
            
            if (!empty($request->billing_month)) {
                $query->byMonth($request->billing_month);
            }
            
            if (!empty($request->vehicle_number)) {
                $query->byVehicle($request->vehicle_number);
            }
            
            if (!empty($request->status) && $request->status !== 'all') {
                $query->byStatus($request->status);
            }
            
            $claims = $query->orderBy('trip_date')->get();
            
            return response()->json([
                'claims' => $claims
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error filtering claims: ' . $e->getMessage()
            ], 500);
        }
    }
}
