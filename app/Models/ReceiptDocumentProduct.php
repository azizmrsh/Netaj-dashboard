<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptDocumentProduct extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'receipt_document_products';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'receipt_document_id',
        'product_id',
        'quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'unit_price_with_tax',
        'subtotal',
        'total_tax',
        'total_with_tax',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:4',
        'unit_price_with_tax' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_with_tax' => 'decimal:2',
    ];

    /**
     * Get the receipt document that owns the product.
     */
    public function receiptDocument(): BelongsTo
    {
        return $this->belongsTo(ReceiptDocument::class, 'receipt_document_id');
    }

    /**
     * Get the product that owns the receipt document product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
