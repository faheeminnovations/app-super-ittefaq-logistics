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
            $table->string('expense_category')->nullable()->after('expenses');
            $table->decimal('total_income', 15, 2)->default(0)->after('amount_changed');
            $table->decimal('total_expense', 15, 2)->default(0)->after('total_income');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_operations', function (Blueprint $table) {
            $table->dropColumn(['expense_category', 'total_income', 'total_expense']);
        });
    }
};
