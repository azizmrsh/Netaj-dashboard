<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class SalesInvoice extends Model
{
    use HasFactory;

    protected $table = 'sales_invoices';

    protected $fillable = [
        'invoice_no',
        'delivery_document_id',
        'invoice_date',
        'due_date',
        'customer_name',
        'customer_address',
        'customer_phone',
        'customer_tax_number',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'notes',
        'payment_method',
        'payment_date',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationship with delivery document
    public function deliveryDocument(): BelongsTo
    {
        return $this->belongsTo(DeliveryDocument::class);
    }

    // Relationship with invoice products through delivery document
    public function deliveryDocumentProducts(): HasManyThrough
    {
        // Access products through delivery document
        // HasManyThrough(related, through, firstKey, secondKey, localKey, secondLocalKey)
        return $this->hasManyThrough(
            DeliveryDocumentProduct::class, // Related model
            DeliveryDocument::class,        // Through model
            'id',                          // Foreign key on delivery_documents table
            'delivery_document_id',        // Foreign key on delivery_document_products table
            'delivery_document_id',        // Local key on sales_invoices table
            'id'                          // Local key on delivery_documents table
        );
    }

    /**
     * Calculate and update invoice totals based on delivery document products
     */
    public function calculateTotals(): void
    {
        $products = $this->deliveryDocumentProducts;
        
        $subtotal = $products->sum('subtotal');
        $totalTax = $products->sum('total_tax');
        $totalWithTax = $products->sum('total_with_tax');
        
        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $totalTax,
            'total_amount' => $totalWithTax - ($this->discount_amount ?? 0),
        ]);
    }

    // Calculate subtotal
    public function calculateSubtotal(): float
    {
        return $this->deliveryDocumentProducts()->sum('subtotal');
    }

    // Calculate tax amount
    public function calculateTaxAmount(): float
    {
        return $this->deliveryDocumentProducts()->sum('total_tax');
    }

    // Calculate total amount
    public function calculateTotalAmount(): float
    {
        return $this->deliveryDocumentProducts()->sum('total_with_tax') - ($this->discount_amount ?? 0);
    }

    // Query scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'sent']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereIn('status', ['draft', 'sent']);
    }
}
