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
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique()->comment('Invoice Number');
            $table->foreignId('delivery_document_id')->constrained('delivery_documents')->onDelete('cascade')->comment('Delivery Document ID');
            $table->date('invoice_date')->comment('Invoice Date');
            $table->date('due_date')->nullable()->comment('Due Date');
            $table->string('customer_name')->comment('Customer Name');
            $table->text('customer_address')->nullable()->comment('Customer Address');
            $table->string('customer_phone')->nullable()->comment('Customer Phone');
            $table->string('customer_tax_number')->nullable()->comment('Customer Tax Number');
            $table->decimal('subtotal', 15, 2)->default(0)->comment('Subtotal');
            $table->decimal('tax_rate', 5, 2)->default(15)->comment('Tax Rate %');
            $table->decimal('tax_amount', 15, 2)->default(0)->comment('Tax Amount');
            $table->decimal('discount_amount', 15, 2)->default(0)->comment('Discount Amount');
            $table->decimal('total_amount', 15, 2)->default(0)->comment('Total Amount');
            $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft')->comment('Invoice Status');
            $table->text('notes')->nullable()->comment('Notes');
            $table->string('payment_method')->nullable()->comment('Payment Method');
            $table->date('payment_date')->nullable()->comment('Payment Date');
            $table->timestamps();
            
            $table->index(['invoice_date', 'status']);
            $table->index('customer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
