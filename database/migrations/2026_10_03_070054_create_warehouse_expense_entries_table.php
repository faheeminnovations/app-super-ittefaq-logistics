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
        Schema::create('warehouse_expense_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_trip_id')->constrained('warehouse_trips')->onDelete('cascade');
            $table->string('expense_category');
            $table->string('payment_type')->nullable();
            $table->decimal('amount', 18, 2);
            $table->string('description')->nullable();
            $table->date('expense_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_expense_entries');
    }
};
