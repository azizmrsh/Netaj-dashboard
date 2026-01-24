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
            $table->string('order_no')->nullable()->after('purchase_invoice_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_documents', function (Blueprint $table) {
            $table->dropColumn('order_no');
        });
    }
};
