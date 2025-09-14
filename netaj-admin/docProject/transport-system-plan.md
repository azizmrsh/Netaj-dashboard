# خطة عمل نظام إدارة النقل والمخزون

## المرحلة الأولى: إعداد قاعدة البيانات (Database Migrations)

### 1. إنشاء Migration للجداول الأساسية

#### 1.1 جدول المنتجات (Products)
- [ ] إنشاء migration: `create_products_table`
- [ ] الحقول المطلوبة:
  - `id` (Primary Key)
  - `name` (string, 255)
  - `sku` (string, 100, unique)
  - `is_active` (boolean, default true)
  - `created_at`, `updated_at` (timestamps)

#### 1.2 جدول الموردين (Suppliers)
- [ ] إنشاء migration: `create_suppliers_table`
- [ ] الحقول المطلوبة:
  - `id` (Primary Key)
  - `name` (string, 255)
  - `phone` (string, 20, nullable)
  - `vat_no` (string, 50, nullable)
  - `address` (text, nullable)
  - `is_active` (boolean, default true)
  - `created_at`, `updated_at` (timestamps)

#### 1.3 جدول العملاء (Customers)
- [ ] إنشاء migration: `create_customers_table`
- [ ] الحقول المطلوبة:
  - `id` (Primary Key)
  - `name` (string, 255)
  - `phone` (string, 20, nullable)
  - `vat_no` (string, 50, nullable)
  - `address` (text, nullable)
  - `is_active` (boolean, default true)
  - `created_at`, `updated_at` (timestamps)

#### 1.4 جدول سندات الاستلام (Goods Receipts)
- [ ] إنشاء migration: `create_goods_receipts_table`
- [ ] الحقول المطلوبة:
  - `id` (Primary Key)
  - `grn_no` (string, 50, unique)
  - `grn_date` (date)
  - `supplier_id` (Foreign Key → suppliers.id)
  - `notes` (text, nullable)
  - `status` (enum: 'draft', 'confirmed', 'cancelled')
  - `created_at`, `updated_at` (timestamps)

#### 1.5 جدول تفاصيل سندات الاستلام (Goods Receipt Lines)
- [ ] إنشاء migration: `create_goods_receipt_lines_table`
- [ ] الحقول المطلوبة:
  - `id` (Primary Key)
  - `grn_id` (Foreign Key → goods_receipts.id)
  - `product_id` (Foreign Key → products.id)
  - `qty` (decimal, 10,2)
  - `unit_price` (decimal, 10,2)
  - `created_at`, `updated_at` (timestamps)

#### 1.6 جدول فواتير المشتريات (Purchase Invoices)
- [ ] إنشاء migration: `create_purchase_invoices_table`
- [ ] الحقول المطلوبة:
  - `id` (Primary Key)
  - `invoice_no` (string, 50, unique)
  - `invoice_date` (date)
  - `supplier_id` (Foreign Key → suppliers.id)
  - `grn_id` (Foreign Key → goods_receipts.id)
  - `total_amount` (decimal, 12,2)
  - `currency` (string, 3, default 'USD')
  - `vat_rate` (decimal, 5,2, default 0)
  - `notes` (text, nullable)
  - `status` (enum: 'draft', 'confirmed', 'paid', 'cancelled')
  - `created_at`, `updated_at` (timestamps)

#### 1.7 جدول سندات التسليم (Delivery Notes)
- [ ] إنشاء migration: `create_delivery_notes_table`
- [ ] الحقول المطلوبة:
  - `id` (Primary Key)
  - `dn_no` (string, 50, unique)
  - `dn_date` (date)
  - `customer_id` (Foreign Key → customers.id)
  - `notes` (text, nullable)
  - `status` (enum: 'draft', 'confirmed', 'delivered', 'cancelled')
  - `created_at`, `updated_at` (timestamps)

#### 1.8 جدول تفاصيل سندات التسليم (Delivery Note Lines)
- [ ] إنشاء migration: `create_delivery_note_lines_table`
- [ ] الحقول المطلوبة:
  - `id` (Primary Key)
  - `dn_id` (Foreign Key → delivery_notes.id)
  - `product_id` (Foreign Key → products.id)
  - `qty` (decimal, 10,2)
  - `unit_price` (decimal, 10,2)
  - `created_at`, `updated_at` (timestamps)

#### 1.9 جدول فواتير المبيعات (Sales Invoices)
- [ ] إنشاء migration: `create_sales_invoices_table`
- [ ] الحقول المطلوبة:
  - `id` (Primary Key)
  - `invoice_no` (string, 50, unique)
  - `invoice_date` (date)
  - `customer_id` (Foreign Key → customers.id)
  - `dn_id` (Foreign Key → delivery_notes.id)
  - `total_amount` (decimal, 12,2)
  - `currency` (string, 3, default 'USD')
  - `vat_rate` (decimal, 5,2, default 0)
  - `notes` (text, nullable)
  - `status` (enum: 'draft', 'confirmed', 'paid', 'cancelled')
  - `created_at`, `updated_at` (timestamps)

---

## المرحلة الثانية: إنشاء النماذج (Eloquent Models)

### 2.1 إنشاء Model للمنتجات
- [ ] إنشاء `app/Models/Product.php`
- [ ] تعريف الخصائص القابلة للتعبئة (fillable)
- [ ] تعريف العلاقات:
  - `hasMany(GoodsReceiptLine::class)`
  - `hasMany(DeliveryNoteLine::class)`

### 2.2 إنشاء Model للموردين
- [ ] إنشاء `app/Models/Supplier.php`
- [ ] تعريف الخصائص القابلة للتعبئة
- [ ] تعريف العلاقات:
  - `hasMany(GoodsReceipt::class)`
  - `hasMany(PurchaseInvoice::class)`

### 2.3 إنشاء Model للعملاء
- [ ] إنشاء `app/Models/Customer.php`
- [ ] تعريف الخصائص القابلة للتعبئة
- [ ] تعريف العلاقات:
  - `hasMany(DeliveryNote::class)`
  - `hasMany(SalesInvoice::class)`

### 2.4 إنشاء Model لسندات الاستلام
- [ ] إنشاء `app/Models/GoodsReceipt.php`
- [ ] تعريف الخصائص القابلة للتعبئة
- [ ] تعريف العلاقات:
  - `belongsTo(Supplier::class)`
  - `hasMany(GoodsReceiptLine::class)`
  - `hasOne(PurchaseInvoice::class)`

### 2.5 إنشاء Model لتفاصيل سندات الاستلام
- [ ] إنشاء `app/Models/GoodsReceiptLine.php`
- [ ] تعريف الخصائص القابلة للتعبئة
- [ ] تعريف العلاقات:
  - `belongsTo(GoodsReceipt::class)`
  - `belongsTo(Product::class)`

### 2.6 إنشاء Model لفواتير المشتريات
- [ ] إنشاء `app/Models/PurchaseInvoice.php`
- [ ] تعريف الخصائص القابلة للتعبئة
- [ ] تعريف العلاقات:
  - `belongsTo(Supplier::class)`
  - `belongsTo(GoodsReceipt::class)`

### 2.7 إنشاء Model لسندات التسليم
- [ ] إنشاء `app/Models/DeliveryNote.php`
- [ ] تعريف الخصائص القابلة للتعبئة
- [ ] تعريف العلاقات:
  - `belongsTo(Customer::class)`
  - `hasMany(DeliveryNoteLine::class)`
  - `hasOne(SalesInvoice::class)`

### 2.8 إنشاء Model لتفاصيل سندات التسليم
- [ ] إنشاء `app/Models/DeliveryNoteLine.php`
- [ ] تعريف الخصائص القابلة للتعبئة
- [ ] تعريف العلاقات:
  - `belongsTo(DeliveryNote::class)`
  - `belongsTo(Product::class)`

### 2.9 إنشاء Model لفواتير المبيعات
- [ ] إنشاء `app/Models/SalesInvoice.php`
- [ ] تعريف الخصائص القابلة للتعبئة
- [ ] تعريف العلاقات:
  - `belongsTo(Customer::class)`
  - `belongsTo(DeliveryNote::class)`

---

## المرحلة الثالثة: إنشاء موارد Filament (Resources)

### 3.1 إنشاء Resource للمنتجات
- [ ] إنشاء `app/Filament/Resources/ProductResource.php`
- [ ] تكوين النموذج (Form) للإضافة والتعديل
- [ ] تكوين الجدول (Table) لعرض البيانات
- [ ] إضافة فلاتر البحث والتصفية
- [ ] إضافة إجراءات (Actions) مخصصة

### 3.2 إنشاء Resource للموردين
- [ ] إنشاء `app/Filament/Resources/SupplierResource.php`
- [ ] تكوين النموذج والجدول
- [ ] إضافة التحقق من صحة البيانات
- [ ] إضافة فلاتر للحالة النشطة/غير النشطة

### 3.3 إنشاء Resource للعملاء
- [ ] إنشاء `app/Filament/Resources/CustomerResource.php`
- [ ] تكوين النموذج والجدول
- [ ] إضافة التحقق من صحة البيانات
- [ ] إضافة فلاتر للحالة النشطة/غير النشطة

### 3.4 إنشاء Resource لسندات الاستلام
- [ ] إنشاء `app/Filament/Resources/GoodsReceiptResource.php`
- [ ] تكوين النموذج مع العلاقات (Supplier)
- [ ] إضافة Repeater لتفاصيل السند
- [ ] تكوين حالات السند (Status)
- [ ] إضافة حساب المجموع التلقائي

### 3.5 إنشاء Resource لفواتير المشتريات
- [ ] إنشاء `app/Filament/Resources/PurchaseInvoiceResource.php`
- [ ] ربط الفاتورة بسند الاستلام
- [ ] حساب الضريبة والمجموع
- [ ] إضافة حالات الدفع

### 3.6 إنشاء Resource لسندات التسليم
- [ ] إنشاء `app/Filament/Resources/DeliveryNoteResource.php`
- [ ] تكوين النموذج مع العلاقات (Customer)
- [ ] إضافة Repeater لتفاصيل السند
- [ ] التحقق من توفر المخزون
- [ ] تكوين حالات التسليم

### 3.7 إنشاء Resource لفواتير المبيعات
- [ ] إنشاء `app/Filament/Resources/SalesInvoiceResource.php`
- [ ] ربط الفاتورة بسند التسليم
- [ ] حساب الضريبة والمجموع
- [ ] إضافة حالات الدفع

---

## المرحلة الرابعة: إضافات متقدمة

### 4.1 إنشاء تقارير المخزون
- [ ] إنشاء Resource لتقرير أرصدة المخزون
- [ ] حساب الرصيد الحالي لكل منتج
- [ ] إضافة فلاتر بالتاريخ والمنتج

### 4.2 إنشاء Dashboard
- [ ] إضافة ويدجت لإجمالي المنتجات
- [ ] إضافة ويدجت لسندات الاستلام الحديثة
- [ ] إضافة ويدجت لسندات التسليم الحديثة
- [ ] إضافة رسوم بيانية للمبيعات والمشتريات

### 4.3 إعداد الصلاحيات
- [ ] تكوين أدوار المستخدمين
- [ ] تحديد صلاحيات كل دور
- [ ] حماية الموارد حسب الصلاحيات

### 4.4 إضافة التحقق والقيود
- [ ] التحقق من عدم تكرار أرقام السندات
- [ ] التحقق من توفر المخزون عند التسليم
- [ ] إضافة قيود على تعديل السندات المؤكدة

---

## المرحلة الخامسة: الاختبار والتحسين

### 5.1 إنشاء بيانات تجريبية
- [ ] إنشاء Seeders للبيانات الأساسية
- [ ] إضافة بيانات تجريبية للاختبار

### 5.2 اختبار النظام
- [ ] اختبار إضافة المنتجات والموردين والعملاء
- [ ] اختبار دورة الاستلام الكاملة
- [ ] اختبار دورة التسليم الكاملة
- [ ] اختبار التقارير والحسابات

### 5.3 التحسينات النهائية
- [ ] تحسين واجهة المستخدم
- [ ] إضافة رسائل التأكيد والتحذير
- [ ] تحسين الأداء والاستعلامات
- [ ] إضافة التوثيق النهائي

---

## ملاحظات مهمة:

1. **ترتيب التنفيذ**: يجب تنفيذ المراحل بالترتيب المذكور
2. **العلاقات**: التأكد من صحة العلاقات بين الجداول
3. **التحقق**: إضافة التحقق من صحة البيانات في كل مرحلة
4. **الاختبار**: اختبار كل مرحلة قبل الانتقال للتالية
5. **النسخ الاحتياطية**: عمل نسخة احتياطية قبل كل تغيير كبير

---

**تاريخ الإنشاء**: $(date)
**آخر تحديث**: $(date)
**الحالة**: قيد التطوير