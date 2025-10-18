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
        // Drop foreign key constraints that reference suppliers table
        if (Schema::hasTable('suppliers')) {
            Schema::table('suppliers', function (Blueprint $table) {
                if (Schema::hasColumn('suppliers', 'customer_id')) {
                    $table->dropForeign(['customer_id']);
                }
            });
        }

        // Drop old supplier_id columns from document tables
        if (Schema::hasTable('receipt_documents') && Schema::hasColumn('receipt_documents', 'id_supplier')) {
            Schema::table('receipt_documents', function (Blueprint $table) {
                $table->dropForeign(['id_supplier']);
                $table->dropColumn('id_supplier');
            });
        }

        // Drop any other supplier_id references
        $tablesToClean = [
            'purchase_invoices' => 'supplier_id',
            'purchase_orders' => 'supplier_id',
        ];

        foreach ($tablesToClean as $table => $column) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->dropForeign([$column]);
                    $table->dropColumn($column);
                });
            }
        }

        // Finally, drop the suppliers table
        Schema::dropIfExists('suppliers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the suppliers table structure
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('note')->nullable();
            $table->string('name_company')->nullable();
            $table->string('country')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('zip_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('national_number')->nullable();
            $table->string('commercial_registration_number')->nullable();
            $table->boolean('is_customer')->default(false);
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index('phone');
            $table->index('email');
            $table->index('is_active');
            $table->index('is_customer');
            
            // Foreign key
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });

        // Note: Data restoration would require a backup
        // This down migration only recreates the structure
    }
};
