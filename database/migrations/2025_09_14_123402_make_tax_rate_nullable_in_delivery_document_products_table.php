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
        Schema::table('delivery_document_products', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->nullable()->default(15.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_document_products', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->nullable(false)->default(15.00)->change();
        });
    }
};
