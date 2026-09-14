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
            $table->decimal('initial_amount', 10, 2)->default(0)->after('expenses');
            $table->decimal('amount_changed', 10, 2)->default(0)->after('initial_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_logs', function (Blueprint $table) {
            $table->dropColumn(['initial_amount', 'amount_changed']);
        });
    }
};
