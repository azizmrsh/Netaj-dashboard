<?php

namespace Database\Factories;

use App\Models\PurchaseInvoice;
use App\Models\ReceiptDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseInvoice>
 */
class PurchaseInvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotalAmount = $this->faker->randomFloat(2, 1000, 50000);
        $totalTaxAmount = $subtotalAmount * 0.15; // 15% tax
        $totalAmountWithTax = $subtotalAmount + $totalTaxAmount;

        $paymentTerms = [
            'نقداً عند الاستلام',
            'دفع خلال 30 يوم',
            'دفع خلال 60 يوم',
            'دفع مقدم 50%',
            'دفع على دفعتين',
            'دفع خلال 15 يوم',
        ];

        $placesOfSupply = [
            'الرياض - المملكة العربية السعودية',
            'جدة - المملكة العربية السعودية',
            'الدمام - المملكة العربية السعودية',
            'مكة المكرمة - المملكة العربية السعودية',
            'المدينة المنورة - المملكة العربية السعودية',
            'الطائف - المملكة العربية السعودية',
        ];

        return [
            'invoice_no' => 'INV-' . $this->faker->unique()->numerify('######'),
            'date_and_time' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'id_receipt_documents' => ReceiptDocument::factory(),
            'payment_terms' => $this->faker->randomElement($paymentTerms),
            'place_of_supply' => $this->faker->randomElement($placesOfSupply),
            'buyers_order_no' => 'BO-' . $this->faker->numerify('######'),
            'subtotal_amount' => $subtotalAmount,
            'total_tax_amount' => $totalTaxAmount,
            'total_amount_with_tax' => $totalAmountWithTax,
            'note' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Configure the model factory to use existing receipt documents.
     */
    public function withExistingRelations(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'id_receipt_documents' => ReceiptDocument::inRandomOrder()->first()?->id ?? ReceiptDocument::factory(),
            ];
        });
    }
}