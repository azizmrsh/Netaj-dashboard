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
        Schema::table('customers', function (Blueprint $table) {
            // Add the type ENUM column
            $table->enum('type', ['customer', 'supplier', 'both'])
                  ->default('customer')
                  ->after('id');
            
            // Add index for better performance
            $table->index('type');
        });
        
        // Remove the old supplier relationship fields
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['is_supplier', 'supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the old supplier relationship fields
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('is_supplier')->default(false)->after('id');
            $table->unsignedBigInteger('supplier_id')->nullable()->after('is_supplier');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
        
        // Remove the type column
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }
};