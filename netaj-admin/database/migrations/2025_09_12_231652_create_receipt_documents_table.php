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
        Schema::create('receipt_documents', function (Blueprint $table) {
            $table->id();
            
            // Main fields
            $table->dateTime('date_and_time');
            
            // Foreign key relationships
            $table->foreignId('id_supplier')
                  ->constrained('suppliers')
                  ->onDelete('cascade');
            
            $table->foreignId('id_transporter')
                  ->constrained('transporters')
                  ->onDelete('cascade');
            
            // Officer information (nullable)
            $table->text('purchasing_officer_name')->nullable();
            $table->text('purchasing_officer_signature')->nullable();
            $table->text('warehouse_officer_name')->nullable();
            $table->text('warehouse_officer_signature')->nullable();
            $table->text('recipient_name')->nullable();
            $table->text('recipient_signature')->nullable();
            $table->text('accountant_name')->nullable();
            $table->text('accountant_signature')->nullable();
            
            // Additional fields
            $table->text('purchase_invoice_no')->nullable();
            $table->text('note')->nullable();
            $table->text('material_source')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('date_and_time');
            $table->index('id_supplier');
            $table->index('id_transporter');
            $table->index('purchase_invoice_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_documents');
    }
};
