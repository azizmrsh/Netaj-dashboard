<?php

namespace Database\Seeders;

use App\Models\ReceiptDocument;
use App\Models\ReceiptDocumentProduct;
use Illuminate\Database\Seeder;

class ReceiptDocumentProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all receipt documents and products
        $receiptDocuments = ReceiptDocument::all();
        $allProducts = \App\Models\Product::pluck('id')->toArray();
        
        foreach ($receiptDocuments as $receiptDocument) {
            // Create 2-8 products for each receipt document
            $productCount = rand(2, min(8, count($allProducts)));
            
            // Get random unique products for this receipt document
            $selectedProducts = array_rand(array_flip($allProducts), $productCount);
            if (!is_array($selectedProducts)) {
                $selectedProducts = [$selectedProducts];
            }
            
            foreach ($selectedProducts as $productId) {
                ReceiptDocumentProduct::factory()
                    ->create([
                        'receipt_document_id' => $receiptDocument->id,
                        'product_id' => $productId,
                    ]);
            }
        }
    }
}