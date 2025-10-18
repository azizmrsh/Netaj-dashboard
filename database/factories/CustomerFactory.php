<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $arabicCompanies = [
            'شركة النجاح للتجارة العامة',
            'مؤسسة الأمل للمواد الغذائية',
            'شركة الفجر للاستيراد والتصدير',
            'مجموعة الرياض التجارية',
            'شركة الخليج للصناعات الغذائية',
            'مؤسسة البركة للتوزيع',
            'شركة النور للمواد الاستهلاكية',
            'مجموعة الشرق الأوسط التجارية',
            'شركة الوطن للاستثمار',
            'مؤسسة السلام للتجارة',
        ];

        $arabicNames = [
            'أحمد محمد العلي',
            'فاطمة أحمد الزهراني',
            'محمد عبدالله السعيد',
            'نورا خالد المطيري',
            'عبدالرحمن صالح القحطاني',
            'مريم عبدالعزيز الشمري',
            'خالد محمد البلوي',
            'سارة أحمد الغامدي',
            'عبدالله سعد الدوسري',
            'هند محمد العتيبي',
        ];

        $cities = [
            'الرياض', 'جدة', 'الدمام', 'مكة المكرمة', 'المدينة المنورة',
            'الطائف', 'تبوك', 'بريدة', 'خميس مشيط', 'حائل'
        ];

        $type = $this->faker->randomElement([Customer::TYPE_CUSTOMER, Customer::TYPE_SUPPLIER, Customer::TYPE_BOTH]);
        
        return [
            'type' => $type,
            'name' => $this->faker->randomElement($arabicNames),
            'phone' => '+966' . $this->faker->numerify('#########'),
            'email' => $this->faker->unique()->safeEmail(),
            'note' => $this->faker->optional(0.3)->sentence(),
            'name_company' => $this->faker->randomElement($arabicCompanies),
            'country' => 'المملكة العربية السعودية',
            'address' => $this->faker->randomElement($cities) . ' - ' . $this->faker->streetAddress(),
            'tax_number' => $this->faker->numerify('##########'),
            'zip_code' => $this->faker->numerify('#####'),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'national_number' => $this->faker->numerify('##########'),
            'commercial_registration_number' => $this->faker->numerify('##########'),
        ];
    }

    /**
     * Indicate that the customer is a supplier.
     */
    public function supplier(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Customer::TYPE_SUPPLIER,
        ]);
    }

    /**
     * Indicate that the customer is a customer.
     */
    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Customer::TYPE_CUSTOMER,
        ]);
    }

    /**
     * Indicate that the customer can be both supplier and customer.
     */
    public function both(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Customer::TYPE_BOTH,
        ]);
    }

    /**
     * Indicate that the customer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}