<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouse_trips', function (Blueprint $table) {
            $table->id();
            $table->string('trip_number')->unique();
            $table->date('trip_date');
            $table->string('vehicle_number');
            $table->string('gp_number');
            $table->string('delivery_point');
            $table->string('vehicle_type'); // 1T, 2T, 4T, etc.
            $table->decimal('kilometers', 10, 2);
            $table->decimal('rate_per_km', 10, 2);
            $table->decimal('freight', 10, 2); // Auto-calculated: kilometers * rate_per_km
            $table->string('fuel_type')->nullable(); // CASH, etc.
            $table->string('driver_name')->nullable();
            $table->string('load_id')->nullable();
            $table->string('freight_bill_no')->nullable();
            $table->string('billing_month');
            $table->string('warehouse_location')->default('DEPALPUR');
            $table->string('gl_number')->nullable();
            $table->string('business_area')->nullable();
            $table->string('invoice_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, completed, billed
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_trips');
    }
};