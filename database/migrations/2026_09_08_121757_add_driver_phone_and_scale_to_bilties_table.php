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
        Schema::table('bilties', function (Blueprint $table) {
            $table->string('driver_phone')->nullable()->after('card_number');
            $table->decimal('scale', 10, 2)->nullable()->after('rent_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bilties', function (Blueprint $table) {
            $table->dropColumn(['driver_phone', 'scale']);
        });
    }
};
