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
            $table->decimal('kilometers', 15, 2)->change();
            $table->decimal('rate_per_km', 15, 2)->change();
            $table->decimal('freight', 20, 2)->change();
            $table->decimal('fuel_payment_amount', 15, 2)->change();
            $table->decimal('rent_paid', 15, 2)->change();
            $table->decimal('expenses', 15, 2)->change();
            $table->decimal('initial_amount', 15, 2)->change();
            $table->decimal('amount_changed', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_operations', function (Blueprint $table) {
            $table->decimal('kilometers', 10, 2)->change();
            $table->decimal('rate_per_km', 10, 2)->change();
            $table->decimal('freight', 10, 2)->change();
            $table->decimal('fuel_payment_amount', 10, 2)->change();
            $table->decimal('rent_paid', 10, 2)->change();
            $table->decimal('expenses', 10, 2)->change();
            $table->decimal('initial_amount', 10, 2)->change();
            $table->decimal('amount_changed', 10, 2)->change();
        });
    }
};
