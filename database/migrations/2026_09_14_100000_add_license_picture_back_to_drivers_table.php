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
            // Add back fields for CNIC and License pictures
            $table->string('cnic_picture_back')->nullable()->after('cnic_picture');
            $table->string('license_picture_back')->nullable()->after('license_picture');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['cnic_picture_back', 'license_picture_back']);
        });
    }
};