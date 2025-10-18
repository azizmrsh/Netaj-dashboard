<?php

namespace Database\Factories;

use App\Models\ReceiptDocument;
use App\Models\Customer;
use App\Models\Transporter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReceiptDocument>
 */
class ReceiptDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $officerNames = [
            'أحمد محمد العلي',
            'فاطمة سعد الزهراني',
            'محمد عبدالله القحطاني',
            'نورا خالد الغامدي',
            'عبدالرحمن صالح الدوسري',
            'مريم عبدالعزيز المطيري',
            'خالد محمد الشمري',
            'سارة أحمد البلوي',
            'عبدالله سعد العتيبي',
            'هند محمد الحربي',
        ];

        $materialSources = [
            'مستودع الرياض الرئيسي',
            'مستودع جدة الفرعي',
            'مستودع الدمام',
            'مستودع مكة المكرمة',
            'مستودع المدينة المنورة',
            'مورد خارجي - الشركة الأولى',
            'مورد خارجي - الشركة الثانية',
            'مصنع محلي',
        ];

        return [
            'date_and_time' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'id_customer' => Customer::factory(),
            'id_transporter' => Transporter::factory(),
            'purchasing_officer_name' => $this->faker->randomElement($officerNames),
            'purchasing_officer_signature' => null,
            'warehouse_officer_name' => $this->faker->randomElement($officerNames),
            'warehouse_officer_signature' => null,
            'recipient_name' => $this->faker->randomElement($officerNames),
            'recipient_signature' => null,
            'accountant_name' => $this->faker->randomElement($officerNames),
            'accountant_signature' => null,
            'purchase_invoice_no' => 'INV-' . $this->faker->unique()->numerify('######'),
            'material_source' => $this->faker->randomElement($materialSources),
            'note' => $this->faker->optional(0.4)->sentence(),
        ];
    }

    /**
     * Configure the model factory to use existing customers and transporters.
     */
    public function withExistingRelations(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'id_customer' => Customer::inRandomOrder()->first()?->id ?? Customer::factory(),
                'id_transporter' => Transporter::inRandomOrder()->first()?->id ?? Transporter::factory(),
            ];
        });
    }
}