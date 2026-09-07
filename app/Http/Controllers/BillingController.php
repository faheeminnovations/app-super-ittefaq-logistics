<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billing;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Services\NotificationService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BillingController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        try {
            $currentMonth = Carbon::now()->format('Y-m');
            $billings = Billing::byMonth($currentMonth)
                ->orderBy('sr')
                ->get();
            
            $vehicles = Vehicle::all();
            $customers = Customer::all();
            
            // Calculate summary statistics with fallback values
            $totalRent = $billings->sum('rent') ?? 0;
            $totalAdvance = $billings->sum('advance') ?? 0;
            $totalDues = $billings->sum('dues') ?? 0;
            $paidCount = $billings->where('status', 'Paid')->count() ?? 0;
            $pendingCount = $billings->where('status', 'Pending')->count() ?? 0;
            $partialCount = $billings->where('status', 'Partial')->count() ?? 0;
            
            return view('pages.billing', compact(
                'billings', 'vehicles', 'customers', 'currentMonth',
                'totalRent', 'totalAdvance', 'totalDues',
                'paidCount', 'pendingCount', 'partialCount'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading billing data: ' . $e->getMessage());
        }
    }
    
    public function filter(Request $request)
    {
        try {
            $query = Billing::query();
            
            // Filter by billing month
            if (!empty($request->billing_month)) {
                $query->byMonth($request->billing_month);
            }
            
            // Filter by vehicle
            if (!empty($request->vehicle_no)) {
                $query->byVehicle($request->vehicle_no);
            }
            
            // Filter by customer
            if (!empty($request->customer_name)) {
                $query->byCustomer($request->customer_name);
            }
            
            // Filter by status
            if (!empty($request->status) && $request->status !== 'all') {
                $query->byStatus($request->status);
            }
            
            $billings = $query->orderBy('sr')->get();
            
            // Calculate summary statistics
            $totalRent = $billings->sum('rent') ?? 0;
            $totalAdvance = $billings->sum('advance') ?? 0;
            $totalDues = $billings->sum('dues') ?? 0;
            $paidCount = $billings->where('status', 'Paid')->count() ?? 0;
            $pendingCount = $billings->where('status', 'Pending')->count() ?? 0;
            $partialCount = $billings->where('status', 'Partial')->count() ?? 0;
            
            return response()->json([
                'billings' => $billings,
                'totalRent' => $totalRent,
                'totalAdvance' => $totalAdvance,
                'totalDues' => $totalDues,
                'paidCount' => $paidCount,
                'pendingCount' => $pendingCount,
                'partialCount' => $partialCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error filtering billings: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'sr' => 'nullable|integer',
                'date' => 'required|date',
                'vehicle_no' => 'required|string|max:50',
                'gp_number' => 'nullable|string|max:50',
                'customer_name' => 'required|string|max:255',
                'contact_number' => 'nullable|string|max:20',
                'bags' => 'nullable|integer',
                'delivery_point' => 'required|string|max:255',
                'vehicle_type' => 'nullable|string|max:20',
                'km_covered' => 'nullable|numeric',
                'rate' => 'nullable|numeric',
                'rent' => 'required|numeric|min:0',
                'freight' => 'nullable|numeric',
                'advance' => 'nullable|numeric',
                'advance_date' => 'nullable|date',
                'guarantor' => 'nullable|string|max:255',
                'dues' => 'nullable|numeric',
                'status' => 'required|in:Pending,Paid,Partial',
                'billing_month' => 'required|date_format:Y-m'
            ]);
            
            // Prepare data with proper formatting
            $billingData = $request->all();
            $billingData['vehicle_no'] = strtoupper($billingData['vehicle_no'] ?? '');
            $billingData['customer_name'] = ucwords(strtolower($billingData['customer_name'] ?? ''));
            $billingData['delivery_point'] = ucwords(strtolower($billingData['delivery_point'] ?? ''));
            if (!empty($billingData['guarantor'])) {
                $billingData['guarantor'] = ucwords(strtolower($billingData['guarantor']));
            }
            
            $billing = Billing::create($billingData);
            
            // Create notification for billing record creation
            $this->notificationService->createNotification(
                auth()->id(),
                'save',
                'New Billing Record Created',
                "Billing record #{$billing->sr} for {$billing->customer_name} ({$billing->vehicle_no}) has been created successfully.",
                'billing',
                [
                    'billing_id' => $billing->id,
                    'sr' => $billing->sr,
                    'customer_name' => $billing->customer_name,
                    'vehicle_no' => $billing->vehicle_no,
                    'rent' => $billing->rent,
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Billing record created successfully',
                'billing' => $billing
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . json_encode($e->errors()),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating billing record: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'sr' => 'nullable|integer',
                'date' => 'required|date',
                'vehicle_no' => 'required|string|max:50',
                'gp_number' => 'nullable|string|max:50',
                'customer_name' => 'required|string|max:255',
                'contact_number' => 'nullable|string|max:20',
                'bags' => 'nullable|integer',
                'delivery_point' => 'required|string|max:255',
                'vehicle_type' => 'nullable|string|max:20',
                'km_covered' => 'nullable|numeric',
                'rate' => 'nullable|numeric',
                'rent' => 'required|numeric|min:0',
                'freight' => 'nullable|numeric',
                'advance' => 'nullable|numeric',
                'advance_date' => 'nullable|date',
                'guarantor' => 'nullable|string|max:255',
                'dues' => 'nullable|numeric',
                'status' => 'required|in:Pending,Paid,Partial',
                'billing_month' => 'required|date_format:Y-m'
            ]);
            
            $billing = Billing::findOrFail($id);
            
            // Prepare data with proper formatting
            $billingData = $request->all();
            $billingData['vehicle_no'] = strtoupper($billingData['vehicle_no'] ?? '');
            $billingData['customer_name'] = ucwords(strtolower($billingData['customer_name'] ?? ''));
            $billingData['delivery_point'] = ucwords(strtolower($billingData['delivery_point'] ?? ''));
            if (!empty($billingData['guarantor'])) {
                $billingData['guarantor'] = ucwords(strtolower($billingData['guarantor']));
            }
            
            $billing->update($billingData);
            
            // Create notification for billing record update
            $this->notificationService->createNotification(
                auth()->id(),
                'update',
                'Billing Record Updated',
                "Billing record #{$billing->sr} for {$billing->customer_name} ({$billing->vehicle_no}) has been updated.",
                'billing',
                [
                    'billing_id' => $billing->id,
                    'sr' => $billing->sr,
                    'customer_name' => $billing->customer_name,
                    'vehicle_no' => $billing->vehicle_no,
                    'rent' => $billing->rent,
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Billing record updated successfully',
                'billing' => $billing
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Billing record not found'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . json_encode($e->errors()),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating billing record: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $billing = Billing::findOrFail($id);
            
            // Store billing info for notification before deletion
            $billingInfo = [
                'billing_id' => $billing->id,
                'sr' => $billing->sr,
                'customer_name' => $billing->customer_name,
                'vehicle_no' => $billing->vehicle_no,
                'rent' => $billing->rent,
            ];
            
            $billing->delete();
            
            // Create notification for billing record deletion
            $this->notificationService->createNotification(
                auth()->id(),
                'delete',
                'Billing Record Deleted',
                "Billing record #{$billingInfo['sr']} for {$billingInfo['customer_name']} ({$billingInfo['vehicle_no']}) has been deleted.",
                'billing',
                $billingInfo
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Billing record deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Billing record not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting billing record: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function show($id)
    {
        try {
            $billing = Billing::findOrFail($id);
            return response()->json($billing);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Billing record not found'
            ], 404);
        }
    }
    
    public function export(Request $request)
    {
        try {
            $billingMonth = $request->get('month', Carbon::now()->format('Y-m'));
            
            $billings = Billing::where('billing_month', $billingMonth)
                ->orderBy('sr')
                ->get();
            
            $csvContent = "Sr,Date,Vhl No,Name,Number,Bag,Drop/Delivery Point,Km Cover,Rent,Advance,Advance Date,Guarantor,Dues,Status\n";
            
            foreach ($billings as $billing) {
                $sr = $billing->sr ?? '';
                $dateStr = $billing->date ? $billing->date->format('d-M-y') : '';
                $vehicleNo = strtoupper($billing->vehicle_no ?? '');
                $customerName = $billing->customer_name ?? '';
                $contactNumber = $billing->contact_number ?? '';
                $bags = $billing->bags ?? 0;
                $deliveryPoint = $billing->delivery_point ?? '';
                $kmCovered = $billing->km_covered ?? 0;
                $rent = $billing->rent ?? 0;
                $advance = $billing->advance ?? 0;
                $advanceDateStr = $billing->advance_date ? $billing->advance_date->format('d-M-y') : '';
                $guarantor = $billing->guarantor ?? '';
                $dues = $billing->dues ?? 0;
                $status = $billing->status ?? 'Pending';
                
                $csvContent .= "{$sr},{$dateStr},{$vehicleNo},{$customerName},{$contactNumber},{$bags},{$deliveryPoint},{$kmCovered},{$rent},{$advance},{$advanceDateStr},{$guarantor},{$dues},{$status}\n";
            }
            
            $fileName = "billing_export_{$billingMonth}.csv";
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"")
                ->header('Cache-Control', 'no-cache, must-revalidate')
                ->header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error exporting billing data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function showMonthlySummary(Request $request)
    {
        try {
            $billingMonth = $request->get('month', Carbon::now()->format('Y-m'));
            
            $billings = Billing::byMonth($billingMonth)
                ->orderBy('sr')
                ->get();
            
            // Calculate monthly summary
            $summary = [
                'total_records' => $billings->count(),
                'total_rent' => $billings->sum('rent') ?? 0,
                'total_advance' => $billings->sum('advance') ?? 0,
                'total_dues' => $billings->sum('dues') ?? 0,
                'total_bags' => $billings->sum('bags') ?? 0,
                'total_km' => $billings->sum('km_covered') ?? 0,
                'paid_count' => $billings->where('status', 'Paid')->count() ?? 0,
                'pending_count' => $billings->where('status', 'Pending')->count() ?? 0,
                'partial_count' => $billings->where('status', 'Partial')->count() ?? 0,
                'pending_dues' => $billings->where('status', 'Pending')->sum('dues') ?? 0,
            ];
            
            // Group by customer
            $byCustomer = $billings->groupBy('customer_name')->map(function ($group) {
                return [
                    'total_trips' => $group->count(),
                    'total_rent' => $group->sum('rent') ?? 0,
                    'total_dues' => $group->sum('dues') ?? 0,
                    'pending_records' => $group->where('status', 'Pending')->count()
                ];
            });
            
            // Group by vehicle
            $byVehicle = $billings->groupBy('vehicle_no')->map(function ($group) {
                return [
                    'total_trips' => $group->count(),
                    'total_rent' => $group->sum('rent') ?? 0,
                    'total_km' => $group->sum('km_covered') ?? 0
                ];
            });
            
            return response()->json([
                'summary' => $summary,
                'by_customer' => $byCustomer,
                'by_vehicle' => $byVehicle,
                'billings' => $billings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error generating monthly summary: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateInvoice(Request $request)
    {
        try {
            $billingMonth = $request->get('month', Carbon::now()->format('Y-m'));
            $invoiceNumber = $request->get('invoice_number', '0000');

            $billings = Billing::byMonth($billingMonth)
                ->orderBy('sr')
                ->get();

            if ($billings->isEmpty()) {
                return response()->json([
                    'error' => 'No billing records found for the selected month'
                ], 404);
            }

            // Calculate totals
            $totalFreight = $billings->sum('freight') ?? 0;
            $totalKm = $billings->sum('km_covered') ?? 0;

            return view('pages.invoice', compact(
                'billings',
                'billingMonth',
                'invoiceNumber',
                'totalFreight',
                'totalKm'
            ));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error generating invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importExcel(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240'
            ]);

            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header rows (first 7 rows based on Excel structure)
            $dataRows = array_slice($rows, 7);

            $importedCount = 0;
            $errors = [];

            foreach ($dataRows as $rowIndex => $row) {
                try {
                    // Skip empty rows
                    if (empty($row[0]) && empty($row[1])) {
                        continue;
                    }

                    $billingData = [
                        'sr' => $row[0] ?? null,
                        'date' => !empty($row[1]) ? Carbon::parse($row[1])->format('Y-m-d') : null,
                        'vehicle_no' => $row[2] ?? null,
                        'gp_number' => $row[3] ?? null,
                        'delivery_point' => $row[4] ?? null,
                        'vehicle_type' => $row[5] ?? null,
                        'km_covered' => $row[6] ?? 0,
                        'rate' => $row[7] ?? 0,
                        'freight' => $row[8] ?? 0,
                        'rent' => $row[8] ?? 0, // Freight as rent
                        'customer_name' => 'Imported from Excel',
                        'billing_month' => Carbon::now()->format('Y-m'),
                        'status' => 'Pending'
                    ];

                    if (!empty($billingData['date']) && !empty($billingData['vehicle_no'])) {
                        Billing::create($billingData);
                        $importedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($rowIndex + 8) . ": " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$importedCount} records",
                'imported_count' => $importedCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing Excel file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $billingMonth = $request->get('month', Carbon::now()->format('Y-m'));
            $invoiceNumber = $request->get('invoice_number', '0000');

            $billings = Billing::byMonth($billingMonth)
                ->orderBy('sr')
                ->get();

            if ($billings->isEmpty()) {
                return response()->json([
                    'error' => 'No billing records found for the selected month'
                ], 404);
            }

            $spreadsheet = new Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();

            // Company header
            $worksheet->setCellValue('A1', 'SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY');
            $worksheet->mergeCells('A1:I1');
            $worksheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            // Address
            $worksheet->setCellValue('A2', 'Rizvi Chowk, Bypass Okara Road Depalpur');
            $worksheet->mergeCells('A2:I2');

            // Contact
            $worksheet->setCellValue('A3', 'Contact Detail:0300-6967450  zahidafzal5152@gmail.com');
            $worksheet->mergeCells('A3:I3');

            // NTN and Vendor Code
            $worksheet->setCellValue('A4', 'NTN : 4252472-5              Vendor Code : 0006781511');
            $worksheet->mergeCells('A4:I4');

            // Invoice info
            $monthName = Carbon::createFromFormat('Y-m', $billingMonth)->format('F-Y');
            $worksheet->setCellValue('A5', "Invoice No : {$invoiceNumber}                              Date : {$monthName}");
            $worksheet->mergeCells('A5:I5');

            $worksheet->setCellValue('A6', "Billing Month : {$monthName}");
            $worksheet->mergeCells('A6:I6');

            // Column headers
            $worksheet->setCellValue('A7', 'Sr');
            $worksheet->setCellValue('B7', 'Date');
            $worksheet->setCellValue('C7', 'Vhl No');
            $worksheet->setCellValue('D7', 'GP#');
            $worksheet->setCellValue('E7', 'Drop/Delivery Point');
            $worksheet->setCellValue('F7', 'Vhl');
            $worksheet->setCellValue('G7', 'Km');
            $worksheet->setCellValue('H7', 'Rate');
            $worksheet->setCellValue('I7', 'FRT');

            // Style headers
            $headerRange = 'A7:I7';
            $worksheet->getStyle($headerRange)->getFont()->setBold(true);
            $worksheet->getStyle($headerRange)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E0E0E0');

            // Data rows
            $row = 8;
            foreach ($billings as $billing) {
                $worksheet->setCellValue('A' . $row, $billing->sr ?? '');
                $worksheet->setCellValue('B' . $row, $billing->date ? $billing->date->format('d-M-y') : '');
                $worksheet->setCellValue('C' . $row, strtoupper($billing->vehicle_no ?? ''));
                $worksheet->setCellValue('D' . $row, $billing->gp_number ?? '');
                $worksheet->setCellValue('E' . $row, $billing->delivery_point ?? '');
                $worksheet->setCellValue('F' . $row, strtoupper($billing->vehicle_type ?? ''));
                $worksheet->setCellValue('G' . $row, $billing->km_covered ?? 0);
                $worksheet->setCellValue('H' . $row, $billing->rate ?? 0);
                $worksheet->setCellValue('I' . $row, $billing->freight ?? 0);
                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'I') as $col) {
                $worksheet->getColumnDimension($col)->setAutoSize(true);
            }

            $fileName = "SIMG_DEPALPUR_{$billingMonth}.xlsx";

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error exporting Excel file: ' . $e->getMessage()
            ], 500);
        }
    }
}