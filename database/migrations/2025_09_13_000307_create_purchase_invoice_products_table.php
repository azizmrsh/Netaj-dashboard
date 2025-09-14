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
        Schema::create('purchase_invoice_products', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->unsignedBigInteger('purchase_invoice_id')->comment('Purchase Invoice ID');
            $table->unsignedBigInteger('product_id')->comment('Product ID');
            $table->unsignedBigInteger('receipt_document_product_id')->comment('Receipt Document Product ID');
            
            // Product and delivery information
            $table->decimal('received_quantity', 10, 3)->comment('Received Quantity');
            $table->dateTime('delivery_date')->comment('Delivery Date');
            
            // Pricing information
            $table->decimal('unit_price', 12, 4)->comment('Unit Price Before Tax');
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('Tax Rate %');
            $table->decimal('tax_amount', 12, 4)->default(0)->comment('Tax Amount');
            $table->decimal('unit_price_with_tax', 12, 4)->comment('Unit Price Including Tax');
            
            // Calculated totals
            $table->decimal('subtotal', 15, 2)->comment('Subtotal Before Tax');
            $table->decimal('total_tax', 15, 2)->comment('Total Tax');
            $table->decimal('total_with_tax', 15, 2)->comment('Total Including Tax');
            
            // Additional information
            $table->text('notes')->nullable()->comment('Product Notes');
            
            $table->timestamps();
            
            // Foreign Key Constraints
            $table->foreign('purchase_invoice_id')->references('id')->on('purchase_invoices')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('receipt_document_product_id')->references('id')->on('receipt_document_products')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('purchase_invoice_id');
            $table->index('product_id');
            $table->index('receipt_document_product_id');
            $table->index('delivery_date');
            
            // Unique constraint to prevent duplicate products in same invoice
            $table->unique(['purchase_invoice_id', 'receipt_document_product_id'], 'unique_invoice_receipt_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoice_products');
    }
};
