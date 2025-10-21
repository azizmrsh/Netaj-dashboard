<?php

namespace App\Http\Controllers;

use App\Models\ReceiptDocument;
use Illuminate\Http\Request;

class ReceiptDocumentController extends Controller
{
    public function print(ReceiptDocument $receiptDocument)
    {
        // Load relationships for the receipt document
        $receiptDocument->load([
            'supplier',
            'transporter', 
            'receiptDocumentProducts.product'
        ]);

        return view('receipt-documents.print', compact('receiptDocument'));
    }
}
