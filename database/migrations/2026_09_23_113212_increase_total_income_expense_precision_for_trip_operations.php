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
            $table->decimal('total_income', 20, 2)->change();
            $table->decimal('total_expense', 20, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_operations', function (Blueprint $table) {
            $table->decimal('total_income', 15, 2)->change();
            $table->decimal('total_expense', 15, 2)->change();
        });
    }
};
