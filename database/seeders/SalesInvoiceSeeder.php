<?php

namespace Database\Seeders;

use App\Models\SalesInvoice;
use Illuminate\Database\Seeder;

class SalesInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 80 regular sales invoices
        SalesInvoice::factory(80)->withExistingRelations()->create();
        
        // Create 10 paid invoices
        SalesInvoice::factory(10)->withExistingRelations()->paid()->create();
        
        // Create 10 overdue invoices
        SalesInvoice::factory(10)->withExistingRelations()->overdue()->create();
    }
}