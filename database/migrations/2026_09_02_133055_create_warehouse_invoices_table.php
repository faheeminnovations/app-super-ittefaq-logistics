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
        Schema::create('warehouse_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->string('billing_month'); // e.g. "May-2026"
            $table->integer('billing_year');
            $table->integer('billing_month_number'); // 1-12 for sorting
            
            // Service Provider Information
            $table->string('warehouse_location')->default('Depalpur');
            $table->string('gl_number')->default('4022265');
            $table->string('business_area')->default('0900, Without any Cost Center.');
            $table->string('service_provider_name')->default('SUPER ITTEFAQ MINI GOODS TRANSPORT CO.');
            $table->string('service_provider_address')->default('RIZVI CHOWK DEPALPUR');
            $table->string('service_provider_ntn')->default('4252472-5');
            
            // Client Information
            $table->string('client_name')->default('Bayer Pakistan (Pvt.) Ltd.');
            $table->string('client_address')->default('Plot # 23, Sector-22, Korangi Industrial Area, Karachi Pakistan');
            $table->string('client_ntn')->nullable();
            
            // Financial Information
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(16);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('amount_in_words')->nullable();
            
            // Status and Notes
            $table->enum('status', ['draft', 'pending', 'sent', 'paid', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('invoice_number');
            $table->index('billing_month');
            $table->index('billing_year');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_invoices');
    }
};
