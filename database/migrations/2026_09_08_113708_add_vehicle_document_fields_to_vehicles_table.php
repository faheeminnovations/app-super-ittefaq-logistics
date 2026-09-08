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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->date('route_permit')->nullable()->after('vehicle_category');
            $table->date('token_tax')->nullable()->after('route_permit');
            $table->date('insurance')->nullable()->after('token_tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['route_permit', 'token_tax', 'insurance']);
        });
    }
};
