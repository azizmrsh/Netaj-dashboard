<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function print(PurchaseInvoice $purchaseInvoice)
    {
        // Load relationships for the purchase invoice
        $purchaseInvoice->load([
            'receiptDocument.supplier',
            'receiptDocumentProducts.product'
        ]);

        return view('purchase-invoices.print', compact('purchaseInvoice'));
    }
}
