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
            if (!Schema::hasColumn('trip_logs', 'expenses')) {
                $table->decimal('expenses', 10, 2)->default(0)->after('rent_paid');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_logs', function (Blueprint $table) {
            if (Schema::hasColumn('trip_logs', 'expenses')) {
                $table->dropColumn('expenses');
            }
        });
    }
};
