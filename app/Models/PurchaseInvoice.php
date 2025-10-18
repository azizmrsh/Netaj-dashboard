<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PurchaseInvoice extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'purchase_invoices';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'invoice_no',
        'date_and_time',
        'id_receipt_documents',
        'payment_terms',
        'place_of_supply',
        'buyers_order_no',
        'subtotal_amount',
        'total_tax_amount',
        'total_amount_with_tax',
        'note',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_and_time' => 'datetime',
        'subtotal_amount' => 'decimal:2',
        'total_tax_amount' => 'decimal:2',
        'total_amount_with_tax' => 'decimal:2',
    ];

    /**
     * Get the receipt document that this invoice is based on.
     */
    public function receiptDocument(): BelongsTo
    {
        return $this->belongsTo(ReceiptDocument::class, 'id_receipt_documents');
    }

    /**
     * Get the supplier through the receipt document.
     */
    public function supplier()
    {
        return $this->receiptDocument->supplier ?? null;
    }

    /**
     * Get the transporter through the receipt document.
     */
    public function transporter(): BelongsTo
    {
        return $this->receiptDocument()->getRelated()->transporter();
    }

    /**
     * Get the receipt document products through the receipt document.
     */
    public function receiptDocumentProducts(): HasManyThrough
    {
        return $this->hasManyThrough(
            ReceiptDocumentProduct::class,
            ReceiptDocument::class,
            'id',
            'id_receipt_documents',
            'id_receipt_documents',
            'id'
        );
    }

    /**
     * Get all products through receipt document products.
     */
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,
            ReceiptDocumentProduct::class,
            'id_receipt_documents', // Foreign key on receipt_document_products table
            'id', // Foreign key on products table
            'id_receipt_documents', // Local key on purchase_invoices table
            'id_product' // Local key on receipt_document_products table
        );
    }

    /**
     * Calculate and update invoice totals based on receipt document products.
     */
    public function calculateTotals(): void
    {
        $products = $this->receiptDocumentProducts;
        
        $this->subtotal_amount = $products->sum('subtotal') ?? 0;
        $this->total_tax_amount = $products->sum('total_tax') ?? 0;
        $this->total_amount_with_tax = $products->sum('total_with_tax') ?? 0;
        
        $this->save();
    }

    /**
     * Get the formatted invoice number.
     */
    public function getFormattedInvoiceNoAttribute(): string
    {
        return 'INV-' . str_pad($this->invoice_no, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_and_time', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by supplier.
     */
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->whereHas('receiptDocument', function ($q) use ($supplierId) {
            $q->where('id_customer', $supplierId);
        });
    }
}
