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
        Schema::create('bilties', function (Blueprint $table) {
            $table->id();
            
            // Bilty Information
            $table->string('bilty_number')->unique();
            $table->date('bilty_date');
            
            // Location Information
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            
            // Vehicle and Driver Information
            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('card_number')->nullable();
            
            // Sender and Receiver Information
            $table->string('sender_name')->nullable();
            $table->string('sender_phone')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            
            // Goods Information
            $table->string('goods_description')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('quantity_unit')->nullable();
            
            // Financial Information
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('advance_amount', 10, 2)->default(0);
            $table->decimal('remaining_balance', 10, 2)->default(0);
            $table->decimal('rent_amount', 10, 2)->nullable();
            
            // Status and Notes
            $table->enum('status', ['pending', 'in_transit', 'delivered', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            
            // Foreign Keys to existing system
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('job_id')->nullable()->constrained('transport_jobs')->onDelete('set null');
            
            // Additional fields for traditional receipt
            $table->string('registration_number')->nullable();
            $table->text('contact_details')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bilties');
    }
};
