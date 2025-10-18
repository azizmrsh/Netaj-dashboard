<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip updating existing customers since they already have the type column
        // and we're migrating from suppliers table to customers table

        // Create a mapping table to track old supplier IDs to new customer IDs
        $supplierToCustomerMapping = [];

        // Get all suppliers and migrate them to customers table
        $suppliers = DB::table('suppliers')->get();
        
        foreach ($suppliers as $supplier) {
            // Check if this supplier already exists as a customer (by email or name)
            $existingCustomer = DB::table('customers')
                ->where(function($query) use ($supplier) {
                    if ($supplier->email) {
                        $query->where('email', $supplier->email);
                    } else {
                        $query->where('name', $supplier->name);
                    }
                })
                ->first();
            
            if ($existingCustomer) {
                // Update existing customer to type 'both'
                DB::table('customers')
                    ->where('id', $existingCustomer->id)
                    ->update(['type' => 'both']);
                
                $supplierToCustomerMapping[$supplier->id] = $existingCustomer->id;
            } else {
                // Create new customer record from supplier
                $newCustomerId = DB::table('customers')->insertGetId([
                    'type' => $supplier->is_customer ? 'both' : 'supplier',
                    'name' => $supplier->name,
                    'phone' => $supplier->phone,
                    'email' => $supplier->email,
                    'note' => $supplier->note,
                    'name_company' => $supplier->name_company,
                    'country' => $supplier->country,
                    'address' => $supplier->address,
                    'tax_number' => $supplier->tax_number,
                    'zip_code' => $supplier->zip_code,
                    'is_active' => $supplier->is_active,
                    'national_number' => $supplier->national_number,
                    'commercial_registration_number' => $supplier->commercial_registration_number,
                    'created_at' => $supplier->created_at,
                    'updated_at' => $supplier->updated_at,
                ]);
                
                $supplierToCustomerMapping[$supplier->id] = $newCustomerId;
            }
        }

        // Update all document references from supplier_id to customer_id
        $this->updateDocumentReferences($supplierToCustomerMapping);
    }

    /**
     * Update document references from suppliers to customers
     */
    private function updateDocumentReferences(array $supplierToCustomerMapping): void
    {
        // Update receipt_documents table
        if (Schema::hasTable('receipt_documents')) {
            // Add id_customer column if it doesn't exist
            if (!Schema::hasColumn('receipt_documents', 'id_customer')) {
                Schema::table('receipt_documents', function (Blueprint $table) {
                    $table->unsignedBigInteger('id_customer')->nullable()->after('id_supplier');
                    $table->foreign('id_customer')->references('id')->on('customers')->onDelete('cascade');
                });
            }
            
            // Now update the records
            foreach ($supplierToCustomerMapping as $oldSupplierId => $newCustomerId) {
                DB::table('receipt_documents')
                    ->where('id_supplier', $oldSupplierId)
                    ->update(['id_customer' => $newCustomerId]);
            }
        }

        // Update any other tables that reference suppliers
        $tablesToUpdate = [
            'purchase_invoices' => 'supplier_id',
            'purchase_orders' => 'supplier_id',
            // Add other tables as needed
        ];

        foreach ($tablesToUpdate as $table => $column) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                // Add customer_id column if it doesn't exist
                if (!Schema::hasColumn($table, 'customer_id')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->unsignedBigInteger('customer_id')->nullable();
                        $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                    });
                }
                
                // Now update the records
                foreach ($supplierToCustomerMapping as $oldSupplierId => $newCustomerId) {
                    DB::table($table)
                        ->where($column, $oldSupplierId)
                        ->update(['customer_id' => $newCustomerId]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a complex data migration that's difficult to reverse
        // In a production environment, you would need to backup data before running
        throw new Exception('This migration cannot be reversed. Please restore from backup if needed.');
    }
};
