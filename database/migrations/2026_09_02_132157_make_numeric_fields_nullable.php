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
            $table->decimal('km', 10, 2)->nullable()->change();
            $table->integer('quantity')->nullable()->change();
            $table->decimal('rent_paid', 10, 2)->nullable()->change();
            $table->decimal('expenses', 10, 2)->nullable()->change();
            $table->string('billing_month')->nullable()->change();
            $table->integer('billing_year')->nullable()->change();
            $table->integer('billing_month_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_logs', function (Blueprint $table) {
            $table->decimal('km', 10, 2)->nullable(false)->change();
            $table->integer('quantity')->nullable(false)->change();
            $table->decimal('rent_paid', 10, 2)->nullable(false)->change();
            $table->decimal('expenses', 10, 2)->nullable(false)->change();
            $table->string('billing_month')->nullable(false)->change();
            $table->integer('billing_year')->nullable(false)->change();
            $table->integer('billing_month_number')->nullable(false)->change();
        });
    }
};
