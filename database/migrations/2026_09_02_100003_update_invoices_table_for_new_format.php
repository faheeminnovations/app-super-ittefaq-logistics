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
        Schema::table('invoices', function (Blueprint $table) {
            // Drop old fields that are no longer needed (if they exist)
            if (Schema::hasColumn('invoices', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('invoices', 'due_date')) {
                $table->dropColumn('due_date');
            }
            if (Schema::hasColumn('invoices', 'paid_date')) {
                $table->dropColumn('paid_date');
            }
            if (Schema::hasColumn('invoices', 'vat')) {
                $table->dropColumn('vat');
            }
            
            // Change status enum to match new format (if exists)
            if (Schema::hasColumn('invoices', 'status')) {
                $table->dropColumn('status');
            }
            $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft')->after('notes');
            
            // Add new fields for the new invoice format (only if they don't exist)
            if (!Schema::hasColumn('invoices', 'billing_month')) {
                $table->string('billing_month')->nullable()->after('invoice_date');
            }
            if (!Schema::hasColumn('invoices', 'billing_year')) {
                $table->integer('billing_year')->nullable()->after('billing_month');
            }
            if (!Schema::hasColumn('invoices', 'billing_month_number')) {
                $table->integer('billing_month_number')->nullable()->after('billing_year');
            }
            
            // Service provider info
            if (!Schema::hasColumn('invoices', 'service_provider_name')) {
                $table->string('service_provider_name')->nullable()->after('billing_month_number');
            }
            if (!Schema::hasColumn('invoices', 'service_provider_address')) {
                $table->string('service_provider_address')->nullable()->after('service_provider_name');
            }
            if (!Schema::hasColumn('invoices', 'service_provider_ntn')) {
                $table->string('service_provider_ntn')->nullable()->after('service_provider_address');
            }
            if (!Schema::hasColumn('invoices', 'service_provider_strn')) {
                $table->string('service_provider_strn')->nullable()->after('service_provider_ntn');
            }
            
            // Client info
            if (!Schema::hasColumn('invoices', 'client_name')) {
                $table->string('client_name')->nullable()->after('service_provider_strn');
            }
            if (!Schema::hasColumn('invoices', 'client_address')) {
                $table->string('client_address')->nullable()->after('client_name');
            }
            if (!Schema::hasColumn('invoices', 'client_ntn')) {
                $table->string('client_ntn')->nullable()->after('client_address');
            }
            if (!Schema::hasColumn('invoices', 'client_strn')) {
                $table->string('client_strn')->nullable()->after('client_ntn');
            }
            
            // Warehouse and billing info
            if (!Schema::hasColumn('invoices', 'warehouse')) {
                $table->string('warehouse')->nullable()->after('client_strn');
            }
            if (!Schema::hasColumn('invoices', 'gl_number')) {
                $table->string('gl_number')->nullable()->after('warehouse');
            }
            if (!Schema::hasColumn('invoices', 'business_area')) {
                $table->string('business_area')->nullable()->after('gl_number');
            }
            
            // Financial calculations
            if (!Schema::hasColumn('invoices', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->default(0)->after('business_area');
            }
            if (!Schema::hasColumn('invoices', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(16)->after('subtotal');
            }
            if (!Schema::hasColumn('invoices', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
            }
            if (!Schema::hasColumn('invoices', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('invoices', 'amount_in_words')) {
                $table->string('amount_in_words')->nullable()->after('total_amount');
            }
            
            // Verification fields
            if (!Schema::hasColumn('invoices', 'verified_by')) {
                $table->string('verified_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('invoices', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }
            
            // Handle legacy_amount field
            if (Schema::hasColumn('invoices', 'amount') && !Schema::hasColumn('invoices', 'legacy_amount')) {
                $table->renameColumn('amount', 'legacy_amount');
            }
            
            // Make legacy_amount nullable after renaming
            if (Schema::hasColumn('invoices', 'legacy_amount')) {
                DB::statement('ALTER TABLE invoices MODIFY COLUMN legacy_amount DECIMAL(10,2) NULL');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Restore old fields
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->decimal('vat', 10, 2)->nullable();
            
            // Restore old status
            $table->dropColumn('status');
            $table->enum('status', ['paid', 'unpaid', 'overdue', 'cancelled'])->default('unpaid')->after('notes');
            
            // Remove new fields
            $table->dropColumn('billing_month');
            $table->dropColumn('billing_year');
            $table->dropColumn('billing_month_number');
            $table->dropColumn('service_provider_name');
            $table->dropColumn('service_provider_address');
            $table->dropColumn('service_provider_ntn');
            $table->dropColumn('service_provider_strn');
            $table->dropColumn('client_name');
            $table->dropColumn('client_address');
            $table->dropColumn('client_ntn');
            $table->dropColumn('client_strn');
            $table->dropColumn('warehouse');
            $table->dropColumn('gl_number');
            $table->dropColumn('business_area');
            $table->dropColumn('subtotal');
            $table->dropColumn('tax_rate');
            $table->dropColumn('tax_amount');
            $table->dropColumn('total_amount');
            $table->dropColumn('amount_in_words');
            $table->dropColumn('verified_by');
            $table->dropColumn('verified_at');
            
            // Restore amount field
            $table->renameColumn('legacy_amount', 'amount');
        });
    }
};
