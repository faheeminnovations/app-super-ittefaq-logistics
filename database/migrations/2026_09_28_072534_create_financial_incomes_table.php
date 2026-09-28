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
        Schema::create('financial_incomes', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->decimal('total_income', 15, 2)->default(0);
            $table->string('vehicle')->nullable();
            $table->string('driver')->nullable();
            $table->decimal('expense', 15, 2)->default(0);
            $table->string('expense_category')->nullable();
            $table->decimal('fuel', 15, 2)->default(0);
            $table->decimal('food', 15, 2)->default(0);
            $table->decimal('toll', 15, 2)->default(0);
            $table->decimal('driver_payment', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_incomes');
    }
};
