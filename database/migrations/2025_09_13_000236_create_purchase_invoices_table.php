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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            
            // Invoice Information
            $table->string('invoice_no')->unique()->comment('Invoice Number');
            $table->dateTime('date_and_time')->comment('Invoice Date and Time');
            
            // Foreign key to receipt_documents table
            $table->unsignedBigInteger('id_receipt_documents')->comment('Receipt Document Reference');
            $table->foreign('id_receipt_documents')->references('id')->on('receipt_documents')->onDelete('cascade');
            
            // Invoice Details
            $table->string('payment_terms')->comment('Payment Terms');
            $table->string('place_of_supply')->nullable()->comment('Place of Supply');
            $table->string('buyers_order_no')->nullable()->comment('Purchase Order Number');
            
            // Financial Totals
            $table->decimal('subtotal_amount', 15, 2)->default(0)->comment('Subtotal Amount Before Tax');
            $table->decimal('total_tax_amount', 15, 2)->default(0)->comment('Total Tax Amount');
            $table->decimal('total_amount_with_tax', 15, 2)->default(0)->comment('Total Amount Including Tax');
            
            // Additional Information
            $table->text('note')->nullable()->comment('Additional Notes');
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('invoice_no');
            $table->index('date_and_time');
            $table->index('id_receipt_documents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
