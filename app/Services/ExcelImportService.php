<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\TripLog;
use App\Models\CompanySettings;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class ExcelImportService
{
    /**
     * Import Excel file with 2-sheet format
     * Sheet 1: Invoice details
     * Sheet 2: Trip details
     */
    public function importNewFormat($filePath)
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheetNames = $spreadsheet->getSheetNames();
            
            if (count($sheetNames) < 2) {
                throw new \Exception('Excel file must have at least 2 sheets');
            }
            
            // Process Sheet 1 (Invoice details)
            $invoiceSheet = $spreadsheet->getSheetByName($sheetNames[0]);
            $invoiceData = $this->parseInvoiceSheet($invoiceSheet);
            
            // Process Sheet 2 (Trip details)
            $tripSheet = $spreadsheet->getSheetByName($sheetNames[1]);
            $tripData = $this->parseTripSheet($tripSheet);
            
            // Create or update invoice
            $invoice = $this->createInvoice($invoiceData);
            
            // Create trip logs
            $this->createTripLogs($tripData, $invoice);
            
            // Calculate invoice totals
            $invoice->calculateTotals();
            
            return [
                'success' => true,
                'invoice' => $invoice,
                'trip_count' => count($tripData),
                'message' => 'Successfully imported ' . count($tripData) . ' trips'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error importing Excel: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Parse invoice sheet (Sheet 1)
     */
    private function parseInvoiceSheet($sheet)
    {
        $data = [
            'invoice_number' => null,
            'invoice_date' => null,
            'billing_month' => null,
            'billing_year' => null,
            'warehouse' => 'Depalpur',
            'gl_number' => '4022265',
            'business_area' => '0900, Without any Cost Center.',
            'service_provider_name' => 'SUPER ITTEFAQ MINI GOODS TRANSPORT CO.',
            'service_provider_address' => 'RIZVI CHOWK DEPALPUR',
            'service_provider_ntn' => '4252472-5',
            'client_name' => 'Bayer Pakistan (Pvt.) Ltd.',
            'client_address' => 'Plot # 23, Sector-22, Korangi Industrial Area, Karachi Pakistan',
            'tax_rate' => 16,
        ];
        
        // Extract invoice number from row 5, column A
        $invoiceNoCell = $sheet->getCell('A5')->getValue();
        if (preg_match('/Invoice No\s*:\s*(\d+)/i', $invoiceNoCell, $matches)) {
            $data['invoice_number'] = $matches[1];
        }
        
        // Extract date from row 5
        $dateCell = $sheet->getCell('E5')->getValue();
        if (is_numeric($dateCell)) {
            $data['invoice_date'] = Date::excelToDateTimeObject($dateCell)->format('Y-m-d');
        }
        
        // Extract billing month from row 6
        $billingMonthCell = $sheet->getCell('A6')->getValue();
        if (preg_match('/Billing Month\s*:\s*(.+)/i', $billingMonthCell, $matches)) {
            $billingMonth = trim($matches[1]);
            $data['billing_month'] = $billingMonth;
            
            // Extract year and month number
            if (preg_match('/(\w+)\s*-\s*(\d{4})/i', $billingMonth, $dateMatches)) {
                $monthName = $dateMatches[1];
                $year = $dateMatches[2];
                $data['billing_year'] = (int)$year;
                $data['billing_month_number'] = date('m', strtotime("$monthName 1, $year"));
            }
        }
        
        // Set default values if not extracted
        if (empty($data['billing_month'])) {
            $data['billing_month'] = date('F-Y');
            $data['billing_year'] = date('Y');
            $data['billing_month_number'] = date('n');
        }
        
        // Extract warehouse from row 8
        $warehouseCell = $sheet->getCell('B8')->getValue();
        if (!empty($warehouseCell)) {
            $data['warehouse'] = $warehouseCell;
        }
        
        // Extract GL number from row 9
        $glCell = $sheet->getCell('B9')->getValue();
        if (!empty($glCell)) {
            $data['gl_number'] = $glCell;
        }
        
        // Extract business area from row 10
        $businessAreaCell = $sheet->getCell('B10')->getValue();
        if (!empty($businessAreaCell)) {
            $data['business_area'] = $businessAreaCell;
        }
        
        return $data;
    }
    
    /**
     * Parse trip sheet (Sheet 2)
     */
    private function parseTripSheet($sheet)
    {
        $trips = [];
        $highestRow = $sheet->getHighestRow();
        
        // Find header row (contains "Sr", "Date", "Load ID", etc.)
        $headerRow = 7; // Based on our analysis, header is at row 7
        
        // Start reading from row 9 (first data row)
        for ($row = 9; $row <= $highestRow; $row++) {
            $sr = $sheet->getCell('A' . $row)->getValue();
            
            // Skip empty rows or TOTAL row
            if (empty($sr) && $sr !== '0') {
                continue;
            }
            
            // Skip TOTAL AMOUNT row
            if (strtoupper(trim($sr)) === 'TOTAL AMOUNT') {
                continue;
            }
            
            $dateValue = $sheet->getCell('B' . $row)->getValue();
            $date = now()->format('Y-m-d'); // Default to today
            if (is_numeric($dateValue) && $dateValue > 0) {
                try {
                    $date = Date::excelToDateTimeObject($dateValue)->format('Y-m-d');
                } catch (\Exception $e) {
                    $date = now()->format('Y-m-d');
                }
            }
            
            $loadId = $sheet->getCell('C' . $row)->getValue();
            $freightBillNo = $sheet->getCell('D' . $row)->getValue();
            $vehicleNo = $sheet->getCell('E' . $row)->getValue();
            $gpNumber = $sheet->getCell('F' . $row)->getValue();
            $deliveryPoint = $sheet->getCell('G' . $row)->getValue();
            $vehicleCategory = $sheet->getCell('H' . $row)->getValue();
            $km = $sheet->getCell('I' . $row)->getValue();
            $rate = $sheet->getCell('J' . $row)->getValue();
            $frtValue = $sheet->getCell('K' . $row)->getValue();
            
            // Clean numeric values
            $km = is_numeric($km) ? floatval($km) : 0;
            $rate = is_numeric($rate) ? floatval($rate) : 0;
            
            // Calculate FRT if it's a formula or empty
            if (empty($frtValue) || is_string($frtValue)) {
                $frt = $km * $rate;
            } else {
                $frt = is_numeric($frtValue) ? floatval($frtValue) : 0;
            }
            
            // Normalize vehicle category
            $vehicleCategory = strtoupper(trim($vehicleCategory));
            if (!in_array($vehicleCategory, ['1T', '2T', '4T', '8T'])) {
                $vehicleCategory = '2T'; // Default
            }
            
            $trips[] = [
                'sr' => is_numeric($sr) ? intval($sr) : null,
                'date' => $date,
                'load_id' => $loadId,
                'freight_bill_no' => $freightBillNo,
                'vehicle_no' => $vehicleNo,
                'gp_number' => $gpNumber,
                'delivery_point' => $deliveryPoint,
                'vehicle_category' => $vehicleCategory,
                'km' => $km,
                'rate' => $rate,
                'frt' => $frt,
            ];
        }
        
        return $trips;
    }
    
    /**
     * Create invoice from parsed data
     */
    private function createInvoice($data)
    {
        // Generate invoice number if not provided
        if (empty($data['invoice_number'])) {
            $companySettings = CompanySettings::first();
            $invoiceNumber = $companySettings ? str_pad($companySettings->current_invoice_number, 4, '0', STR_PAD_LEFT) : '0001';
            $data['invoice_number'] = $invoiceNumber;
            
            // Increment invoice number
            if ($companySettings) {
                $companySettings->current_invoice_number++;
                $companySettings->save();
            }
        }
        
        $invoice = Invoice::updateOrCreate(
            ['invoice_number' => $data['invoice_number']],
            [
                'invoice_date' => $data['invoice_date'] ?? now(),
                'billing_month' => $data['billing_month'],
                'billing_year' => $data['billing_year'] ?? now()->year,
                'billing_month_number' => $data['billing_month_number'] ?? now()->month,
                'warehouse' => $data['warehouse'],
                'gl_number' => $data['gl_number'],
                'business_area' => $data['business_area'],
                'service_provider_name' => $data['service_provider_name'],
                'service_provider_address' => $data['service_provider_address'],
                'service_provider_ntn' => $data['service_provider_ntn'],
                'client_name' => $data['client_name'],
                'client_address' => $data['client_address'],
                'tax_rate' => $data['tax_rate'],
                'status' => 'draft',
            ]
        );
        
        return $invoice;
    }
    
    /**
     * Create trip logs linked to invoice
     */
    private function createTripLogs($tripData, $invoice)
    {
        foreach ($tripData as $trip) {
            // Skip if no vehicle number
            if (empty($trip['vehicle_no'])) {
                continue;
            }
            
            TripLog::create([
                'invoice_id' => $invoice->id,
                'sr' => $trip['sr'],
                'date' => $trip['date'],
                'vehicle_no' => $trip['vehicle_no'],
                'gp_number' => $trip['gp_number'],
                'delivery_point' => $trip['delivery_point'],
                'vehicle_category' => $trip['vehicle_category'],
                'km' => $trip['km'],
                'rate' => $trip['rate'],
                'frt' => $trip['frt'],
                'load_id' => $trip['load_id'],
                'freight_bill_no' => $trip['freight_bill_no'],
                'billing_month' => $invoice->billing_month,
                'billing_year' => $invoice->billing_year,
                'billing_month_number' => $invoice->billing_month_number,
            ]);
        }
    }
}