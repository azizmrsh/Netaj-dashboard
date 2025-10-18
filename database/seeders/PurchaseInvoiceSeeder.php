<?php

namespace Database\Seeders;

use App\Models\PurchaseInvoice;
use Illuminate\Database\Seeder;

class PurchaseInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 100 purchase invoices with existing relations
        PurchaseInvoice::factory(100)->withExistingRelations()->create();
    }
}