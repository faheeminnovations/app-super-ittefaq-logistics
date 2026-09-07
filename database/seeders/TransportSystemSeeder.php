<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\CompanySettings;
use App\Models\MonthlyRate;

class TransportSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create company settings
        CompanySettings::firstOrCreate([], [
            'company_name' => 'SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY',
            'address' => 'Rizvi Chowk, Bypass Okara Road Depalpur',
            'contact_phone' => '0300-6967450',
            'contact_email' => 'zahidafzal5152@gmail.com',
            'ntn' => '4252472-5',
            'vendor_code' => '0006781511',
            'current_invoice_number' => 1,
        ]);

        // Create vehicles from Excel data
        $vehicles = [
            ['reg_no' => 'SLN-3824', 'type' => 'Truck', 'make_model' => '2 Ton Truck', 'year' => 2020, 'vehicle_category' => '2T', 'status' => 'available', 'fitness_certificate_expiry' => '2026-12-31'],
            ['reg_no' => 'LET-200', 'type' => 'Truck', 'make_model' => '2 Ton Truck', 'year' => 2021, 'vehicle_category' => '2T', 'status' => 'available', 'fitness_certificate_expiry' => '2026-12-31'],
            ['reg_no' => 'CBC-6378', 'type' => 'Truck', 'make_model' => '4 Ton Truck', 'year' => 2019, 'vehicle_category' => '4T', 'status' => 'available', 'fitness_certificate_expiry' => '2026-12-31'],
            ['reg_no' => 'LES-9541', 'type' => 'Truck', 'make_model' => '2 Ton Truck', 'year' => 2022, 'vehicle_category' => '2T', 'status' => 'available', 'fitness_certificate_expiry' => '2026-12-31'],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::firstOrCreate(['reg_no' => $vehicle['reg_no']], $vehicle);
        }

        // Create drivers from Excel data
        $drivers = [
            ['name' => 'Irfan', 'licence_no' => 'LIC-001', 'category' => 'Heavy', 'phone' => '0300-1234567', 'status' => 'on_duty', 'cpc_expiry' => '2026-12-31'],
            ['name' => 'Nawaz', 'licence_no' => 'LIC-002', 'category' => 'Heavy', 'phone' => '0300-2345678', 'status' => 'on_duty', 'cpc_expiry' => '2026-12-31'],
            ['name' => 'Shahid', 'licence_no' => 'LIC-003', 'category' => 'Heavy', 'phone' => '0300-3456789', 'status' => 'on_duty', 'cpc_expiry' => '2026-12-31'],
            ['name' => 'Mushtaq', 'licence_no' => 'LIC-004', 'category' => 'Heavy', 'phone' => '0300-4567890', 'status' => 'on_duty', 'cpc_expiry' => '2026-12-31'],
            ['name' => 'Usama', 'licence_no' => 'LIC-005', 'category' => 'Heavy', 'phone' => '0300-5678901', 'status' => 'on_duty', 'cpc_expiry' => '2026-12-31'],
            ['name' => 'Adil', 'licence_no' => 'LIC-006', 'category' => 'Heavy', 'phone' => '0300-6789012', 'status' => 'on_duty', 'cpc_expiry' => '2026-12-31'],
            ['name' => 'Self', 'licence_no' => 'LIC-007', 'category' => 'Heavy', 'phone' => '0300-6967450', 'status' => 'on_duty', 'cpc_expiry' => '2026-12-31'],
        ];

        foreach ($drivers as $driver) {
            Driver::firstOrCreate(['name' => $driver['name']], $driver);
        }

        // Create monthly rates for 2026 (based on Excel data)
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $categories = ['1T', '2T', '4T', '8T'];
        
        // Base rates from Excel (varies by month)
        $ratesByMonth = [
            0 => ['1T' => 95, '2T' => 112, '4T' => 128, '8T' => 150],   // January
            1 => ['1T' => 95, '2T' => 112, '4T' => 128, '8T' => 150],   // February
            2 => ['1T' => 95, '2T' => 112, '4T' => 128, '8T' => 150],   // March
            3 => ['1T' => 95, '2T' => 112, '4T' => 128, '8T' => 150],   // April
            4 => ['1T' => 128, '2T' => 134.29, '4T' => 141, '8T' => 160], // May (increased rates)
            5 => ['1T' => 128, '2T' => 134.29, '4T' => 141, '8T' => 160], // June
        ];

        foreach ($months as $monthNumber => $monthName) {
            foreach ($categories as $category) {
                $rate = $ratesByMonth[$monthNumber][$category] ?? 112; // Default to 112 if not set
                
                MonthlyRate::firstOrCreate([
                    'vehicle_category' => $category,
                    'billing_month' => $monthName . '-2026',
                    'billing_year' => 2026,
                    'billing_month_number' => $monthNumber + 1,
                ], [
                    'rate_per_km' => $rate,
                ]);
            }
        }
    }
}