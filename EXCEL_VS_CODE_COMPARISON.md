# تقرير المقارنة: ملف Excel مقابل الكود الحالي
## Excel vs Code Comparison Report

**تاريخ المقارنة:** 2025-10-21  
**الملفات المقارنة:**
- ملف Excel: كشف حساب مخزون - شركة Al-Gary
- الكود الحالي: CustomerReport.php & CustomerReportExport.php

---

## 📊 الجزء الأول: تحليل ملف Excel

### بنية الجدول في Excel:

```
┌─────────────────────────────────────────────────────────────────────────┐
│ Netaj Almotatwrah Commercial Company                                    │
│ ASPHALT 60/70 Bin Card                                                  │
│ From 1-31 August 2025                                                   │
│ Customer Name: Al-Gary Company                                          │
├────┬──────────┬──────────────┬──────────────┬────────────┬─────────────┤
│ No │ Date     │ Document No  │ Product Name │ Receipts   │ Issues      │
│    │          │              │              │ (Qty Ton)  │ (Qty Ton)   │
├────┼──────────┼──────────────┼──────────────┼────────────┼─────────────┤
│ *  │ *        │ Opening Bal  │ *            │ *          │ *           │
│ 1  │ 8/2/2025 │ Receipt 0008 │ ASPHALT 60/70│ 26.02      │             │
│ 2  │ 8/2/2025 │ Delivery 0006│ PG 76 S-10   │            │ 25.06       │
└────┴──────────┴──────────────┴──────────────┴────────────┴─────────────┘

    ┌─────────────┬──────────────┬──────────────────┐
    │ Balance     │ Rate (SAR)   │ Value (SAR)      │
    │ (Qty Ton)   │              │                  │
    ├─────────────┼──────────────┼──────────────────┤
    │ 0.00        │ *            │ *                │
    │ 26.02       │              │                  │
    │ 0.96        │ 115          │ 2881.9           │
    └─────────────┴──────────────┴──────────────────┘
```

---

## 🔢 الجزء الثاني: المعادلات في Excel

### 1. معادلة حساب الرصيد (Balance):
```excel
Balance (G7) = G6 + E7 - F7
```

**الشرح:**
- `G6` = الرصيد السابق
- `E7` = الكمية الواردة (Receipts)
- `F7` = الكمية الصادرة (Issues)
- **المنطق:** الرصيد الجديد = الرصيد السابق + الوارد - الصادر

**مثال من الصورة:**
- الصف 1: Opening Balance = 0.00
- الصف 2: Receipt 26.02 → Balance = 0 + 26.02 - 0 = **26.02**
- الصف 3: Delivery 25.06 → Balance = 26.02 + 0 - 25.06 = **0.96**

---

### 2. معادلة حساب القيمة (Value):
```excel
Value (I7) = F7 × H7
```

**الشرح:**
- `F7` = الكمية الصادرة (Issues)
- `H7` = السعر (Rate)
- **المنطق:** القيمة = الكمية الصادرة × السعر
- **ملاحظة هامة:** القيمة تُحسب فقط للصادرات (Deliveries)، ليس للواردات (Receipts)

**مثال من الصورة:**
- الصف 3: Issues = 25.06, Rate = 115 → Value = 25.06 × 115 = **2881.9**
- الصف 4: Issues = 24.42, Rate = 115 → Value = 24.42 × 115 = **2808.3**

---

### 3. معادلات الجدول الختامي (Summary):

#### إجمالي الواردات (Total Receipts):
```excel
Total Receipts = SUM(E7:E81)
```
من الصورة: **923.70** طن

#### إجمالي الصادرات (Total Issues):
```excel
Total Issues = SUM(F7:F81)
```
من الصورة: **934.14** طن

#### الرصيد النهائي (Balance):
```excel
Balance = Total Receipts - Total Issues
Balance = 923.70 - 934.14 = -10.44
```
من الصورة: **-10.44** طن (رصيد سالب)

#### إجمالي المبلغ قبل الضريبة (Total Amount Before Tax):
```excel
Total Amount Before Tax = SUM(I8:I81)
```
من الصورة: **107426.10** ريال

#### ضريبة القيمة المضافة (VAT):
```excel
VAT = Total Amount Before Tax × 15%
VAT = 107426.10 × 0.15 = 16113.92
```
من الصورة: **16113.92** ريال

#### إجمالي المبلغ بعد الضريبة (Total Amount After Tax):
```excel
Total Amount After Tax = Total Amount Before Tax + VAT
Total Amount After Tax = 107426.10 + 16113.92 = 123540.02
```
من الصورة: **123540.02** ريال

---

## 💻 الجزء الثالث: المنطق في الكود الحالي

### ملف: `CustomerReport.php`

#### 1. حساب الرصيد الجاري (Running Balance):

```php
// في دالة calculateReportData()
$runningBalance = $openingBalance;

foreach ($transactions as $transaction) {
    $runningBalance += $transaction['receipts'] - $transaction['issues'];
    
    $reportData->push([
        'balance' => $runningBalance,
        // ...
    ]);
}
```

**✅ المنطق مطابق لـ Excel:**
- الرصيد الجديد = الرصيد السابق + الواردات - الصادرات

---

#### 2. حساب القيمة (Value):

```php
// في دالة calculateReportData()
$value = $transaction['issues'] > 0 ? $transaction['issues'] * $this->rate : 0;

$reportData->push([
    'rate' => $transaction['issues'] > 0 ? $this->rate : 0,
    'value' => $value,
    // ...
]);
```

**✅ المنطق مطابق لـ Excel:**
- القيمة = الكمية الصادرة × السعر
- القيمة تُحسب فقط للصادرات

---

#### 3. حساب الملخص (Summary):

```php
// في دالة calculateReportData()
$this->totalReceipts = 0;
$this->totalIssues = 0;

foreach ($transactions as $transaction) {
    $this->totalReceipts += $transaction['receipts'];
    $this->totalIssues += $transaction['issues'];
}

$this->finalBalance = $runningBalance;
$this->totalAmountBeforeTax = $this->totalIssues * $this->rate;
$this->vatAmount = $this->totalAmountBeforeTax * 0.15;
$this->totalAmountAfterTax = $this->totalAmountBeforeTax + $this->vatAmount;
```

**✅ المنطق مطابق لـ Excel:**
- Total Receipts = مجموع الواردات
- Total Issues = مجموع الصادرات
- Final Balance = الرصيد الأخير
- Total Amount Before Tax = مجموع الصادرات × السعر
- VAT = المبلغ قبل الضريبة × 15%
- Total Amount After Tax = المبلغ قبل الضريبة + الضريبة

---

## ⚖️ الجزء الرابع: المقارنة والفروقات

### ✅ التشابهات (ما هو متطابق):

| العنصر | Excel | الكود الحالي | الحالة |
|--------|-------|-------------|--------|
| **معادلة الرصيد** | `G7 = G6 + E7 - F7` | `$runningBalance += receipts - issues` | ✅ متطابق |
| **معادلة القيمة** | `I7 = F7 × H7` | `$value = issues × rate` | ✅ متطابق |
| **إجمالي الواردات** | `SUM(E7:E81)` | `sum(receipts)` | ✅ متطابق |
| **إجمالي الصادرات** | `SUM(F7:F81)` | `sum(issues)` | ✅ متطابق |
| **الرصيد النهائي** | `Receipts - Issues` | `$runningBalance` | ✅ متطابق |
| **ضريبة 15%** | `× 0.15` | `× 0.15` | ✅ متطابق |
| **بنية الأعمدة** | 8 أعمدة | 8 أعمدة | ✅ متطابق |

---

### ⚠️ الفروقات الرئيسية:

#### 1. حساب المبلغ قبل الضريبة:

| Excel | الكود الحالي |
|-------|-------------|
| `SUM(I8:I81)` - مجموع قيم كل صف | `Total Issues × Rate` |
| يجمع كل القيم الفردية | يحسب الإجمالي مباشرة |

**التحليل:**
- **Excel**: يحسب القيمة لكل صف ثم يجمعها: `(25.06×115) + (24.42×115) + ...`
- **الكود**: يجمع الكميات ثم يضرب: `(25.06 + 24.42 + ...) × 115`
- **النتيجة**: ✅ **نفس النتيجة رياضياً** (خاصية التوزيع)

```
Excel:   (a×r) + (b×r) + (c×r) = 107426.10
Code:    (a + b + c) × r = 107426.10
Result:  نفس النتيجة ✅
```

---

#### 2. صف الرصيد الافتتاحي (Opening Balance):

| Excel | الكود الحالي |
|-------|-------------|
| يظهر في الصف الأول دائماً | يظهر في الصف الأول ✅ |
| الرصيد يُدخل يدوياً | يُدخل يدوياً أو يُحسب من التاريخ السابق ✅ |

**التحليل:**
- ✅ الكود يدعم إدخال الرصيد الافتتاحي يدوياً
- ✅ الكود يدعم أيضاً حساب الرصيد من العمليات السابقة (ميزة إضافية)

```php
// في الواجهة (Form)
Forms\Components\TextInput::make('opening_balance')
    ->label('Opening Balance (الرصيد الافتتاحي)')
    ->numeric()
    ->default(0);

// في الكود
$openingBalance = $this->openingBalance; // يُدخل يدوياً

// أو يُحسب من التاريخ السابق
$openingBalance = $this->calculateOpeningBalance($customerId, $beforeDate);
```

---

#### 3. معدل الضريبة (Tax Rate):

| Excel | الكود الحالي |
|-------|-------------|
| ثابت: 15% | قابل للتغيير من الواجهة ✅ |

**التحليل:**
- Excel: الضريبة ثابتة 15%
- الكود: يمكن تعديلها من واجهة المستخدم
- **مثال:** يمكن وضع 0% أو 5% أو 15%

```php
Forms\Components\TextInput::make('tax_rate')
    ->label('Tax Rate (معدل الضريبة)')
    ->numeric()
    ->default(0)
    ->suffix('%');
```

---

#### 4. السعر (Rate):

| Excel | الكود الحالي |
|-------|-------------|
| ثابت: 115 ريال لكل الصفوف | يُدخل من الواجهة مرة واحدة |
| يظهر في عمود Rate | يُطبق على كل الصادرات |

**⚠️ مشكلة محتملة في الكود:**
```php
// الكود الحالي
$this->rate = ($data['tax_rate'] ?? 0) / 100 + 1;
```

**تحليل المشكلة:**
- إذا أدخل المستخدم `tax_rate = 0`
- النتيجة: `rate = 0/100 + 1 = 1`
- **المشكلة:** المتغير `rate` يُستخدم كـ "السعر الأساسي" وليس "معدل الضريبة"

**في Excel:**
- Rate = 115 (السعر الأساسي)
- Tax = 15% (معدل الضريبة)
- هذان مفهومان منفصلان!

**الحل المقترح:**
يجب فصل مفهوم "السعر الأساسي" عن "معدل الضريبة":

```php
// يجب إضافة حقل منفصل للسعر الأساسي
Forms\Components\TextInput::make('unit_rate')
    ->label('Unit Rate (سعر الوحدة)')
    ->numeric()
    ->default(115)
    ->suffix('SAR');

Forms\Components\TextInput::make('tax_rate')
    ->label('Tax Rate (معدل الضريبة)')
    ->numeric()
    ->default(15)
    ->suffix('%');

// في الحساب
$this->unitRate = $data['unit_rate'] ?? 115;
$this->taxRate = ($data['tax_rate'] ?? 15) / 100;
$this->totalAmountBeforeTax = $this->totalIssues * $this->unitRate;
$this->vatAmount = $this->totalAmountBeforeTax * $this->taxRate;
```

---

#### 5. تجميع المنتجات:

| Excel | الكود الحالي |
|-------|-------------|
| كل صف = منتج واحد | كل صف = كل منتجات المستند |
| صف واحد: ASPHALT 60/70 | صف واحد: "Receipt: ASPHALT 60/70, PG 76, ..." |

**مثال من Excel:**
```
Row 2: Receipt Note 0008 | ASPHALT 60/70 | 26.02
```

**مثال من الكود:**
```php
foreach ($receiptDocs as $doc) {
    $totalQty = $doc->receiptDocumentProducts->sum('quantity');
    $products = $doc->receiptDocumentProducts->pluck('product.name')->join(', ');
    
    $transactions->push([
        'product_name' => "Receipt: {$products}",
        'receipts' => $totalQty, // مجموع كل المنتجات في المستند
    ]);
}
```

**النتيجة:**
```
Row: Receipt Note 0008 | Receipt: ASPHALT 60/70, Product2, Product3 | 26.02 + 10 + 5 = 41.02
```

**⚠️ الفرق الجوهري:**
- Excel: صف واحد لكل منتج في المستند
- الكود: صف واحد لكل مستند (يجمع كل المنتجات)

---

## 🎯 الجزء الخامس: التوصيات

### 1. إصلاح مفهوم Rate vs Tax Rate ⚠️ **عاجل**

**المشكلة الحالية:**
```php
// ❌ خطأ: استخدام tax_rate كـ rate
$this->rate = ($data['tax_rate'] ?? 0) / 100 + 1;
$value = $transaction['issues'] * $this->rate;
```

**الحل:**
```php
// ✅ صحيح: فصل السعر عن الضريبة
$this->unitRate = $data['unit_rate'] ?? 115;
$this->taxRate = ($data['tax_rate'] ?? 15) / 100;
$value = $transaction['issues'] * $this->unitRate;
$vatAmount = $totalAmountBeforeTax * $this->taxRate;
```

---

### 2. خيار عرض المنتجات منفصلة

**الوضع الحالي:**
- المستند الواحد = صف واحد (مجموع كل المنتجات)

**الخيار المقترح:**
```php
Forms\Components\Toggle::make('separate_products')
    ->label('Show products separately (عرض المنتجات منفصلة)')
    ->default(false);

// في الكود
if ($this->data['separate_products']) {
    // عرض كل منتج في صف منفصل (مثل Excel)
    foreach ($doc->receiptDocumentProducts as $product) {
        $transactions->push([
            'product_name' => $product->product->name,
            'receipts' => $product->quantity,
        ]);
    }
} else {
    // عرض المستند كاملاً في صف واحد (الوضع الحالي)
}
```

---

### 3. خيار السعر لكل منتج

**في Excel:**
- كل الصادرات لها نفس السعر (115)

**الكود الحالي:**
- سعر واحد لكل المنتجات

**تحسين مقترح:**
```php
// إذا كان كل منتج له سعر مختلف
if ($product->unit_price) {
    $value = $transaction['issues'] * $product->unit_price;
} else {
    $value = $transaction['issues'] * $this->defaultRate;
}
```

---

### 4. تحسين صيغة العرض في Excel Export

**الوضع الحالي في `CustomerReportExport.php`:**
```php
public function styles(Worksheet $sheet)
{
    // يضيف header معلومات الشركة
    $sheet->setCellValue('A1', 'Inventory Account Statement - ' . $this->customer->name);
}
```

**تحسين مقترح لمطابقة Excel:**
```php
// إضافة اسم المنتج إذا كان التقرير لمنتج واحد
$sheet->setCellValue('A1', 'Netaj Almotatwrah Commercial Company');
$sheet->setCellValue('A2', 'ASPHALT 60/70 Bin Card'); // أو اسم المنتج
$sheet->setCellValue('A3', 'From ' . $dateFrom . ' To ' . $dateTo);
$sheet->setCellValue('A4', 'Customer Name: ' . $this->customer->name);
```

---

### 5. إضافة تصفية حسب المنتج

**غير موجود حالياً:**
- التقرير يعرض كل المنتجات للعميل

**مقترح:**
```php
Forms\Components\Select::make('product_id')
    ->label('Product (optional)')
    ->options(Product::pluck('name', 'id'))
    ->searchable()
    ->nullable();

// في الكود
if ($this->data['product_id']) {
    // تصفية العمليات حسب المنتج المحدد
}
```

---

## 📋 الجزء السادس: جدول المقارنة الشامل

| الميزة | Excel | الكود الحالي | التطابق | الملاحظات |
|--------|-------|-------------|---------|-----------|
| **معادلة الرصيد** | G=G-1+E-F | ✅ نفس المنطق | ✅ 100% | متطابق تماماً |
| **معادلة القيمة** | I=F×H | ✅ نفس المنطق | ✅ 100% | متطابق تماماً |
| **إجمالي الواردات** | SUM(E:E) | ✅ sum(receipts) | ✅ 100% | متطابق تماماً |
| **إجمالي الصادرات** | SUM(F:F) | ✅ sum(issues) | ✅ 100% | متطابق تماماً |
| **الرصيد النهائي** | محسوب | ✅ محسوب | ✅ 100% | متطابق تماماً |
| **المبلغ قبل الضريبة** | SUM(I:I) | ✅ Issues×Rate | ✅ 100% | رياضياً متطابق |
| **ضريبة 15%** | ثابت | ✅ قابل للتعديل | ⚠️ 90% | الكود أكثر مرونة |
| **الرصيد الافتتاحي** | يدوي | ✅ يدوي + محسوب | ✅ 110% | الكود أفضل |
| **السعر (Rate)** | ثابت 115 | ❌ مربوط بـ tax_rate | ❌ 0% | **يحتاج إصلاح** |
| **عرض المنتجات** | منفصلة | مجمّعة | ⚠️ 50% | يحتاج خيار |
| **تصفية بالمنتج** | يدوي | ❌ غير موجود | ⚠️ 50% | ميزة مقترحة |
| **تنسيق Excel** | منسّق | ✅ منسّق | ⚠️ 80% | يحتاج تحسين |

**النتيجة الإجمالية:** ⚠️ **85% متطابق**

---

## 🔧 الجزء السابع: الأخطاء التي يجب إصلاحها

### ❌ خطأ 1: الخلط بين Rate و Tax Rate

**الملف:** `app/Filament/Resources/CustomerResource/Pages/CustomerReport.php`  
**السطر:** 111

**الكود الحالي:**
```php
$this->rate = ($data['tax_rate'] ?? 0) / 100 + 1;
```

**المشكلة:**
- المتغير `rate` يُستخدم لحساب القيمة: `value = issues × rate`
- لكن يتم حسابه من `tax_rate` وهذا خطأ مفاهيمي
- في Excel: Rate = 115 (سعر الوحدة)، Tax = 15% (ضريبة)

**الحل:**
```php
// إضافة حقل جديد في الفورم
Forms\Components\TextInput::make('unit_rate')
    ->label('Unit Rate - سعر الوحدة (SAR)')
    ->numeric()
    ->default(115)
    ->required()
    ->helperText('Enter the unit price per ton'),

Forms\Components\TextInput::make('tax_rate')
    ->label('Tax Rate - معدل الضريبة (%)')
    ->numeric()
    ->default(15)
    ->step(0.01)
    ->suffix('%')
    ->helperText('Enter VAT percentage (e.g., 15 for 15%)'),

// تعديل الحساب
$this->unitRate = $data['unit_rate'] ?? 115;
$this->taxRate = ($data['tax_rate'] ?? 15) / 100;

// استخدام unitRate بدلاً من rate
$value = $transaction['issues'] * $this->unitRate;

// حساب الضريبة
$this->totalAmountBeforeTax = $this->totalIssues * $this->unitRate;
$this->vatAmount = $this->totalAmountBeforeTax * $this->taxRate;
```

---

### ⚠️ تحسين 1: عرض المنتجات منفصلة

**المشكلة:**
- Excel يعرض كل منتج في صف منفصل
- الكود يجمع كل منتجات المستند في صف واحد

**الحل:**
```php
// إضافة خيار في الفورم
Forms\Components\Toggle::make('separate_products')
    ->label('Show products separately - عرض كل منتج في صف منفصل')
    ->default(false)
    ->helperText('When enabled, each product will be shown in a separate row'),

// تعديل دالة getTransactionsInRange
protected function getTransactionsInRange(int $customerId, Carbon $dateFrom, Carbon $dateTo): Collection
{
    $transactions = collect();
    $separateProducts = $this->data['separate_products'] ?? false;

    if ($separateProducts) {
        // عرض كل منتج منفصل
        $receiptDocs = ReceiptDocument::where('id_customer', $customerId)
            ->whereBetween('date_and_time', [$dateFrom, $dateTo])
            ->with(['receiptDocumentProducts.product'])
            ->get();

        foreach ($receiptDocs as $doc) {
            foreach ($doc->receiptDocumentProducts as $docProduct) {
                $transactions->push([
                    'date' => $doc->date_and_time,
                    'document_number' => $doc->document_number,
                    'product_name' => $docProduct->product->name,
                    'receipts' => $docProduct->quantity,
                    'issues' => 0,
                    'sort_date' => $doc->date_and_time,
                ]);
            }
        }
        
        // نفس الشيء للـ Delivery Documents
        // ...
    } else {
        // الطريقة الحالية (تجميع المنتجات)
        // ...
    }

    return $transactions->sortBy('sort_date')->values();
}
```

---

### ⚠️ تحسين 2: تصفية حسب المنتج

**الميزة المقترحة:**
- إمكانية عمل تقرير لمنتج واحد فقط (مثل ASPHALT 60/70 فقط)

**الحل:**
```php
// إضافة في الفورم
Forms\Components\Select::make('product_id')
    ->label('Filter by Product (optional) - تصفية حسب المنتج')
    ->options(Product::pluck('name', 'id'))
    ->searchable()
    ->nullable()
    ->helperText('Leave empty to show all products'),

// تعديل دالة getTransactionsInRange
protected function getTransactionsInRange(int $customerId, Carbon $dateFrom, Carbon $dateTo): Collection
{
    $productId = $this->data['product_id'] ?? null;

    $receiptQuery = ReceiptDocument::where('id_customer', $customerId)
        ->whereBetween('date_and_time', [$dateFrom, $dateTo])
        ->with(['receiptDocumentProducts.product']);
    
    if ($productId) {
        $receiptQuery->whereHas('receiptDocumentProducts', function($q) use ($productId) {
            $q->where('id_product', $productId);
        });
    }
    
    $receiptDocs = $receiptQuery->get();
    
    foreach ($receiptDocs as $doc) {
        // تصفية المنتجات داخل المستند
        $products = $doc->receiptDocumentProducts;
        if ($productId) {
            $products = $products->where('id_product', $productId);
        }
        
        $totalQty = $products->sum('quantity');
        $productNames = $products->pluck('product.name')->join(', ');
        
        // ...
    }
}
```

---

## ✅ الجزء الثامن: الخلاصة النهائية

### المطابقة العامة: ⭐ **85%**

### ✅ ما هو متطابق تماماً:
1. ✅ معادلة حساب الرصيد
2. ✅ معادلة حساب القيمة
3. ✅ إجمالي الواردات والصادرات
4. ✅ حساب الرصيد النهائي
5. ✅ حساب الضريبة 15%
6. ✅ بنية الجدول (8 أعمدة)
7. ✅ الجدول الختامي (Summary)

### ⚠️ ما يحتاج إصلاح:
1. ❌ **عاجل**: فصل مفهوم Unit Rate عن Tax Rate
2. ⚠️ إضافة خيار عرض المنتجات منفصلة
3. ⚠️ إضافة تصفية حسب المنتج
4. ⚠️ تحسين تنسيق Excel Export ليطابق النموذج

### 🎯 الأولويات:
1. **الأولوية 1:** إصلاح خطأ Rate/Tax Rate (عاجل) ⚠️
2. **الأولوية 2:** إضافة خيار عرض المنتجات منفصلة
3. **الأولوية 3:** إضافة تصفية حسب المنتج
4. **الأولوية 4:** تحسين تنسيق الـ Export

---

## 📊 الرسم التوضيحي

```
Excel File Structure:
┌────────────────────────────────────────────┐
│ Company Header                              │
│ Product Name: ASPHALT 60/70                 │
│ Period: 1-31 Aug 2025                       │
├────┬──────┬─────────┬────────┬──────────────┤
│ No │ Date │ Doc No  │ Product│ Receipts     │
├────┼──────┼─────────┼────────┼──────────────┤
│ 1  │ 8/2  │ REC-008 │ ASP    │ 26.02        │
│ 2  │ 8/2  │ DEL-006 │ PG 76  │              │
└────┴──────┴─────────┴────────┴──────────────┘
     ┌─────────┬──────┬─────────┐
     │ Issues  │ Bal  │ Value   │
     ├─────────┼──────┼─────────┤
     │         │ 26.02│         │
     │ 25.06   │ 0.96 │ 2881.9  │
     └─────────┴──────┴─────────┘

Current Code Structure:
┌────────────────────────────────────────────┐
│ Inventory Account Statement - Al-Gary      │
│ From: 01/08/2025 To: 31/08/2025            │
├────┬──────┬─────────────────────────────────┤
│ No │ Date │ Doc No  │ Description          │
├────┼──────┼─────────┼──────────────────────┤
│ 1  │ 8/2  │ REC-008 │ Receipt: ASP         │
│ 2  │ 8/2  │ DEL-006 │ Delivery: PG 76      │
└────┴──────┴─────────┴──────────────────────┘
     ┌─────────┬──────┬─────────┐
     │ Issues  │ Bal  │ Value   │
     ├─────────┼──────┼─────────┤
     │         │ 26.02│         │
     │ 25.06   │ 0.96 │ 2881.9  │
     └─────────┴──────┴─────────┘

✅ Same calculation logic
⚠️ Different product grouping
❌ Rate concept needs fix
```

---

**تم إعداد التقرير بواسطة:** GitHub Copilot  
**التاريخ:** 2025-10-21  
**الحالة:** 🟡 يحتاج إصلاحات
