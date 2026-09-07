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
        Schema::table('warehouse_trips', function (Blueprint $table) {
            $table->foreignId('warehouse_invoice_id')->nullable()->after('invoice_number');
            $table->foreign('warehouse_invoice_id')->references('id')->on('warehouse_invoices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_trips', function (Blueprint $table) {
            $table->dropForeign('warehouse_trips_warehouse_invoice_id_foreign');
            $table->dropColumn('warehouse_invoice_id');
        });
    }
};
