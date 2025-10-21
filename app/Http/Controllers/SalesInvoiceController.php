<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use Illuminate\Http\Request;

class SalesInvoiceController extends Controller
{
    public function print(SalesInvoice $salesInvoice)
    {
        // Load relationships for the sales invoice
        $salesInvoice->load([
            'deliveryDocument',
            'deliveryDocumentProducts.product'
        ]);

        return view('sales-invoices.print', compact('salesInvoice'));
    }
}
