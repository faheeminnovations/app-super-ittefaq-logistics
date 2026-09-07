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
        Schema::create('trip_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
            
            $table->integer('sr')->nullable(); // Serial number
            $table->date('date');
            $table->string('vehicle_no'); // Vhl No - e.g. SLN-3824, LET-200
            $table->string('gp_number')->nullable(); // GP# - Gate Pass Number
            $table->text('delivery_point'); // Drop/Delivery Point
            $table->enum('vehicle_category', ['1T', '2T', '4T', '8T']); // Vhl
            $table->decimal('km', 10, 2); // KM distance
            $table->decimal('rate', 10, 2)->nullable(); // Rate per KM
            $table->decimal('frt', 10, 2)->nullable(); // Freight Amount = KM × Rate
            $table->string('fuel')->nullable(); // Fuel - can be amount, CASH, NILL, etc.
            $table->string('driver_name')->nullable(); // Driver Name
            
            // New fields from Excel format
            $table->string('load_id')->nullable(); // Load ID
            $table->string('freight_bill_no')->nullable(); // Freight Bill NO
            
            // Month and year for reporting
            $table->string('billing_month'); // e.g. "May-2026"
            $table->integer('billing_year');
            $table->integer('billing_month_number'); // 1-12 for sorting
            
            $table->timestamps();
            
            // Indexes for filtering
            $table->index('billing_month');
            $table->index('billing_year');
            $table->index('vehicle_no');
            $table->index('invoice_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_logs');
    }
};