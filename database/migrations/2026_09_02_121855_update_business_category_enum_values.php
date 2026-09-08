<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE trip_logs MODIFY COLUMN business_category ENUM('Open Market Work', 'Buyer Supply Chain', 'Buyer Breading', 'Buyer Seed Supply', 'Buyer Marketing Development', 'Buyer S.P.R', 'Syngenta', 'Syngenta Breading') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE trip_logs MODIFY COLUMN business_category ENUM('Open Market Work', 'Buyer Supply Chain', 'Buyer Branding', 'Buyer Seed Supply') NULL");
    }
};
