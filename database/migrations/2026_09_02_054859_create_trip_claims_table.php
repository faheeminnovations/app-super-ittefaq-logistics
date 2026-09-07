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
        Schema::create('trip_claims', function (Blueprint $table) {
            $table->id();
            $table->date('trip_date');
            $table->string('vehicle_number');
            $table->string('route_from');
            $table->string('route_to');
            $table->decimal('agreed_amount', 10, 2)->default(0);
            $table->date('return_date')->nullable();
            $table->decimal('claimed_amount', 10, 2)->default(0);
            $table->text('claim_details')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->string('billing_month')->nullable();
            $table->timestamps();
            
            $table->index('vehicle_number');
            $table->index('trip_date');
            $table->index('billing_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_claims');
    }
};
