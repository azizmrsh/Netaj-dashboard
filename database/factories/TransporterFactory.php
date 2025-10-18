<?php

namespace Database\Factories;

use App\Models\Transporter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transporter>
 */
class TransporterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $transporterCompanies = [
            'شركة النقل السريع',
            'مؤسسة الخليج للنقل',
            'شركة الرياض للشحن',
            'مجموعة الشرق للنقل',
            'شركة الأمان للنقل',
            'مؤسسة الفهد للشحن',
            'شركة النجم للنقل',
            'مجموعة الوطن للشحن',
            'شركة السلام للنقل',
            'مؤسسة الحرمين للشحن',
        ];

        $driverNames = [
            'محمد أحمد الشهري',
            'عبدالله سعد القحطاني',
            'خالد محمد الغامدي',
            'سعد عبدالرحمن العتيبي',
            'أحمد خالد الدوسري',
            'محمد عبدالعزيز المطيري',
            'عبدالرحمن أحمد الزهراني',
            'سعد محمد البلوي',
            'خالد عبدالله الشمري',
            'أحمد سعد الحربي',
        ];

        return [
            'name' => $this->faker->randomElement($transporterCompanies),
            'phone' => '+966' . $this->faker->numerify('#########'),
            'email' => $this->faker->unique()->safeEmail(),
            'note' => $this->faker->optional(0.3)->sentence(),
            'id_number' => $this->faker->numerify('##########'),
            'tax_number' => $this->faker->numerify('##########'),
            'driver_name' => $this->faker->randomElement($driverNames),
            'document_no' => 'DOC-' . $this->faker->numerify('######'),
            'car_no' => $this->faker->regexify('[A-Z]{3}') . '-' . $this->faker->numerify('####'),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Indicate that the transporter is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}