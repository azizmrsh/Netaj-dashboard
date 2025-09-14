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
        Schema::table('receipt_document_products', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->nullable()->after('quantity')->comment('Unit Price');
            $table->decimal('tax_rate', 5, 2)->default(15.00)->after('unit_price')->comment('Tax Rate Percentage');
            $table->decimal('tax_amount', 10, 4)->nullable()->after('tax_rate')->comment('Tax Amount Per Unit');
            $table->decimal('unit_price_with_tax', 10, 2)->nullable()->after('tax_amount')->comment('Unit Price with Tax');
            $table->decimal('subtotal', 10, 2)->nullable()->after('unit_price_with_tax')->comment('Subtotal Before Tax');
            $table->decimal('total_tax', 10, 2)->nullable()->after('subtotal')->comment('Total Tax');
            $table->decimal('total_with_tax', 10, 2)->nullable()->after('total_tax')->comment('Total with Tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_document_products', function (Blueprint $table) {
            $table->dropColumn([
                'unit_price',
                'tax_rate',
                'tax_amount',
                'unit_price_with_tax',
                'subtotal',
                'total_tax',
                'total_with_tax'
            ]);
        });
    }
};
