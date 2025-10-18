<?php

namespace Database\Seeders;

use App\Models\ReceiptDocument;
use Illuminate\Database\Seeder;

class ReceiptDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 100 receipt documents with existing relations
        ReceiptDocument::factory(100)->withExistingRelations()->create();
    }
}