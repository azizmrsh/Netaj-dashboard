<?php

namespace Database\Seeders;

use App\Models\DeliveryDocument;
use App\Models\DeliveryDocumentProduct;
use Illuminate\Database\Seeder;

class DeliveryDocumentProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all delivery documents and products
        $deliveryDocuments = DeliveryDocument::all();
        $allProducts = \App\Models\Product::pluck('id')->toArray();
        
        foreach ($deliveryDocuments as $deliveryDocument) {
            // Create 2-8 products for each delivery document
            $productCount = rand(2, min(8, count($allProducts)));
            
            // Get random unique products for this delivery document
            $selectedProducts = array_rand(array_flip($allProducts), $productCount);
            if (!is_array($selectedProducts)) {
                $selectedProducts = [$selectedProducts];
            }
            
            foreach ($selectedProducts as $productId) {
                DeliveryDocumentProduct::factory()
                    ->create([
                        'delivery_document_id' => $deliveryDocument->id,
                        'product_id' => $productId,
                    ]);
            }
        }
    }
}