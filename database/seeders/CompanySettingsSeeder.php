<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CompanySettings;

class CompanySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanySettings::updateOrCreate(
            ['id' => 1],
            [
                'company_name' => 'SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY',
                'address' => 'Rizvi Chowk, Bypass Okara Road Depalpur',
                'contact_phone' => '0300-6967450',
                'contact_email' => 'zahidafzal5152@gmail.com',
                'ntn' => '4252472-5',
                'vendor_code' => '0006781511',
                'current_invoice_number' => 1,
            ]
        );
    }
}
