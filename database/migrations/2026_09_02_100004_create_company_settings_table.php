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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY');
            $table->string('address')->default('Rizvi Chowk, Bypass Okara Road Depalpur');
            $table->string('contact_phone')->default('0300-6967450');
            $table->string('contact_email')->default('zahidafzal5152@gmail.com');
            $table->string('ntn')->default('4252472-5');
            $table->string('vendor_code')->default('0006781511');
            $table->integer('current_invoice_number')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};