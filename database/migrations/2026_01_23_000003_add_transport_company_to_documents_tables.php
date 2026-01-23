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
        Schema::table('receipt_documents', function (Blueprint $table) {
            $table->foreignId('transport_company_id')->nullable()->after('id_customer')->constrained('transport_companies')->nullOnDelete();
            $table->string('driver_name')->nullable()->after('id_transporter');
            $table->string('car_no')->nullable()->after('driver_name');
            $table->string('driver_id_number')->nullable()->after('car_no');
            $table->string('driver_phone')->nullable()->after('driver_id_number');
        });

        Schema::table('delivery_documents', function (Blueprint $table) {
            $table->foreignId('transport_company_id')->nullable()->after('id_customer')->constrained('transport_companies')->nullOnDelete();
            $table->string('driver_name')->nullable()->after('id_transporter');
            $table->string('car_no')->nullable()->after('driver_name');
            $table->string('driver_id_number')->nullable()->after('car_no');
            $table->string('driver_phone')->nullable()->after('driver_id_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_documents', function (Blueprint $table) {
            $table->dropForeign(['transport_company_id']);
            $table->dropColumn(['transport_company_id', 'driver_name', 'car_no', 'driver_id_number', 'driver_phone']);
        });

        Schema::table('delivery_documents', function (Blueprint $table) {
            $table->dropForeign(['transport_company_id']);
            $table->dropColumn(['transport_company_id', 'driver_name', 'car_no', 'driver_id_number', 'driver_phone']);
        });
    }
};
