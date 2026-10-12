<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WarehouseTrip;
use Carbon\Carbon;

class WarehouseTripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouseLocations = ['Depalpur', 'Multan', 'Lahore', 'Faisalabad', 'Okara'];
        $vehicleTypes = ['1T', '2T', '4T', '8T'];
        $businessCategories = [
            'Open Market Work',
            'Buyer Supply Chain',
            'Buyer Breading',
            'Buyer Seed Supply',
            'Buyer Marketing Development',
            'Buyer S.P.R',
            'Syngenta'
        ];
        $statuses = ['pending', 'completed', 'billed'];
        $deliveryPoints = [
            'Depalpur Warehouse',
            'Multan Depot',
            'Lahore Distribution Center',
            'Faisalabad Hub',
            'Okara Storage',
            'Pakpattan',
            'Sahiwal',
            'Arifwala',
            'Haroonabad',
            'Hasilpur'
        ];

        $trips = [];

        for ($i = 1; $i <= 50; $i++) {
            $date = Carbon::now()->subDays(rand(0, 60));
            $billingMonth = $date->format('F-Y');
            $km = rand(50, 500);
            $rate = rand(10, 50);
            $freight = $km * $rate;

            $trips[] = [
                'trip_number' => 'TEMP-' . date('YmdHis') . '-' . $i,
                'trip_date' => $date->format('Y-m-d'),
                'vehicle_number' => 'VHL-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'gp_number' => 'GP-' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                'delivery_point' => $deliveryPoints[array_rand($deliveryPoints)],
                'vehicle_type' => $vehicleTypes[array_rand($vehicleTypes)],
                'kilometers' => $km,
                'rate_per_km' => $rate,
                'freight' => $freight,
                'driver_name' => 'Driver ' . rand(1, 20),
                'freight_bill_no' => 'FB-' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                'billing_month' => $billingMonth,
                'warehouse_location' => $warehouseLocations[array_rand($warehouseLocations)],
                'business_category' => $businessCategories[array_rand($businessCategories)],
                'status' => $statuses[array_rand($statuses)],
                'total_income' => $freight,
                'total_expense' => rand(1000, 5000),
                'net_amount' => $freight - rand(1000, 5000),
                'notes' => 'Sample trip ' . $i,
            ];
        }

        foreach ($trips as $trip) {
            WarehouseTrip::create($trip);
        }

        $this->command->info('50 warehouse trips seeded successfully!');
    }
}
