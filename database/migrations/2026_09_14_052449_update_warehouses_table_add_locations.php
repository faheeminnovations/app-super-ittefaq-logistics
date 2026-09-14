<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('location')->after('name');
            $table->string('address')->nullable()->after('location');
            $table->string('contact_number')->nullable()->after('address');
            $table->boolean('is_active')->default(true)->after('contact_number');
        });

        // Insert the three warehouse locations
        DB::table('warehouses')->insert([
            [
                'name' => 'Multan Warehouse',
                'location' => 'Multan',
                'address' => 'Multan, Punjab, Pakistan',
                'contact_number' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lahore Warehouse',
                'location' => 'Lahore',
                'address' => 'Lahore, Punjab, Pakistan',
                'contact_number' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Depalpur Warehouse',
                'location' => 'Depalpur',
                'address' => 'Depalpur, Punjab, Pakistan',
                'contact_number' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn(['name', 'location', 'address', 'contact_number', 'is_active']);
        });
    }
};
