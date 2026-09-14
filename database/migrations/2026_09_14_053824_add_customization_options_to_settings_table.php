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
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'vehicle_categories')) {
                $table->json('vehicle_categories')->nullable();
            }
            if (!Schema::hasColumn('settings', 'vehicle_number_formats')) {
                $table->json('vehicle_number_formats')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['vehicle_categories', 'vehicle_number_formats']);
        });
    }
};
