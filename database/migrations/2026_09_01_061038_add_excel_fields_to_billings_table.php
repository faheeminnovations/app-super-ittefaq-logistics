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
        Schema::table('billings', function (Blueprint $table) {
            $table->string('gp_number')->nullable()->after('vehicle_no');
            $table->string('vehicle_type')->nullable()->after('delivery_point');
            $table->decimal('rate', 10, 2)->default(0)->after('km_covered');
            $table->decimal('freight', 12, 2)->default(0)->after('rent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn(['freight', 'rate', 'vehicle_type', 'gp_number']);
        });
    }
};
