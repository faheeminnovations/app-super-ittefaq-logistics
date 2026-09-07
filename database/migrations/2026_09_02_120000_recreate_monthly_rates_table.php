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
        Schema::create('monthly_rates', function (Blueprint $table) {
            $table->id();
            $table->enum('vehicle_category', ['1T', '2T', '4T', '8T']);
            $table->decimal('rate_per_km', 10, 2); // Rate per KM for this category
            $table->string('billing_month'); // e.g. "January-2026"
            $table->integer('billing_year');
            $table->integer('billing_month_number'); // 1-12 for sorting
            $table->timestamps();
            
            // Unique constraint: one rate per category per month
            $table->unique(['vehicle_category', 'billing_month', 'billing_year'], 'unique_monthly_rate');
            
            // Indexes
            $table->index('billing_month');
            $table->index('billing_year');
            $table->index('vehicle_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_rates');
    }
};
