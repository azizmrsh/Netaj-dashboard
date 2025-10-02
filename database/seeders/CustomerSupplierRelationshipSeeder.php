<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Supplier;

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
        ]);

        // إنشاء مورد عادي
        $supplier1 = Supplier::create([
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
        ]);

        // إنشاء عميل يصبح مورد أيضاً
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
        ]);

        // إنشاء مورد مرتبط بالعميل السابق
        $supplier2 = new Supplier();
        $supplier2->copyFromCustomer($customer2);
        $supplier2->note = 'مورد تم إنشاؤه من عميل موجود';
        $supplier2->save();

        // تحديث العميل ليشير إلى المورد
        $customer2->update([
            'is_supplier' => true,
            'supplier_id' => $supplier2->id,
        ]);

        // إنشاء مورد يصبح عميل أيضاً
        $supplier3 = Supplier::create([
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
        ]);

        // إنشاء عميل مرتبط بالمورد السابق
        $customer3 = new Customer();
        $customer3->copyFromSupplier($supplier3);
        $customer3->note = 'عميل تم إنشاؤه من مورد موجود';
        $customer3->save();

        // تحديث المورد ليشير إلى العميل
        $supplier3->update([
            'is_customer' => true,
            'customer_id' => $customer3->id,
        ]);

        echo "تم إنشاء البيانات التجريبية بنجاح:\n";
        echo "- عميل عادي: {$customer1->name}\n";
        echo "- مورد عادي: {$supplier1->name}\n";
        echo "- عميل ومورد: {$customer2->name} (ID: {$customer2->id}) <-> {$supplier2->name} (ID: {$supplier2->id})\n";
        echo "- مورد وعميل: {$supplier3->name} (ID: {$supplier3->id}) <-> {$customer3->name} (ID: {$customer3->id})\n";
    }
}
