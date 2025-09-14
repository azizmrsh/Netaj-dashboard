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
        Schema::table('delivery_documents', function (Blueprint $table) {
            $table->dropForeign(['id_product']);
            $table->dropColumn(['id_product', 'product_quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_documents', function (Blueprint $table) {
            $table->foreignId('id_product')
                  ->constrained('products')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Reference to product table');
            
            $table->integer('product_quantity')
                  ->unsigned()
                  ->comment('Quantity of the associated product');
        });
    }
};
