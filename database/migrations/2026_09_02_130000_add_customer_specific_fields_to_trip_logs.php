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
        Schema::table('trip_logs', function (Blueprint $table) {
            // Additional fields for different customer types
            
            // Buyer Supply Chain specific fields
            if (!Schema::hasColumn('trip_logs', 'cluster')) {
                $table->string('cluster')->nullable()->after('business_category');
            }
            
            // Common fields for all customer types
            if (!Schema::hasColumn('trip_logs', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('cluster');
            }
            
            // Buyer Branding specific fields
            if (!Schema::hasColumn('trip_logs', 'loading_point')) {
                $table->string('loading_point')->nullable()->after('delivery_point');
            }
            if (!Schema::hasColumn('trip_logs', 'unloading_point')) {
                $table->string('unloading_point')->nullable()->after('loading_point');
            }
            
            // Buyer Seed Supply specific fields
            if (!Schema::hasColumn('trip_logs', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('driver_name');
            }
            if (!Schema::hasColumn('trip_logs', 'quantity')) {
                $table->integer('quantity')->default(0)->after('phone_number');
            }
            if (!Schema::hasColumn('trip_logs', 'guarantor')) {
                $table->string('guarantor')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('trip_logs', 'rent_paid')) {
                $table->decimal('rent_paid', 10, 2)->default(0)->after('guarantor');
            }
            if (!Schema::hasColumn('trip_logs', 'payment_details')) {
                $table->text('payment_details')->nullable()->after('rent_paid');
            }
            if (!Schema::hasColumn('trip_logs', 'receiving_details')) {
                $table->text('receiving_details')->nullable()->after('payment_details');
            }
            
            // Status field for Buyer Seed Supply
            if (!Schema::hasColumn('trip_logs', 'trip_status')) {
                $table->enum('trip_status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending')->after('receiving_details');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_logs', function (Blueprint $table) {
            $columns = [
                'cluster', 'customer_name', 'loading_point', 'unloading_point',
                'phone_number', 'quantity', 'guarantor', 'rent_paid',
                'payment_details', 'receiving_details', 'trip_status'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('trip_logs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
