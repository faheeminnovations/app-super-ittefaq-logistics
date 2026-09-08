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
            $table->string('licence_no')->nullable()->change();
            $table->string('category')->nullable()->change();
            $table->date('cpc_expiry')->nullable()->change();
            $table->string('phone')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('licence_no')->nullable(false)->change();
            $table->string('category')->nullable(false)->change();
            $table->date('cpc_expiry')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
        });
    }
};
