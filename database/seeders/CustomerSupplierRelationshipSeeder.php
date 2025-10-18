<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSupplierRelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء عميل عادي
        $customer1 = Customer::create([
            'name' => 'أحمد محمد',
            'phone' => '0123456789',
            'email' => 'ahmed@example.com',
            'name_company' => 'شركة أحمد للتجارة',
            'country' => 'مصر',
            'address' => 'القاهرة، مصر الجديدة',
            'tax_number' => '123456789',
            'zip_code' => '11341',
            'is_active' => true,
            'national_number' => '12345678901234',
            'commercial_registration_number' => 'CR123456',
            'type' => Customer::TYPE_CUSTOMER,
        ]);

        // إنشاء مورد عادي
        $supplier1 = Customer::create([
            'name' => 'سارة أحمد',
            'phone' => '0987654321',
            'email' => 'sara@example.com',
            'name_company' => 'شركة سارة للمواد الخام',
            'country' => 'الأردن',
            'address' => 'عمان، الأردن',
            'tax_number' => '987654321',
            'zip_code' => '11181',
            'is_active' => true,
            'national_number' => '98765432109876',
            'commercial_registration_number' => 'CR987654',
            'type' => Customer::TYPE_SUPPLIER,
        ]);

        // إنشاء عميل ومورد في نفس الوقت
        $customer2 = Customer::create([
            'name' => 'محمد علي',
            'phone' => '0555666777',
            'email' => 'mohamed@example.com',
            'name_company' => 'شركة محمد المتكاملة',
            'country' => 'السعودية',
            'address' => 'الرياض، السعودية',
            'tax_number' => '555666777',
            'zip_code' => '12345',
            'is_active' => true,
            'national_number' => '55566677788899',
            'commercial_registration_number' => 'CR555666',
            'type' => Customer::TYPE_BOTH,
            'note' => 'عميل ومورد في نفس الوقت',
        ]);

        // إنشاء مورد وعميل في نفس الوقت
        $supplier3 = Customer::create([
            'name' => 'فاطمة حسن',
            'phone' => '0777888999',
            'email' => 'fatima@example.com',
            'name_company' => 'شركة فاطمة للخدمات',
            'country' => 'لبنان',
            'address' => 'بيروت، لبنان',
            'tax_number' => '777888999',
            'zip_code' => '1107',
            'is_active' => true,
            'national_number' => '77788899900011',
            'commercial_registration_number' => 'CR777888',
            'type' => Customer::TYPE_BOTH,
            'note' => 'مورد وعميل في نفس الوقت',
        ]);

        echo "تم إنشاء البيانات التجريبية بنجاح:\n";
        echo "- عميل عادي: {$customer1->name} (النوع: " . $customer1->getTypeLabel() . ")\n";
        echo "- مورد عادي: {$supplier1->name} (النوع: " . $supplier1->getTypeLabel() . ")\n";
        echo "- عميل ومورد: {$customer2->name} (النوع: " . $customer2->getTypeLabel() . ")\n";
        echo "- مورد وعميل: {$supplier3->name} (النوع: " . $supplier3->getTypeLabel() . ")\n";
    }
}
