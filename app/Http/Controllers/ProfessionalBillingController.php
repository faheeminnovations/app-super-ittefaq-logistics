<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplyChainBilling;
use App\Models\BrandingBilling;
use App\Models\MarketingDevelopmentBilling;
use App\Models\SprBilling;
use App\Models\CementPakistanBilling;
use App\Models\OpenMarketWorkBilling;
use App\Models\SeedSupplyBilling;
use Carbon\Carbon;

class ProfessionalBillingController extends Controller
{
    // Index page - shows all billing types
    public function index()
    {
        return view('pages.professional-billing');
    }

    // Supply Chain Billing
    public function supplyChainIndex()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $billings = SupplyChainBilling::byMonth($currentMonth)
            ->orderBy('date')
            ->get();
        
        return view('pages.supply-chain-billing', compact('billings', 'currentMonth'));
    }

    public function supplyChainStore(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'vehicle_number' => 'required|string|max:50',
                'delivery_point' => 'required|string|max:255',
                'billing_month' => 'required|date_format:Y-m',
            ]);

            $billing = SupplyChainBilling::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Supply Chain billing record created successfully',
                'billing' => $billing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating record: ' . $e->getMessage()
            ], 500);
        }
    }

    // Branding Billing
    public function brandingIndex()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $billings = BrandingBilling::byMonth($currentMonth)
            ->orderBy('date')
            ->get();
        
        return view('pages.branding-billing', compact('billings', 'currentMonth'));
    }

    public function brandingStore(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'vehicle_number' => 'required|string|max:50',
                'delivery_point' => 'required|string|max:255',
                'billing_month' => 'required|date_format:Y-m',
            ]);

            $billing = BrandingBilling::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Branding billing record created successfully',
                'billing' => $billing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating record: ' . $e->getMessage()
            ], 500);
        }
    }

    // Marketing Development Billing
    public function marketingDevelopmentIndex()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $billings = MarketingDevelopmentBilling::byMonth($currentMonth)
            ->orderBy('date')
            ->get();
        
        return view('pages.marketing-development-billing', compact('billings', 'currentMonth'));
    }

    public function marketingDevelopmentStore(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'vehicle_number' => 'required|string|max:50',
                'delivery_point' => 'required|string|max:255',
                'billing_month' => 'required|date_format:Y-m',
            ]);

            $billing = MarketingDevelopmentBilling::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Marketing Development billing record created successfully',
                'billing' => $billing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating record: ' . $e->getMessage()
            ], 500);
        }
    }

    // SPR Billing
    public function sprIndex()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $billings = SprBilling::byMonth($currentMonth)
            ->orderBy('date')
            ->get();
        
        return view('pages.spr-billing', compact('billings', 'currentMonth'));
    }

    public function sprStore(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'vehicle_number' => 'required|string|max:50',
                'delivery_point' => 'required|string|max:255',
                'billing_month' => 'required|date_format:Y-m',
            ]);

            $billing = SprBilling::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'SPR billing record created successfully',
                'billing' => $billing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating record: ' . $e->getMessage()
            ], 500);
        }
    }

    // Cement Pakistan Billing
    public function cementPakistanIndex()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $billings = CementPakistanBilling::byMonth($currentMonth)
            ->orderBy('date')
            ->get();
        
        return view('pages.cement-pakistan-billing', compact('billings', 'currentMonth'));
    }

    public function cementPakistanStore(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'vehicle_number' => 'required|string|max:50',
                'delivery_point' => 'required|string|max:255',
                'billing_month' => 'required|date_format:Y-m',
            ]);

            $billing = CementPakistanBilling::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Cement Pakistan billing record created successfully',
                'billing' => $billing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating record: ' . $e->getMessage()
            ], 500);
        }
    }

    // Open Market Work Billing
    public function openMarketWorkIndex()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $billings = OpenMarketWorkBilling::byMonth($currentMonth)
            ->orderBy('date')
            ->get();
        
        return view('pages.open-market-work-billing', compact('billings', 'currentMonth'));
    }

    public function openMarketWorkStore(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'vehicle_number' => 'required|string|max:50',
                'customer_name' => 'required|string|max:255',
                'loading_point' => 'required|string|max:255',
                'billing_month' => 'required|date_format:Y-m',
            ]);

            $billing = OpenMarketWorkBilling::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Open Market Work billing record created successfully',
                'billing' => $billing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating record: ' . $e->getMessage()
            ], 500);
        }
    }

    // Seed Supply Billing
    public function seedSupplyIndex()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $billings = SeedSupplyBilling::byMonth($currentMonth)
            ->orderBy('date')
            ->get();
        
        return view('pages.seed-supply-billing', compact('billings', 'currentMonth'));
    }

    public function seedSupplyStore(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'vehicle_number' => 'required|string|max:50',
                'delivery_point' => 'required|string|max:255',
                'billing_month' => 'required|date_format:Y-m',
            ]);

            $billing = SeedSupplyBilling::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Seed Supply billing record created successfully',
                'billing' => $billing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating record: ' . $e->getMessage()
            ], 500);
        }
    }

    // Generic CRUD operations for all types
    public function update(Request $request, $type, $id)
    {
        try {
            $model = $this->getModelByType($type);
            $billing = $model::findOrFail($id);
            $billing->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully',
                'billing' => $billing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating record: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($type, $id)
    {
        try {
            $model = $this->getModelByType($type);
            $billing = $model::findOrFail($id);
            $billing->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting record: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getModelByType($type)
    {
        $models = [
            'supply-chain' => SupplyChainBilling::class,
            'branding' => BrandingBilling::class,
            'marketing-development' => MarketingDevelopmentBilling::class,
            'spr' => SprBilling::class,
            'cement-pakistan' => CementPakistanBilling::class,
            'open-market-work' => OpenMarketWorkBilling::class,
            'seed-supply' => SeedSupplyBilling::class,
        ];

        return $models[$type] ?? SupplyChainBilling::class;
    }
}
