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
            // Add unique constraint on sr within billing_month and billing_year
            $table->unique(['sr', 'billing_month', 'billing_year'], 'unique_sr_per_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_logs', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('unique_sr_per_month');
        });
    }
};
