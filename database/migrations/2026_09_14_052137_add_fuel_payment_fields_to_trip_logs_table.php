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
            $table->enum('fuel_payment_type', ['credit', 'cash'])->nullable()->after('fuel');
            $table->decimal('fuel_payment_amount', 10, 2)->default(0)->after('fuel_payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_logs', function (Blueprint $table) {
            $table->dropColumn(['fuel_payment_type', 'fuel_payment_amount']);
        });
    }
};
