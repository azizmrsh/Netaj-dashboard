<?php

namespace Database\Factories;

use App\Models\ReceiptDocumentProduct;
use App\Models\ReceiptDocument;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReceiptDocumentProduct>
 */
class ReceiptDocumentProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(3, 1, 100);
        $unitPrice = $this->faker->randomFloat(2, 5, 500);
        $taxRate = $this->faker->randomElement([0, 5, 15]); // Common tax rates
        
        // Calculate derived values
        $subtotal = $quantity * $unitPrice;
        $taxAmount = $subtotal * ($taxRate / 100);
        $unitPriceWithTax = $unitPrice * (1 + ($taxRate / 100));
        $totalTax = $taxAmount;
        $totalWithTax = $subtotal + $totalTax;

        return [
            'receipt_document_id' => ReceiptDocument::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'unit_price_with_tax' => $unitPriceWithTax,
            'subtotal' => $subtotal,
            'total_tax' => $totalTax,
            'total_with_tax' => $totalWithTax,
        ];
    }

    /**
     * Configure the model factory to use existing receipt documents and products.
     */
    public function withExistingRelations(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'receipt_document_id' => ReceiptDocument::inRandomOrder()->first()?->id ?? ReceiptDocument::factory(),
                'product_id' => Product::inRandomOrder()->first()?->id ?? Product::factory(),
            ];
        });
    }

    /**
     * Create a product with no tax.
     */
    public function noTax(): static
    {
        return $this->state(function (array $attributes) {
            $quantity = $attributes['quantity'];
            $unitPrice = $attributes['unit_price'];
            $subtotal = $quantity * $unitPrice;
            
            return [
                'tax_rate' => 0,
                'tax_amount' => 0,
                'unit_price_with_tax' => $unitPrice,
                'subtotal' => $subtotal,
                'total_tax' => 0,
                'total_with_tax' => $subtotal,
            ];
        });
    }
}