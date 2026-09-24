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
            $table->decimal('net_amount', 20, 2)->default(0)->after('total_expense');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_operations', function (Blueprint $table) {
            $table->dropColumn('net_amount');
        });
    }
};
