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
            if (!Schema::hasColumn('trip_logs', 'business_category')) {
                $table->enum('business_category', ['Open Market Work', 'Buyer Supply Chain', 'Buyer Branding', 'Buyer Seed Supply', 'Buyer Marketing Development', 'Buyer S.P.R', 'Cement Pakistan'])->nullable()->after('vehicle_category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_logs', function (Blueprint $table) {
            if (Schema::hasColumn('trip_logs', 'business_category')) {
                $table->dropColumn('business_category');
            }
        });
    }
};
