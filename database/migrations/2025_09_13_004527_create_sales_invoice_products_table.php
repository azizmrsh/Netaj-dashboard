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
        Schema::create('sales_invoice_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_invoice_id')->comment('Sales Invoice ID');
            $table->unsignedBigInteger('product_id')->comment('Product ID');
            $table->unsignedBigInteger('delivery_document_product_id')->nullable()->comment('Delivery Document Product ID');
            $table->decimal('sold_quantity', 10, 3)->comment('Sold Quantity');
            $table->date('sale_date')->comment('Sale Date');
            $table->decimal('unit_price', 10, 2)->comment('Unit Price');
            $table->decimal('tax_rate', 5, 2)->default(15)->comment('Tax Rate %');
            $table->decimal('tax_amount', 10, 2)->default(0)->comment('Tax Amount');
            $table->decimal('unit_price_with_tax', 10, 2)->default(0)->comment('Unit Price with Tax');
            $table->decimal('subtotal', 10, 2)->default(0)->comment('Subtotal');
            $table->decimal('total_tax', 10, 2)->default(0)->comment('Total Tax');
            $table->decimal('total_with_tax', 10, 2)->default(0)->comment('Total with Tax');
            $table->text('notes')->nullable()->comment('Notes');
            $table->timestamps();
            
            $table->index(['sales_invoice_id', 'product_id']);
            $table->index('sale_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_products');
    }
};
