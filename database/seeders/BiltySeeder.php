<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bilty;
use App\Models\Customer;

class BiltySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first customer for sample data
        $customer = Customer::first();
        
        $bilties = [
            [
                'bilty_number' => 'BLT-2026-08-0001',
                'bilty_date' => '2026-08-28',
                'from_location' => 'مظفر آباد',
                'to_location' => 'دیپال پور',
                'vehicle_number' => 'LEA-1234',
                'driver_name' => 'محمد علی',
                'card_number' => 'CARD-789',
                'sender_name' => 'شاہ افضل',
                'sender_phone' => '0322-6967450',
                'receiver_name' => 'زاہد افضل جومیر',
                'receiver_phone' => '0300-6967450',
                'goods_description' => 'گندم کی بیلیاں - Wheat bags',
                'quantity' => 50,
                'quantity_unit' => 'bags',
                'total_amount' => 15000.00,
                'advance_amount' => 5000.00,
                'remaining_balance' => 10000.00,
                'rent_amount' => 12000.00,
                'status' => 'in_transit',
                'notes' => 'Emergency delivery - Time sensitive',
                'customer_id' => $customer ? $customer->id : null,
                'registration_number' => '4252472-5',
                'contact_details' => 'rana a.d. 0306-6549090',
            ],
            [
                'bilty_number' => 'BLT-2026-08-0002',
                'bilty_date' => '2026-08-27',
                'from_location' => 'لاہور',
                'to_location' => 'کراچی',
                'vehicle_number' => 'KHI-5678',
                'driver_name' => 'احمد رضا',
                'card_number' => 'CARD-456',
                'sender_name' => 'الغماز ٹریڈرز',
                'sender_phone' => '0301-1234567',
                'receiver_name' => 'کاروبار اینٹرپرائز',
                'receiver_phone' => '021-34567890',
                'goods_description' => 'کتابوں کے ڈبے - Book boxes',
                'quantity' => 100,
                'quantity_unit' => 'boxes',
                'total_amount' => 25000.00,
                'advance_amount' => 10000.00,
                'remaining_balance' => 15000.00,
                'rent_amount' => 22000.00,
                'status' => 'delivered',
                'notes' => 'Books delivery for educational institution',
                'customer_id' => $customer ? $customer->id : null,
                'registration_number' => '4252472-5',
                'contact_details' => 'rana a.d. 0306-6549090',
            ],
            [
                'bilty_number' => 'BLT-2026-08-0003',
                'bilty_date' => '2026-08-26',
                'from_location' => 'فیصل آباد',
                'to_location' => 'راولپنڈی',
                'vehicle_number' => 'RWP-9012',
                'driver_name' => 'جمشید خان',
                'card_number' => 'CARD-123',
                'sender_name' => 'ٹیکسٹائل ملس',
                'sender_phone' => '041-2345678',
                'receiver_name' => 'فیشن گارمنٹس',
                'receiver_phone' => '051-9876543',
                'goods_description' => 'کپڑے کی گٹھڑیاں - Cloth bundles',
                'quantity' => 200,
                'quantity_unit' => 'bundles',
                'total_amount' => 35000.00,
                'advance_amount' => 15000.00,
                'remaining_balance' => 20000.00,
                'rent_amount' => 30000.00,
                'status' => 'pending',
                'notes' => 'Textile materials for garment factory',
                'customer_id' => $customer ? $customer->id : null,
                'registration_number' => '4252472-5',
                'contact_details' => 'rana a.d. 0306-6549090',
            ],
            [
                'bilty_number' => 'BLT-2026-08-0004',
                'bilty_date' => '2026-08-25',
                'from_location' => 'پشاور',
                'to_location' => ' اسلام آباد',
                'vehicle_number' => 'ISB-3456',
                'driver_name' => 'عمران خان',
                'card_number' => 'CARD-789',
                'sender_name' => 'فروٹ مارکیٹ',
                'sender_phone' => '091-5678901',
                'receiver_name' => 'سپر اسٹور',
                'receiver_phone' => '051-2345678',
                'goods_description' => 'پھل اور سبزیاں - Fruits and vegetables',
                'quantity' => 500,
                'quantity_unit' => 'kg',
                'total_amount' => 8000.00,
                'advance_amount' => 3000.00,
                'remaining_balance' => 5000.00,
                'rent_amount' => 7000.00,
                'status' => 'delivered',
                'notes' => 'Fresh produce delivery - Perishable goods',
                'customer_id' => $customer ? $customer->id : null,
                'registration_number' => '4252472-5',
                'contact_details' => 'rana a.d. 0306-6549090',
            ],
            [
                'bilty_number' => 'BLT-2026-08-0005',
                'bilty_date' => '2026-08-24',
                'from_location' => 'کوئٹہ',
                'to_location' => 'لاہور',
                'vehicle_number' => 'LHR-7890',
                'driver_name' => 'ناصر بلوچ',
                'card_number' => 'CARD-012',
                'sender_name' => 'چمنی اور کانسی',
                'sender_phone' => '081-3456789',
                'receiver_name' => 'بیلڈنگ میٹریلز',
                'receiver_phone' => '042-8765432',
                'goods_description' => 'چمنی اور کانسی کا سامان - Coal and copper',
                'quantity' => 1000,
                'quantity_unit' => 'kg',
                'total_amount' => 45000.00,
                'advance_amount' => 20000.00,
                'remaining_balance' => 25000.00,
                'rent_amount' => 40000.00,
                'status' => 'in_transit',
                'notes' => 'Mining materials - Heavy cargo',
                'customer_id' => $customer ? $customer->id : null,
                'registration_number' => '4252472-5',
                'contact_details' => 'rana a.d. 0306-6549090',
            ],
        ];

        foreach ($bilties as $bilty) {
            Bilty::create($bilty);
        }
    }
}
