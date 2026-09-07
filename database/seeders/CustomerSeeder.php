<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create customers for each business category
        $customers = [
            // Buyer Supply Chain Customers
            [
                'name' => 'Buyer Supply Chain - Warehouse A',
                'contact_person' => 'Ali Khan',
                'contact_email' => 'ali@buyerchain.com',
                'contact_phone' => '0300-1234567',
                'city' => 'Lahore',
                'address' => 'Industrial Zone, Lahore',
                'business_type' => 'Buyer Supply Chain',
                'status' => 'active',
            ],
            [
                'name' => 'Buyer Supply Chain - Distribution Center',
                'contact_person' => 'Sara Ahmed',
                'contact_email' => 'sara@buyerchain.com',
                'contact_phone' => '0300-2345678',
                'city' => 'Faisalabad',
                'address' => 'Main Market, Faisalabad',
                'business_type' => 'Buyer Supply Chain',
                'status' => 'active',
            ],

            // Buyer Branding Customers
            [
                'name' => 'Buyer Branding - Marketing Hub',
                'contact_person' => 'Kamran Shah',
                'contact_email' => 'kamran@branding.com',
                'contact_phone' => '0300-3456789',
                'city' => 'Karachi',
                'address' => 'Business District, Karachi',
                'business_type' => 'Buyer Branding',
                'status' => 'active',
            ],
            [
                'name' => 'Buyer Branding - Advertising Agency',
                'contact_person' => 'Fatima Malik',
                'contact_email' => 'fatima@branding.com',
                'contact_phone' => '0300-4567890',
                'city' => 'Islamabad',
                'address' => 'Media City, Islamabad',
                'business_type' => 'Buyer Branding',
                'status' => 'active',
            ],

            // Buyer Seed Supply Customers
            [
                'name' => 'Buyer Seed Supply - Agricultural Center',
                'contact_person' => 'Hassan Raza',
                'contact_email' => 'hassan@seedsupply.com',
                'contact_phone' => '0300-5678901',
                'city' => 'Multan',
                'address' => 'Agricultural Zone, Multan',
                'business_type' => 'Buyer Seed Supply',
                'status' => 'active',
            ],
            [
                'name' => 'Buyer Seed Supply - Farm Distribution',
                'contact_person' => 'Ayesha Khan',
                'contact_email' => 'ayesha@seedsupply.com',
                'contact_phone' => '0300-6789012',
                'city' => 'Sialkot',
                'address' => 'Rural Area, Sialkot',
                'business_type' => 'Buyer Seed Supply',
                'status' => 'active',
            ],

            // Buyer Marketing Development Customers
            [
                'name' => 'Buyer Marketing Development - Event Management',
                'contact_person' => 'Usman Ali',
                'contact_email' => 'usman@marketingdev.com',
                'contact_phone' => '0300-7890123',
                'city' => 'Lahore',
                'address' => 'Event Plaza, Lahore',
                'business_type' => 'Buyer Marketing Development',
                'status' => 'active',
            ],

            // Buyer S.P.R Customers
            [
                'name' => 'Buyer S.P.R - Corporate Office',
                'contact_person' => 'Zara Ahmed',
                'contact_email' => 'zara@spr.com',
                'contact_phone' => '0300-8901234',
                'city' => 'Rawalpindi',
                'address' => 'Corporate Hub, Rawalpindi',
                'business_type' => 'Buyer S.P.R',
                'status' => 'active',
            ],

            // Cement Pakistan Customers
            [
                'name' => 'Cement Pakistan - Construction Site A',
                'contact_person' => 'Bilal Khan',
                'contact_email' => 'bilal@cementpak.com',
                'contact_phone' => '0300-9012345',
                'city' => 'Peshawar',
                'address' => 'Construction Zone, Peshawar',
                'business_type' => 'Cement Pakistan',
                'status' => 'active',
            ],
            [
                'name' => 'Cement Pakistan - Building Materials',
                'contact_person' => 'Nadia Shah',
                'contact_email' => 'nadia@cementpak.com',
                'contact_phone' => '0300-0123456',
                'city' => 'Quetta',
                'address' => 'Industrial Area, Quetta',
                'business_type' => 'Cement Pakistan',
                'status' => 'active',
            ],

            // Open Market Work Customers
            [
                'name' => 'Open Market - Local Transport',
                'contact_person' => 'Imran Malik',
                'contact_email' => 'imran@openmarket.com',
                'contact_phone' => '0300-1234568',
                'city' => 'Gujranwala',
                'address' => 'Main Bazaar, Gujranwala',
                'business_type' => 'Open Market Work',
                'status' => 'active',
            ],
            [
                'name' => 'Open Market - Individual Customer',
                'contact_person' => 'Kashif Ali',
                'contact_email' => 'kashif@openmarket.com',
                'contact_phone' => '0300-2345679',
                'city' => 'Sargodha',
                'address' => 'Residential Area, Sargodha',
                'business_type' => 'Open Market Work',
                'status' => 'active',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['name' => $customer['name']],
                $customer
            );
        }

        $this->command->info('Customers seeded successfully for all business categories!');
    }
}
