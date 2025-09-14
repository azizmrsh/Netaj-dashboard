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
        Schema::create('receipt_document_products', function (Blueprint $table) {
            $table->id();
            
            // Foreign key relationships
            $table->foreignId('receipt_document_id')
                  ->constrained('receipt_documents')
                  ->onDelete('cascade');
            
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');
            
            // Quantity field
            $table->decimal('quantity', 10, 3)->default(0);
            
            $table->timestamps();
            
            // Composite unique constraint to prevent duplicate product entries
            $table->unique(['receipt_document_id', 'product_id']);
            
            // Indexes for performance
            $table->index('receipt_document_id');
            $table->index('product_id');
            $table->index('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_document_products');
    }
};
