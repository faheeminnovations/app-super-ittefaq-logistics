<?php

namespace App\Http\Controllers;

use App\Models\TripLog;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\MonthlyRate;
use App\Models\CompanySettings;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TransportManagementController extends Controller
{
    /**
     * Display transport management dashboard
     */
    public function dashboard()
    {
        $currentMonth = Carbon::now()->format('F-Y');
        $currentYear = Carbon::now()->year;

        // Get current month's data
        $currentMonthTrips = TripLog::byMonth($currentMonth, $currentYear)->get() ?? collect([]);
        $totalTrips = $currentMonthTrips->count();
        $totalKm = $currentMonthTrips->sum('km');
        $totalFreight = $currentMonthTrips->sum('frt');

        // Vehicle-wise breakdown
        $vehicleBreakdown = TripLog::byMonth($currentMonth, $currentYear)
            ->selectRaw('vehicle_no, COUNT(*) as trip_count, SUM(km) as total_km, SUM(frt) as total_freight')
            ->groupBy('vehicle_no')
            ->get() ?? collect([]);

        // Driver-wise breakdown
        $driverBreakdown = TripLog::byMonth($currentMonth, $currentYear)
            ->selectRaw('driver_name, COUNT(*) as trip_count, SUM(frt) as total_freight')
            ->whereNotNull('driver_name')
            ->groupBy('driver_name')
            ->get() ?? collect([]);

        // Category-wise breakdown
        $categoryBreakdown = TripLog::byMonth($currentMonth, $currentYear)
            ->selectRaw('business_category, COUNT(*) as trip_count, SUM(frt) as total_freight')
            ->groupBy('business_category')
            ->get() ?? collect([]);

        // Monthly totals for the year (for chart)
        $monthlyTotals = collect([]);
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create()->month($i)->format('F-Y');
            $monthData = TripLog::byMonth($monthName, $currentYear);
            $monthlyTotals->push([
                'month' => Carbon::create()->month($i)->format('F'),
                'total_freight' => $monthData->totalFreight(),
                'total_km' => $monthData->totalKm(),
                'trip_count' => $monthData->count(),
            ]);
        }

        // Total vehicles and drivers
        $totalVehicles = Vehicle::active()->count();
        $totalDrivers = Driver::active()->count();

        return view('transport.dashboard', compact(
            'totalTrips',
            'totalKm',
            'totalFreight',
            'vehicleBreakdown',
            'driverBreakdown',
            'categoryBreakdown',
            'monthlyTotals',
            'totalVehicles',
            'totalDrivers',
            'currentMonth'
        ));
    }

    /**
     * Display rate management page
     */
    public function rateManagement()
    {
        $monthlyRates = MonthlyRate::orderBy('billing_year')
            ->orderBy('billing_month_number')
            ->get()
            ->groupBy(function ($item) {
                return $item->billing_year;
            });

        $vehicleCategories = ['1T', '2T', '4T', '8T'];

        return view('transport.rate-management', compact(
            'monthlyRates',
            'vehicleCategories'
        ));
    }

    /**
     * Update monthly rates
     */
    public function updateRates(Request $request)
    {
        $validated = $request->validate([
            'rates' => 'required|array',
            'rates.*.*' => 'required|numeric|min:0',
        ]);

        foreach ($validated['rates'] as $year => $months) {
            foreach ($months as $monthNumber => $categories) {
                foreach ($categories as $category => $rate) {
                    $monthName = Carbon::create()->month($monthNumber)->format('F');
                    $billingMonth = $monthName . '-' . $year;

                    MonthlyRate::updateOrCreate(
                        [
                            'vehicle_category' => $category,
                            'billing_month' => $billingMonth,
                            'billing_year' => $year,
                            'billing_month_number' => $monthNumber,
                        ],
                        ['rate_per_km' => $rate]
                    );
                }
            }
        }

        return redirect()->route('transport.rate-management')
            ->with('success', 'Monthly rates updated successfully.');
    }

    /**
     * Generate monthly invoice (Excel export)
     */
    public function generateInvoice($month, $year)
    {
        $tripLogs = TripLog::byMonth($month, $year)
            ->orderBy('sr')
            ->get();

        $companySettings = CompanySettings::getCurrent();

        // Increment invoice number
        $invoiceNumber = str_pad($companySettings->incrementInvoiceNumber(), 4, '0', STR_PAD_LEFT);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Company Header (exactly as in Excel)
        $sheet->setCellValue('A1', 'SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY');
        $sheet->setCellValue('A2', 'Rizvi Chowk, Bypass Okara Road Depalpur');
        $sheet->setCellValue('A3', 'Contact Detail:' . $companySettings->contact_phone . '  ' . $companySettings->contact_email);
        $sheet->setCellValue('A4', 'NTN : ' . $companySettings->ntn . '              Vendor Code : ' . $companySettings->vendor_code);
        $sheet->setCellValue('A5', 'Invoice No : ' . $invoiceNumber . '                              Date : ' . $month);
        $sheet->setCellValue('A6', 'Billing Month : ' . $month);

        // Table Header
        $sheet->setCellValue('A7', 'Sr');
        $sheet->setCellValue('B7', 'Date');
        $sheet->setCellValue('C7', 'Vhl No');
        $sheet->setCellValue('D7', 'GP#');
        $sheet->setCellValue('E7', 'Drop/Delivery Point');
        $sheet->setCellValue('F7', 'Vhl');
        $sheet->setCellValue('G7', 'Km');
        $sheet->setCellValue('H7', 'Rate');
        $sheet->setCellValue('I7', 'FRT');
        $sheet->setCellValue('J7', 'FUEL');
        $sheet->setCellValue('K7', 'DRIVER');

        // Data rows
        $row = 8;
        foreach ($tripLogs as $trip) {
            $sheet->setCellValue('A' . $row, $trip->sr);
            $sheet->setCellValue('B' . $row, $trip->date->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $trip->vehicle_no);
            $sheet->setCellValue('D' . $row, $trip->gp_number);
            $sheet->setCellValue('E' . $row, $trip->delivery_point);
            $sheet->setCellValue('F' . $row, $trip->vehicle_category);
            $sheet->setCellValue('G' . $row, $trip->km);
            $sheet->setCellValue('H' . $row, $trip->rate);
            $sheet->setCellValue('I' . $row, $trip->frt);
            $sheet->setCellValue('J' . $row, $trip->fuel);
            $sheet->setCellValue('K' . $row, $trip->driver_name);
            $row++;
        }

        // Total row
        $totalFreight = $tripLogs->sum('frt');
        $sheet->setCellValue('I' . $row, 'TOTAL AMOUNT');
        $sheet->setCellValue('J' . $row, $totalFreight);

        // Styling
        $sheet->getStyle('A1:A6')->getFont()->setBold(true);
        $sheet->getStyle('A7:K7')->getFont()->setBold(true);
        $sheet->getStyle('A7:K' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A7:K7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I' . $row)->getFont()->setBold(true);

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Invoice_{$month}_{$year}.xlsx";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Vehicle-wise report
     */
    public function vehicleReport(Request $request)
    {
        $vehicleNo = $request->vehicle;
        $month = $request->month ?? Carbon::now()->format('F-Y');
        $year = Carbon::now()->year;

        $query = TripLog::query();
        
        if ($vehicleNo) {
            $query->byVehicle($vehicleNo);
        }

        if ($month && $year) {
            $query->byMonth($month, $year);
        }

        $tripLogs = $query->orderBy('date', 'desc')->get();
        $vehicles = Vehicle::active()->pluck('reg_no', 'reg_no');

        $totalFreight = $tripLogs->sum('frt');
        $totalKm = $tripLogs->sum('km');
        $totalTrips = $tripLogs->count();

        return view('transport.reports.vehicle', compact(
            'tripLogs',
            'vehicles',
            'vehicleNo',
            'month',
            'totalFreight',
            'totalKm',
            'totalTrips'
        ));
    }

    /**
     * Driver-wise report
     */
    public function driverReport(Request $request)
    {
        $driverName = $request->driver;
        $month = $request->month ?? Carbon::now()->format('F-Y');
        $year = Carbon::now()->year;

        $query = TripLog::query();
        
        if ($driverName) {
            $query->byDriver($driverName);
        }

        if ($month && $year) {
            $query->byMonth($month, $year);
        }

        $tripLogs = $query->orderBy('date', 'desc')->get();
        $drivers = Driver::active()->pluck('name', 'name');

        $totalFreight = $tripLogs->sum('frt');
        $totalFuel = $tripLogs->where('fuel', '!=', 'CASH')->where('fuel', '!=', 'NILL')->count();
        $totalTrips = $tripLogs->count();

        return view('transport.reports.driver', compact(
            'tripLogs',
            'drivers',
            'driverName',
            'month',
            'totalFreight',
            'totalFuel',
            'totalTrips'
        ));
    }

    /**
     * Category-wise report
     */
    public function categoryReport(Request $request)
    {
        $category = $request->category;
        $month = $request->month ?? Carbon::now()->format('F-Y');
        $year = Carbon::now()->year;

        $query = TripLog::query();
        
        if ($category) {
            $query->byCategory($category);
        }

        if ($month && $year) {
            $query->byMonth($month, $year);
        }

        $tripLogs = $query->orderBy('date', 'desc')->get();

        $categories = [
            'Buyer Supply Chain',
            'Buyer Branding',
            'Buyer Marketing Development',
            'Buyer SPR',
            'Cement Pakistan',
            'Open Market Work',
            'Buyer Seed Supply'
        ];

        $totalFreight = $tripLogs->sum('frt');
        $totalTrips = $tripLogs->count();

        return view('transport.reports.category', compact(
            'tripLogs',
            'categories',
            'category',
            'month',
            'totalFreight',
            'totalTrips'
        ));
    }

    /**
     * Import Excel data
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
            'month' => 'required|string',
            'year' => 'required|integer',
        ]);

        $file = $request->file('excel_file');
        $month = $request->month;
        $year = $request->year;

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        // Find the header row (contains "Sr", "Date", etc.)
        $headerRow = 7; // Based on Excel structure
        $dataStartRow = 9; // Data starts from row 9

        $importedCount = 0;
        $errorCount = 0;

        for ($row = $dataStartRow; $row <= $sheet->getHighestRow(); $row++) {
            $sr = $sheet->getCell('A' . $row)->getValue();
            $date = $sheet->getCell('B' . $row)->getValue();
            $vehicleNo = $sheet->getCell('C' . $row)->getValue();
            $gpNumber = $sheet->getCell('D' . $row)->getValue();
            $deliveryPoint = $sheet->getCell('E' . $row)->getValue();
            $vehicleCategory = $sheet->getCell('F' . $row)->getValue();
            $km = $sheet->getCell('G' . $row)->getValue();
            $rate = $sheet->getCell('H' . $row)->getValue();
            $fuel = $sheet->getCell('J' . $row)->getValue();
            $driverName = $sheet->getCell('K' . $row)->getValue();

            // Skip empty rows
            if (empty($vehicleNo) && empty($date)) {
                continue;
            }

            try {
                // Convert Excel date to proper format if needed
                if (is_numeric($date)) {
                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
                }

                // Ensure numeric values
                $km = is_numeric($km) ? floatval($km) : 0;
                $rate = is_numeric($rate) ? floatval($rate) : 0;
                
                // Calculate FRT
                $frt = $km * $rate;

                TripLog::create([
                    'sr' => $sr,
                    'date' => $date,
                    'vehicle_no' => $vehicleNo,
                    'gp_number' => $gpNumber,
                    'delivery_point' => $deliveryPoint,
                    'vehicle_category' => $vehicleCategory,
                    'km' => $km,
                    'rate' => $rate,
                    'frt' => $frt,
                    'fuel' => $fuel,
                    'driver_name' => $driverName,
                    'business_category' => 'Open Market Work', // Default
                    'billing_month' => $month,
                    'billing_year' => $year,
                    'billing_month_number' => Carbon::parse($month . '-01')->month,
                ]);

                $importedCount++;
            } catch (\Exception $e) {
                $errorCount++;
            }
        }

        return redirect()->route('trip-logs.index')
            ->with('success', "Imported {$importedCount} records successfully. {$errorCount} errors encountered.");
    }
}