<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Skip AdminUserSeeder if admin user already exists
        if (!\App\Models\User::where('email', 'admin@ittefaq.com')->exists()) {
            $this->call([
                \Database\Seeders\AdminUserSeeder::class,
            ]);
        }

        $this->call([
            \Database\Seeders\CustomerSeeder::class,
        ]);
    }
}
