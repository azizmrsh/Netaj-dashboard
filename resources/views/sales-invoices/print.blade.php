<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>فاتورة مبيعات - {{ $salesInvoice->customer_name ?? 'غير محدد' }} - {{ \Carbon\Carbon::parse($salesInvoice->invoice_date)->format('Y-m-d') }}</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Cairo', 'Segoe UI', Arial, sans-serif;
    font-size: 11px;
    color: #000;
    background: #fff;
    direction: rtl;
    line-height: 1.3;
}
.container {
    width: 210mm;
    min-height: 297mm;
    padding: 10mm;
    margin: 0 auto;
    position: relative;
    background: #fff;
}
@media print {
    @page { size: A4; margin: 0; }
    body { margin: 0; background: #fff; }
    .container { padding: 10mm; }
    .no-print { display: none !important; }
}
@media screen {
    body { background: #f0f0f0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
    .container { box-shadow: 0 0 15px rgba(0,0,0,0.1); }
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 3px solid #d4af37;
}

.header-left {
    text-align: right;
    font-size: 11px;
    line-height: 1.4;
    width: 32%;
    direction: rtl;
}

.header-center {
    width: 36%;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
}

.logo-container {
    width: 180px;
    height: 110px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 0;
    background-color: transparent;
    overflow: visible;
    padding: 0;
}

.header-center h2 {
    font-size: 14px;
    margin: 3px 0;
    font-weight: 600;
    line-height: 1.2;
    font-family: 'Segoe UI', 'Cairo', Arial, sans-serif;
}

.header-right {
    text-align: left;
    font-size: 11px;
    line-height: 1.4;
    width: 32%;
    direction: ltr;
}

.header-content {
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: space-between;
}

.company-title {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 5px;
    color: #2c3e50;
}

.company-details {
    font-size: 11px;
    line-height: 1.4;
}

.header-left, .header-right {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
    font-size: 10px;
}
th, td {
    border: 1px solid #d4af37;
    padding: 5px 3px;
    text-align: center;
    vertical-align: middle;
    font-weight: normal;
}
th {
    background-color: #fff;
    font-weight: 600;
    font-size: 9px;
}
.bilingual {
    display: flex;
    flex-direction: column;
    gap: 1px;
}
.bilingual .ar { direction: rtl; font-weight: 600; font-size: 9px; }
.bilingual .en { direction: ltr; font-weight: 500; font-size: 8px; color: #333; }

/* Buyer Info Table - 6 columns in row 1 */
.client-table-row1 th:nth-child(1) { width: 15%; }
.client-table-row1 th:nth-child(2) { width: 15%; }
.client-table-row1 th:nth-child(3) { width: 40%; }
.client-table-row1 th:nth-child(4) { width: 15%; }

/* 7 columns in row 2 */
.client-table th:nth-child(1) { width: 12%; }
.client-table th:nth-child(2) { width: 12%; }
.client-table th:nth-child(3) { width: 14%; }
.client-table th:nth-child(4) { width: 14%; }
.client-table th:nth-child(5) { width: 12%; }
.client-table th:nth-child(6) { width: 20%; }
.client-table th:nth-child(7) { width: 12%; }

/* Invoice Details - 7 columns */
.invoice-details-table th { width: 14.28%; }

/* Items Table */
.items-table th:nth-child(1) { width: 6%; }
.items-table th:nth-child(2) { width: 28%; }
.items-table th:nth-child(3) { width: 8%; }
.items-table th:nth-child(4) { width: 10%; }
.items-table th:nth-child(5) { width: 12%; }
.items-table th:nth-child(6) { width: 8%; }
.items-table th:nth-child(7) { width: 10%; }
.items-table th:nth-child(8) { width: 18%; }

.items-table td:nth-child(2) {
    text-align: right;
    padding-right: 5px;
}

/* Totals Table */
.totals-table {
    width: 100%;
}
.totals-table td {
    padding: 6px 8px;
}
.totals-table .label {
    background: #fff;
    text-align: right;
    font-weight: 600;
    font-size: 10px;
}
.totals-table .value {
    text-align: center;
    font-weight: 600;
}
.totals-table .total {
    background: #d4af37 !important;
    color: #000;
    font-weight: 700;
    font-size: 11px;
}

/* Signature */
.signature-table {
    margin-top: 15px;
    width: 100%;
}
.signature-table td {
    height: 50px;
    vertical-align: bottom;
    text-align: center;
    font-weight: 600;
    border: 1px solid #d4af37;
}

.no-print {
    position: fixed;
    top: 20px;
    left: 20px;
    padding: 10px 20px;
    background: #d4af37;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    z-index: 1000;
}
.no-print:hover {
    background: #b8942a;
}
</style>
</head>
<body>
<button class="no-print" onclick="window.print()">طباعة PDF</button>

<div class="container">
<!-- Header -->
<div class="header">
<div class="header-left">
<div class="header-content">
<div class="company-title">شركة نتاج المتطورة التجارية</div>
<div class="company-details">
رقم المبنى - الشارع: ٣٠٣٠ - شارع قيصر الكاتب<br>
الحي - المدينة: ٦٢٠٨ مداين الفهد - جدة<br>
الولاية - البلد: مكة المكرمة - المملكة العربية السعودية<br>
الرمز البريدي: ٢٢٣٤٧<br>
البريد الإلكتروني: info@advanced-netaj.com<br>
رقم السجل التجاري: ٤٠٣٠٥٧٩٠٩٠<br>
رقم ضريبة القيمة المضافة: ٣١٢٥٤٤٥٢٩٠٠٠٠٣
</div>
</div>
</div>

<div class="header-center">
<div class="logo-container">
<img src="{{ asset('images/logo.svg') }}" alt="Company Logo" style="width: 100%; height: 100%; object-fit: contain;">
</div>
<h2>Netaj Almotatwrah Commercial Company</h2>
<h2>Sales Invoice - Tax Invoice</h2>
<h2>فاتورة مبيعات - فاتورة ضريبية</h2>
</div>

<div class="header-right">
<div class="header-content">
<div class="company-title">Netaj Almotatwrah Commercial Company</div>
<div class="company-details">
Building No. - Street: 3030 - Kaiser Al Kateb Street<br>
District - City: 6208 Madain Al Fahd - Jeddah<br>
State - Country: Makka - Kingdom of Saudi Arabia<br>
Postal code: 22347<br>
E-mail: info@advanced-netaj.com<br>
Commercial Registration Number: 4030579090<br>
VAT Number: 3125445290003
</div>
</div>
</div>
</div>

<!-- Buyer Info Table -->
<table class="client-table-row1">
<tr>
<th><div class="bilingual"><div class="ar">رقم ضريبة القيمة المضافة</div><div class="en">VAT Number</div></div></th>
<th><div class="bilingual"><div class="ar">رقم السجل التجاري</div><div class="en">Commercial Registration No</div></div></th>
<th><div class="bilingual"><div class="ar">اسم المشتري</div><div class="en">Buyer Name</div></div></th>
<th><div class="bilingual"><div class="ar">رمز العميل</div><div class="en">Customer Code</div></div></th>
</tr>
<tr>
<td>{{ $salesInvoice->customer_tax_number ?? '' }}</td>
<td></td>
<td>{{ $salesInvoice->customer_name ?? '' }}</td>
<td>{{ $salesInvoice->deliveryDocument->customer->id ?? '' }}</td>
</tr>
</table>

<table class="client-table">
<tr>
<th><div class="bilingual"><div class="ar">الرمز البريدي</div><div class="en">Postal Code</div></div></th>
<th><div class="bilingual"><div class="ar">الدولة</div><div class="en">Country</div></div></th>
<th><div class="bilingual"><div class="ar">المدينة</div><div class="en">City</div></div></th>
<th><div class="bilingual"><div class="ar">الحي</div><div class="en">District</div></div></th>
<th><div class="bilingual"><div class="ar">الرقم الفرعي</div><div class="en">Secondary No</div></div></th>
<th><div class="bilingual"><div class="ar">الشارع</div><div class="en">Street</div></div></th>
<th><div class="bilingual"><div class="ar">رقم المبنى</div><div class="en">Building No</div></div></th>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td>{{ $salesInvoice->customer_address ?? '' }}</td>
<td></td>
</tr>
</table>

<!-- Invoice Details -->
<table class="invoice-details-table">
<tr>
<th><div class="bilingual"><div class="ar">طريقة/شروط الدفع</div><div class="en">Mode/Terms of Payment</div></div></th>
<th><div class="bilingual"><div class="ar">مكان التوريد</div><div class="en">Place of Supply</div></div></th>
<th><div class="bilingual"><div class="ar">تاريخ التسليم</div><div class="en">Delivery Date</div></div></th>
<th><div class="bilingual"><div class="ar">رقم التسليم</div><div class="en">Delivery No</div></div></th>
<th><div class="bilingual"><div class="ar">رقم أمر الشراء</div><div class="en">Buyers Order No.</div></div></th>
<th><div class="bilingual"><div class="ar">رقم الفاتورة</div><div class="en">Invoice No</div></div></th>
<th><div class="bilingual"><div class="ar">التاريخ والوقت</div><div class="en">Date and Time</div></div></th>
</tr>
<tr>
<td>{{ $salesInvoice->payment_method ?? '' }}</td>
<td></td>
<td>{{ $salesInvoice->deliveryDocument->date_and_time ? \Carbon\Carbon::parse($salesInvoice->deliveryDocument->date_and_time)->format('Y/m/d') : '' }}</td>
<td>{{ $salesInvoice->deliveryDocument->document_number ?? 'DEL-' . $salesInvoice->delivery_document_id }}</td>
<td>{{ $salesInvoice->deliveryDocument->purchase_order_no ?? '' }}</td>
<td>{{ $salesInvoice->invoice_no ?? 'INV-' . $salesInvoice->id }}</td>
<td>{{ \Carbon\Carbon::parse($salesInvoice->invoice_date)->format('Y/m/d H:i') }}</td>
</tr>
</table>

<!-- Items Table -->
<table class="items-table">
<thead>
<tr>
<th><div class="bilingual"><div class="ar">الرقم التسلسلي</div><div class="en">Serial No</div></div></th>
<th><div class="bilingual"><div class="ar">وصف البضائع</div><div class="en">Description of Goods</div></div></th>
<th><div class="bilingual"><div class="ar">الكمية</div><div class="en">Quantity</div></div></th>
<th><div class="bilingual"><div class="ar">السعر الوحدة</div><div class="en">Unit price</div></div></th>
<th><div class="bilingual"><div class="ar">إجمالي المبلغ غير شامل الضريبة</div><div class="en">Subtotal Exclusive of VAT</div></div></th>
<th><div class="bilingual"><div class="ar">نسبة الضريبة</div><div class="en">VAT Rate</div></div></th>
<th><div class="bilingual"><div class="ar">مبلغ الضريبة</div><div class="en">VAT Amount</div></div></th>
<th><div class="bilingual"><div class="ar">إجمالي المبلغ شامل الضريبة</div><div class="en">Subtotal Inclusive of VAT</div></div></th>
</tr>
</thead>
<tbody>
@php
    $subtotal = 0;
    $totalTax = 0;
@endphp
@foreach($salesInvoice->deliveryDocumentProducts as $index => $item)
    @php
        $itemTotal = $item->quantity * ($item->unit_price ?? 0);
        $itemTax = $itemTotal * (($item->tax_rate ?? 15) / 100);
        $itemTotalWithTax = $itemTotal + $itemTax;
        $subtotal += $itemTotal;
        $totalTax += $itemTax;
    @endphp
    <tr>
        <td>{{ $index + 1 }}</td>
        <td style="text-align: right; padding-right: 5px;">{{ $item->product->name ?? '' }}</td>
        <td>{{ number_format($item->quantity, 3) }}</td>
        <td>{{ number_format($item->unit_price ?? 0, 2) }}</td>
        <td>{{ number_format($itemTotal, 2) }}</td>
        <td>{{ $item->tax_rate ?? 15 }}%</td>
        <td>{{ number_format($itemTax, 2) }}</td>
        <td>{{ number_format($itemTotalWithTax, 2) }}</td>
    </tr>
@endforeach
@for($i = count($salesInvoice->deliveryDocumentProducts); $i < 8; $i++)
    <tr>
        <td>{{ $i + 1 }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
@endfor
</tbody>
</table>

<!-- Totals -->
<table class="totals-table">
<tr>
<td class="label"><div class="bilingual"><div class="ar">إجمالي المبلغ (غير شامل الضريبة)</div><div class="en">Total (Exclusive VAT)</div></div></td>
<td class="value" colspan="2">{{ number_format($salesInvoice->subtotal ?? $subtotal, 2) }}</td>
</tr>
<tr>
<td class="label"><div class="bilingual"><div class="ar">إجمالي ضريبة القيمة المضافة</div><div class="en">VAT Total Amount</div></div></td>
<td class="value" colspan="2">{{ number_format($salesInvoice->tax_amount ?? $totalTax, 2) }}</td>
</tr>
<tr>
<td class="label total"><div class="bilingual"><div class="ar">إجمالي المبلغ (شامل الضريبة)</div><div class="en">Invoice Gross (Inclusive VAT)</div></div></td>
<td class="value total" colspan="2">{{ number_format($salesInvoice->total_amount ?? ($subtotal + $totalTax - ($salesInvoice->discount_amount ?? 0)), 2) }}</td>
</tr>
</table>

<!-- Signature -->
<table class="signature-table">
<tr>
<td colspan="3" style="text-align: center;"><strong>Customer Seal and Signature</strong><br>ختم وتوقيع العميل</td>
<td colspan="3" style="text-align: center;"><strong>Netaj Almotatwrah Commercial Co.</strong><br>شركة نتاج المتطورة التجارية<br><strong>Authorised Signatory</strong> المفوض بالتوقيع</td>
</tr>
</table>
</div>
</body>
</html>
