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
        Schema::create('trip_expense_entries', function (Blueprint $table) {
            $table->id();

            // Relationship to trip operation
            $table->foreignId('trip_operation_id')->constrained('trip_operations')->onDelete('cascade');

            // Expense category and type
            $table->string('expense_category'); // fuel, toll, parking, driver_payment, maintenance, etc.
            $table->string('expense_type')->nullable(); // Additional sub-type if needed
            $table->string('payment_type')->nullable(); // credit, cash

            // Expense amount
            $table->decimal('amount', 20, 2)->default(0);

            // Description and details
            $table->string('description')->nullable();
            $table->text('notes')->nullable();

            // Expense date (can be different from trip date)
            $table->date('expense_date')->nullable();

            // Reference numbers
            $table->string('receipt_number')->nullable();
            $table->string('bill_number')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('trip_operation_id');
            $table->index('expense_category');
            $table->index('expense_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_expense_entries');
    }
};
