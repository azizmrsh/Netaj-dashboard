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
        Schema::create('delivery_documents', function (Blueprint $table) {
            // Primary key
            $table->id(); // Auto-incrementing primary key
            
            // Main transaction fields
            $table->dateTime('date_and_time')->comment('Transaction timestamp'); // NOT NULL by default
            
            // Foreign key relationships (NOT NULL)
            $table->foreignId('id_customer')
                  ->constrained('customers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Reference to customer table');
            
            $table->foreignId('id_transporter')
                  ->constrained('transporters')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Reference to transporter table');
            
            $table->foreignId('id_product')
                  ->constrained('products')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Reference to product table');
            
            // Product quantity (NOT NULL)
            $table->integer('product_quantity')
                  ->unsigned()
                  ->comment('Quantity of the associated product');
            
            // Officer information (nullable text fields)
            $table->text('purchasing_officer_name')
                  ->nullable()
                  ->comment('Name of the purchasing officer');
            
            $table->string('purchasing_officer_signature')
                  ->nullable()
                  ->comment('Signature of the purchasing officer');
            
            $table->text('warehouse_officer_name')
                  ->nullable()
                  ->comment('Name of the warehouse officer');
            
            $table->string('warehouse_officer_signature')
                  ->nullable()
                  ->comment('Signature of the warehouse officer');
            
            $table->text('recipient_name')
                  ->nullable()
                  ->comment('Name of the recipient');
            
            $table->string('recipient_signature')
                  ->nullable()
                  ->comment('Signature of the recipient');
            
            $table->text('accountant_name')
                  ->nullable()
                  ->comment('Name of the accountant');
            
            $table->string('accountant_signature')
                  ->nullable()
                  ->comment('Signature of the accountant');
            
            // Additional required fields
            $table->text('purchase_order_no')
                  ->comment('Purchase order number');
            
            $table->text('project_name_and_location')
                  ->comment('Project name and location details');
            
            // Optional note field
            $table->text('note')
                  ->nullable()
                  ->comment('Additional comments or notes');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for frequently queried columns
            $table->index('date_and_time', 'idx_delivery_date_time');
            $table->index('id_customer', 'idx_delivery_customer');
            $table->index('id_transporter', 'idx_delivery_transporter');
            $table->index('id_product', 'idx_delivery_product');
            $table->index('purchase_order_no', 'idx_delivery_purchase_order');
            $table->index(['date_and_time', 'id_customer'], 'idx_delivery_date_customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_documents');
    }
};
