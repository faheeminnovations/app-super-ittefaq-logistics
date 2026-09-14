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
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('cnic')->nullable()->after('licence_no');
            $table->string('driver_picture')->nullable()->after('cnic');
            $table->string('cnic_picture')->nullable()->after('driver_picture');
            $table->string('license_picture')->nullable()->after('cnic_picture');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['cnic', 'driver_picture', 'cnic_picture', 'license_picture']);
        });
    }
};
