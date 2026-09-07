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
        Schema::dropIfExists('monthly_rates');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('monthly_rates', function (Blueprint $table) {
            $table->id();
            $table->enum('vehicle_category', ['1T', '2T', '4T', '8T']);
            $table->decimal('rate_per_km', 10, 2);
            $table->string('billing_month');
            $table->integer('billing_year');
            $table->integer('billing_month_number');
            $table->timestamps();
            
            $table->unique(['vehicle_category', 'billing_month', 'billing_year'], 'unique_monthly_rate');
            $table->index('billing_month');
            $table->index('billing_year');
            $table->index('vehicle_category');
        });
    }
};
