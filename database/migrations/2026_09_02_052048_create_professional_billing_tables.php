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
        // 1. Supply Chain Billing Table
        Schema::create('supply_chain_billings', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->nullable();
            $table->date('date');
            $table->string('vehicle_number');
            $table->string('load_id')->nullable();
            $table->string('gate_pass_number')->nullable();
            $table->string('delivery_point');
            $table->string('vehicle_category')->nullable();
            $table->string('cluster')->nullable();
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('Pending');
            $table->string('billing_month')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_number');
            $table->index('date');
            $table->index('billing_month');
        });

        // 2. Branding Billing Table
        Schema::create('branding_billings', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->nullable();
            $table->date('date');
            $table->string('vehicle_number');
            $table->string('delivery_point');
            $table->decimal('kilometers', 8, 2)->default(0);
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('Pending');
            $table->string('billing_month')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_number');
            $table->index('date');
            $table->index('billing_month');
        });

        // 3. Marketing Development Billing Table
        Schema::create('marketing_development_billings', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->nullable();
            $table->date('date');
            $table->string('vehicle_number');
            $table->string('delivery_point');
            $table->decimal('kilometers', 8, 2)->default(0);
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('Pending');
            $table->string('billing_month')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_number');
            $table->index('date');
            $table->index('billing_month');
        });

        // 4. SPR Billing Table
        Schema::create('spr_billings', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->nullable();
            $table->date('date');
            $table->string('vehicle_number');
            $table->string('delivery_point');
            $table->decimal('kilometers', 8, 2)->default(0);
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('Pending');
            $table->string('billing_month')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_number');
            $table->index('date');
            $table->index('billing_month');
        });

        // 5. Cement Pakistan Billing Table
        Schema::create('cement_pakistan_billings', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->nullable();
            $table->date('date');
            $table->string('vehicle_number');
            $table->string('delivery_point');
            $table->decimal('kilometers', 8, 2)->default(0);
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('Pending');
            $table->string('billing_month')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_number');
            $table->index('date');
            $table->index('billing_month');
        });

        // 6. Open Market Work Billing Table
        Schema::create('open_market_work_billings', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('vehicle_number');
            $table->string('serial_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('customer_name');
            $table->string('loading_point');
            $table->string('unloading_point')->nullable();
            $table->decimal('rent', 10, 2)->default(0);
            $table->decimal('expenses', 10, 2)->default(0);
            $table->string('status')->default('Pending');
            $table->string('billing_month')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_number');
            $table->index('date');
            $table->index('billing_month');
        });

        // 7. Seed Supply Billing Table
        Schema::create('seed_supply_billings', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('vehicle_number');
            $table->string('driver_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('delivery_point');
            $table->string('guarantor')->nullable();
            $table->decimal('rent_paid', 10, 2)->default(0);
            $table->text('payment_details')->nullable();
            $table->text('receiving_details')->nullable();
            $table->string('status')->default('Pending');
            $table->string('billing_month')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_number');
            $table->index('date');
            $table->index('billing_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seed_supply_billings');
        Schema::dropIfExists('open_market_work_billings');
        Schema::dropIfExists('cement_pakistan_billings');
        Schema::dropIfExists('spr_billings');
        Schema::dropIfExists('marketing_development_billings');
        Schema::dropIfExists('branding_billings');
        Schema::dropIfExists('supply_chain_billings');
    }
};
