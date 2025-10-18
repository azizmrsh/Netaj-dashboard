<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $arabicProducts = [
            'أرز بسمتي فاخر',
            'زيت زيتون بكر ممتاز',
            'دقيق أبيض منخول',
            'سكر أبيض ناعم',
            'شاي أحمر سيلاني',
            'قهوة عربية محمصة',
            'عدس أحمر مجروش',
            'حمص حب كامل',
            'فول مدمس معلب',
            'معكرونة إيطالية',
            'صلصة طماطم طبيعية',
            'زيت عباد الشمس',
            'ملح طعام مكرر',
            'فلفل أسود مطحون',
            'كمون مطحون',
            'هيل حب كامل',
            'قرفة عيدان',
            'زعتر مجفف',
            'سمسم محمص',
            'طحينة سائلة',
        ];

        $descriptions = [
            'منتج عالي الجودة مستورد من أفضل المصادر',
            'منتج طبيعي 100% خالي من المواد الحافظة',
            'منتج محلي ممتاز يلبي أعلى معايير الجودة',
            'منتج مصنع وفقاً للمواصفات العالمية',
            'منتج طازج ومعبأ بأحدث التقنيات',
        ];

        $units = ['ton', 'barrel']; // Must match database enum values
        $grades = ['ممتاز', 'جيد جداً', 'جيد', 'عادي'];
        $modifications = ['طبيعي', 'محسن', 'مطور', 'خاص'];

        return [
            'name' => $this->faker->randomElement($arabicProducts),
            'description' => $this->faker->randomElement($descriptions),
            'product_code' => 'PRD-' . $this->faker->unique()->numerify('####'),
            'performance_grade' => $this->faker->randomElement($grades),
            'modification_type' => $this->faker->randomElement($modifications),
            'unit' => $this->faker->randomElement($units),
            'is_active' => $this->faker->boolean(95), // 95% chance of being active
            'price1' => $this->faker->randomFloat(2, 5, 500), // Price between 5 and 500
            'price2' => $this->faker->randomFloat(2, 5, 500), // Price between 5 and 500
        ];
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the product is premium grade.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'performance_grade' => 'ممتاز',
            'price1' => $this->faker->randomFloat(2, 100, 1000),
            'price2' => $this->faker->randomFloat(2, 100, 1000),
        ]);
    }
}