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
        Schema::create('trip_operations', function (Blueprint $table) {
            $table->id();
            
            // Basic Trip Information
            $table->string('trip_number')->unique();
            $table->date('trip_date');
            $table->string('vehicle_number');
            $table->string('gp_number')->nullable();
            $table->text('delivery_point');
            
            // Vehicle Information
            $table->string('vehicle_category')->nullable(); // 1T, 2T, 4T, 8T, etc.
            $table->string('vehicle_type')->nullable(); // Additional vehicle type info
            
            // Distance and Rate Information
            $table->decimal('kilometers', 15, 2)->default(0);
            $table->decimal('rate_per_km', 15, 2)->default(0);
            $table->decimal('freight', 20, 2)->default(0);

            // Fuel Information
            $table->string('fuel_type')->nullable();
            $table->string('fuel')->nullable(); // Amount, cash, or null
            $table->enum('fuel_payment_type', ['credit', 'cash'])->nullable();
            $table->decimal('fuel_payment_amount', 15, 2)->default(0);
            
            // Driver Information
            $table->string('driver_name')->nullable();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            
            // Load and Bill Information
            $table->string('load_id')->nullable();
            $table->string('freight_bill_no')->nullable();
            
            // Billing Information
            $table->string('billing_month');
            $table->integer('billing_year');
            $table->integer('billing_month_number');
            $table->integer('sr')->nullable(); // Serial number
            
            // Business Category
            $table->string('business_category')->nullable();
            $table->string('customer_name')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            
            // Warehouse Information
            $table->string('warehouse_location')->nullable();
            $table->string('gl_number')->nullable();
            $table->string('business_area')->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            
            // Loading/Unloading Points
            $table->string('loading_point')->nullable();
            $table->string('unloading_point')->nullable();
            
            // Additional Trip Details
            $table->string('phone_number')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('guarantor')->nullable();
            $table->decimal('rent_paid', 15, 2)->default(0);
            $table->text('payment_details')->nullable();
            $table->text('receiving_details')->nullable();
            $table->decimal('expenses', 15, 2)->default(0);

            // Amount Tracking
            $table->decimal('initial_amount', 15, 2)->default(0);
            $table->decimal('amount_changed', 15, 2)->default(0);
            
            // Invoice Information
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->string('invoice_number')->nullable();
            
            // Status and Notes
            $table->enum('status', ['pending', 'in_progress', 'completed', 'billed', 'cancelled'])->default('pending');
            $table->enum('trip_status', ['pending', 'in_progress', 'completed', 'cancelled'])->nullable();
            $table->text('notes')->nullable();
            
            // Vehicle Reference
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            
            // Wizard Steps Completion
            $table->json('wizard_steps_completed')->nullable();
            $table->integer('current_wizard_step')->default(1);

            $table->timestamps();
            
            // Indexes
            $table->index('trip_number');
            $table->index('trip_date');
            $table->index('vehicle_number');
            $table->index('billing_month');
            $table->index('billing_year');
            $table->index('status');
            $table->index('warehouse_location');
            $table->index('business_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_operations');
    }
};
