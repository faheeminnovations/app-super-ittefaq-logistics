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
            // Drop old columns
            $table->dropColumn(['mot_expiry', 'insurance_expiry']);
            
            // Add new fitness certificate column
            $table->date('fitness_certificate_expiry')->after('year');
            
            // Add work type column to separate company vs private work
            $table->enum('work_type', ['company', 'private', 'both'])->default('company')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Revert changes
            $table->dropColumn(['fitness_certificate_expiry', 'work_type']);
            
            // Add back old columns
            $table->date('mot_expiry')->after('year');
            $table->date('insurance_expiry')->after('mot_expiry');
        });
    }
};
