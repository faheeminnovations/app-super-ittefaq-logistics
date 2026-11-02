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
        Schema::table('warehouse_trips', function (Blueprint $table) {
            $table->decimal('required_km', 15, 2)->nullable()->after('vehicle_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_trips', function (Blueprint $table) {
            $table->dropColumn('required_km');
        });
    }
};
