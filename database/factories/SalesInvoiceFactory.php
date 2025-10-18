<?php

namespace Database\Factories;

use App\Models\SalesInvoice;
use App\Models\DeliveryDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesInvoice>
 */
class SalesInvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 1000, 50000);
        $taxRate = $this->faker->randomElement([0, 5, 15]);
        $taxAmount = $subtotal * ($taxRate / 100);
        $discountAmount = $this->faker->randomFloat(2, 0, $subtotal * 0.1);
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        $customerNames = [
            'شركة البناء المتطور المحدودة',
            'مؤسسة الإنشاءات الحديثة',
            'شركة المقاولات العامة',
            'مجموعة التطوير العقاري',
            'شركة الهندسة والبناء',
            'مؤسسة المشاريع الكبرى',
        ];

        $addresses = [
            'الرياض - حي الملك فهد - شارع الملك عبدالعزيز',
            'جدة - حي الروضة - طريق الملك عبدالله',
            'الدمام - حي الفيصلية - شارع الأمير محمد بن فهد',
            'مكة - حي العزيزية - طريق مكة جدة السريع',
            'المدينة - حي قباء - شارع سيد الشهداء',
            'الطائف - حي الشهداء - طريق الرياض الطائف',
        ];

        $paymentMethods = [
            'نقداً',
            'تحويل بنكي',
            'شيك',
            'بطاقة ائتمان',
            'دفع إلكتروني',
        ];

        $statuses = ['draft', 'sent', 'paid', 'cancelled']; // Must match database enum values

        $invoiceDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $dueDate = $this->faker->dateTimeBetween($invoiceDate, '+3 months');

        return [
            'invoice_no' => 'SI-' . $this->faker->unique()->numerify('######'),
            'delivery_document_id' => DeliveryDocument::factory(),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'customer_name' => $this->faker->randomElement($customerNames),
            'customer_address' => $this->faker->randomElement($addresses),
            'customer_phone' => '+966' . $this->faker->numerify('#########'),
            'customer_tax_number' => $this->faker->numerify('##########'),
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'status' => $this->faker->randomElement($statuses),
            'notes' => $this->faker->optional(0.3)->sentence(),
            'payment_method' => $this->faker->randomElement($paymentMethods),
            'payment_date' => $this->faker->optional(0.6)->dateTimeBetween($invoiceDate, 'now'),
        ];
    }

    /**
     * Configure the model factory to use existing delivery documents.
     */
    public function withExistingRelations(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'delivery_document_id' => DeliveryDocument::inRandomOrder()->first()?->id ?? DeliveryDocument::factory(),
            ];
        });
    }

    /**
     * Create a paid invoice.
     */
    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'paid',
                'payment_date' => $this->faker->dateTimeBetween($attributes['invoice_date'], 'now'),
            ];
        });
    }

    /**
     * Create an overdue invoice (sent but not paid).
     */
    public function overdue(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'sent', // Use 'sent' status for overdue invoices
                'due_date' => $this->faker->dateTimeBetween('-3 months', '-1 day'),
                'payment_date' => null,
            ];
        });
    }
}