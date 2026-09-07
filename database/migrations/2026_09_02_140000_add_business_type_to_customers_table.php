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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'business_type')) {
                $table->string('business_type')->nullable()->after('guarantor');
            }
            if (!Schema::hasColumn('customers', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('name');
            }
            if (!Schema::hasColumn('customers', 'contact_phone')) {
                $table->string('contact_phone')->nullable()->after('contact_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'business_type')) {
                $table->dropColumn('business_type');
            }
            if (Schema::hasColumn('customers', 'contact_person')) {
                $table->dropColumn('contact_person');
            }
            if (Schema::hasColumn('customers', 'contact_phone')) {
                $table->dropColumn('contact_phone');
            }
        });
    }
};
