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
        Schema::table('financial_incomes', function (Blueprint $table) {
            $table->decimal('expense', 10, 2)->nullable()->change();
            $table->decimal('fuel', 10, 2)->nullable()->change();
            $table->decimal('food', 10, 2)->nullable()->change();
            $table->decimal('toll', 10, 2)->nullable()->change();
            $table->decimal('driver_payment', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_incomes', function (Blueprint $table) {
            $table->decimal('expense', 10, 2)->nullable(false)->change();
            $table->decimal('fuel', 10, 2)->nullable(false)->change();
            $table->decimal('food', 10, 2)->nullable(false)->change();
            $table->decimal('toll', 10, 2)->nullable(false)->change();
            $table->decimal('driver_payment', 10, 2)->nullable(false)->change();
        });
    }
};
