<?php

namespace App\Http\Controllers;

use App\Models\FinancialIncome;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinancialIncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FinancialIncome::query();

        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Filter by vehicle if provided
        if ($request->filled('vehicle')) {
            $query->where('vehicle', $request->vehicle);
        }

        // Filter by driver if provided
        if ($request->filled('driver')) {
            $query->where('driver', $request->driver);
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Clone query for totals calculation (before pagination)
        $totalsQuery = clone $query;

        $financialIncomes = $query->orderBy('date', 'desc')->paginate(50);

        // Calculate totals from the cloned query
        $allIncomes = $totalsQuery->get();
        $totalIncome = $allIncomes->sum('total_income');
        $totalExpense = $allIncomes->sum('expense') ?? 0;
        $totalFuel = $allIncomes->sum('fuel') ?? 0;
        $totalFood = $allIncomes->sum('food') ?? 0;
        $totalToll = $allIncomes->sum('toll') ?? 0;
        $totalDriverPayment = $allIncomes->sum('driver_payment') ?? 0;
        $netProfit = $totalIncome - ($totalExpense + $totalFuel + $totalFood + $totalToll + $totalDriverPayment);

        // Get filter options
        $vehicles = Vehicle::active()->pluck('reg_no', 'reg_no');
        $drivers = Driver::active()->pluck('name', 'name');

        return view('financial-income.index', compact(
            'financialIncomes',
            'vehicles',
            'drivers',
            'totalIncome',
            'totalExpense',
            'totalFuel',
            'totalFood',
            'totalToll',
            'totalDriverPayment',
            'netProfit'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::active()->pluck('reg_no', 'reg_no');
        $drivers = Driver::active()->pluck('name', 'name');

        $expenseCategories = [
            'fuel' => 'Fuel',
            'food' => 'Food',
            'toll' => 'Toll',
            'driver_payment' => 'Driver Payment',
            'maintenance' => 'Maintenance',
            'other' => 'Other'
        ];

        return view('financial-income.create', compact('vehicles', 'drivers', 'expenseCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'total_income' => 'required|numeric|min:0',
            'vehicle' => 'nullable|string|max:50',
            'driver' => 'nullable|string|max:255',
            'expense' => 'nullable|numeric|min:0',
            'expense_category' => 'nullable|string|max:50',
            'loading_point' => 'nullable|string|max:255',
            'uploading_point' => 'nullable|string|max:255',
            'fuel' => 'nullable|numeric|min:0',
            'food' => 'nullable|numeric|min:0',
            'toll' => 'nullable|numeric|min:0',
            'driver_payment' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,completed,cancelled',
        ]);

        // Set default values for numeric fields if not provided
        $validated['expense'] = $validated['expense'] ?? 0;
        $validated['fuel'] = $validated['fuel'] ?? 0;
        $validated['food'] = $validated['food'] ?? 0;
        $validated['toll'] = $validated['toll'] ?? 0;
        $validated['driver_payment'] = $validated['driver_payment'] ?? 0;
        $validated['status'] = $validated['status'] ?? 'pending';

        FinancialIncome::create($validated);

        return redirect()->route('financial-income.index')
            ->with('success', 'Financial income record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FinancialIncome $financialIncome)
    {
        return view('financial-income.show', compact('financialIncome'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinancialIncome $financialIncome)
    {
        $vehicles = Vehicle::active()->pluck('reg_no', 'reg_no');
        $drivers = Driver::active()->pluck('name', 'name');

        $expenseCategories = [
            'fuel' => 'Fuel',
            'food' => 'Food',
            'toll' => 'Toll',
            'driver_payment' => 'Driver Payment',
            'maintenance' => 'Maintenance',
            'other' => 'Other'
        ];

        return view('financial-income.edit', compact('financialIncome', 'vehicles', 'drivers', 'expenseCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FinancialIncome $financialIncome)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'total_income' => 'required|numeric|min:0',
            'vehicle' => 'nullable|string|max:50',
            'driver' => 'nullable|string|max:255',
            'expense' => 'nullable|numeric|min:0',
            'expense_category' => 'nullable|string|max:50',
            'loading_point' => 'nullable|string|max:255',
            'uploading_point' => 'nullable|string|max:255',
            'fuel' => 'nullable|numeric|min:0',
            'food' => 'nullable|numeric|min:0',
            'toll' => 'nullable|numeric|min:0',
            'driver_payment' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,completed,cancelled',
        ]);

        // Set default values for numeric fields if not provided
        $validated['expense'] = $validated['expense'] ?? 0;
        $validated['fuel'] = $validated['fuel'] ?? 0;
        $validated['food'] = $validated['food'] ?? 0;
        $validated['toll'] = $validated['toll'] ?? 0;
        $validated['driver_payment'] = $validated['driver_payment'] ?? 0;
        $validated['status'] = $validated['status'] ?? 'pending';

        $financialIncome->update($validated);

        return redirect()->route('financial-income.index')
            ->with('success', 'Financial income record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FinancialIncome $financialIncome)
    {
        $financialIncome->delete();

        return redirect()->route('financial-income.index')
            ->with('success', 'Financial income record deleted successfully.');
    }

    /**
     * Export financial income to Excel.
     */
    public function export(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $vehicle = $request->get('vehicle');
        $driver = $request->get('driver');
        $status = $request->get('status');

        $query = FinancialIncome::query();

        if ($dateFrom) {
            $query->where('date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('date', '<=', $dateTo);
        }

        if ($vehicle) {
            $query->where('vehicle', $vehicle);
        }

        if ($driver) {
            $query->where('driver', $driver);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $financialIncomes = $query->orderBy('date', 'desc')->get();

        // Create Excel file using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set company header
        $sheet->setCellValue('A1', 'SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY');
        $sheet->setCellValue('A2', 'Rizvi Chowk, Bypass Okara Road');
        $sheet->setCellValue('A3', 'Contact Detail: 0300-6967450');
        $sheet->setCellValue('A4', 'NTN : 4252472-5');

        $dateRange = ($dateFrom && $dateTo) ? 
            Carbon::parse($dateFrom)->format('d M Y') . ' to ' . Carbon::parse($dateTo)->format('d M Y') : 
            'All Time';
        $sheet->setCellValue('A5', 'Financial Income Report - ' . $dateRange);

        // Set column headers
        $sheet->setCellValue('A7', 'Date');
        $sheet->setCellValue('B7', 'Total Income');
        $sheet->setCellValue('C7', 'Vehicle');
        $sheet->setCellValue('D7', 'Driver');
        $sheet->setCellValue('E7', 'Expense');
        $sheet->setCellValue('F7', 'Expense Category');
        $sheet->setCellValue('G7', 'Loading Point');
        $sheet->setCellValue('H7', 'Uploading Point');
        $sheet->setCellValue('I7', 'Fuel');
        $sheet->setCellValue('J7', 'Food');
        $sheet->setCellValue('K7', 'Toll');
        $sheet->setCellValue('L7', 'Driver Payment');
        $sheet->setCellValue('M7', 'Status');

        // Fill data
        $row = 8;
        foreach ($financialIncomes as $income) {
            $sheet->setCellValue('A' . $row, $income->date ? $income->date->format('d/m/Y') : 'N/A');
            $sheet->setCellValue('B' . $row, number_format($income->total_income, 2));
            $sheet->setCellValue('C' . $row, $income->vehicle ?? '-');
            $sheet->setCellValue('D' . $row, $income->driver ?? '-');
            $sheet->setCellValue('E' . $row, number_format($income->expense ?? 0, 2));
            $sheet->setCellValue('F' . $row, $income->expense_category ?? '-');
            $sheet->setCellValue('G' . $row, $income->loading_point ?? '-');
            $sheet->setCellValue('H' . $row, $income->uploading_point ?? '-');
            $sheet->setCellValue('I' . $row, number_format($income->fuel ?? 0, 2));
            $sheet->setCellValue('J' . $row, number_format($income->food ?? 0, 2));
            $sheet->setCellValue('K' . $row, number_format($income->toll ?? 0, 2));
            $sheet->setCellValue('L' . $row, number_format($income->driver_payment ?? 0, 2));
            $sheet->setCellValue('M' . $row, ucfirst($income->status ?? 'pending'));
            $row++;
        }

        // Add totals row
        $row++;
        $sheet->setCellValue('A' . $row, 'TOTALS');
        $sheet->setCellValue('B' . $row, number_format($financialIncomes->sum('total_income'), 2));
        $sheet->setCellValue('E' . $row, number_format($financialIncomes->sum('expense') ?? 0, 2));
        $sheet->setCellValue('I' . $row, number_format($financialIncomes->sum('fuel') ?? 0, 2));
        $sheet->setCellValue('J' . $row, number_format($financialIncomes->sum('food') ?? 0, 2));
        $sheet->setCellValue('K' . $row, number_format($financialIncomes->sum('toll') ?? 0, 2));
        $sheet->setCellValue('L' . $row, number_format($financialIncomes->sum('driver_payment') ?? 0, 2));

        // Auto-size columns
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set headers for download
        if ($dateFrom && $dateTo) {
            $filename = "financial_income_" . Carbon::parse($dateFrom)->format('Y-m-d') . '_to_' . Carbon::parse($dateTo)->format('Y-m-d') . ".xlsx";
        } else {
            $filename = "financial_income_all_time.xlsx";
        }

        // Save to temp file
        $tempFile = tempnam(sys_get_temp_dir(), 'financial_income_');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFile);

        // Return file download response
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
