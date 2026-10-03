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
        Schema::table('trip_operations', function (Blueprint $table) {
            // Remove fuel-related fields as they should be handled through expense entries
            if (Schema::hasColumn('trip_operations', 'fuel_type')) {
                $table->dropColumn('fuel_type');
            }
            if (Schema::hasColumn('trip_operations', 'fuel')) {
                $table->dropColumn('fuel');
            }
            if (Schema::hasColumn('trip_operations', 'fuel_payment_type')) {
                $table->dropColumn('fuel_payment_type');
            }
            if (Schema::hasColumn('trip_operations', 'fuel_payment_amount')) {
                $table->dropColumn('fuel_payment_amount');
            }
            if (Schema::hasColumn('trip_operations', 'expense_category')) {
                $table->dropColumn('expense_category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_operations', function (Blueprint $table) {
            // Restore fuel-related fields if needed
            if (!Schema::hasColumn('trip_operations', 'fuel_type')) {
                $table->string('fuel_type')->nullable();
            }
            if (!Schema::hasColumn('trip_operations', 'fuel')) {
                $table->string('fuel')->nullable();
            }
            if (!Schema::hasColumn('trip_operations', 'fuel_payment_type')) {
                $table->enum('fuel_payment_type', ['credit', 'cash'])->nullable();
            }
            if (!Schema::hasColumn('trip_operations', 'fuel_payment_amount')) {
                $table->decimal('fuel_payment_amount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('trip_operations', 'expense_category')) {
                $table->string('expense_category')->nullable();
            }
        });
    }
};
