<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Customer extends Model
{
    use HasFactory;
    // Type constants for better code readability
    public const TYPE_CUSTOMER = 'customer';
    public const TYPE_SUPPLIER = 'supplier';
    public const TYPE_BOTH = 'both';

    protected $fillable = [
        'type',
        'name',
        'phone',
        'email',
        'note',
        'name_company',
        'country',
        'address',
        'tax_number',
        'zip_code',
        'is_active',
        'national_number',
        'commercial_registration_number',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only customers (type = 'customer')
     */
    public function scopeCustomers(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_CUSTOMER);
    }

    /**
     * Scope to get only suppliers (type = 'supplier')
     */
    public function scopeSuppliers(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_SUPPLIER);
    }

    /**
     * Scope to get records of type 'both'
     */
    public function scopeBoth(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_BOTH);
    }

    /**
     * Scope for receipt documents - parties that can be suppliers (type = 'supplier' or 'both')
     */
    public function scopeForReceipts(Builder $query): Builder
    {
        return $query->whereIn('type', [self::TYPE_SUPPLIER, self::TYPE_BOTH]);
    }

    /**
     * Scope for delivery documents - parties that can be customers (type = 'customer' or 'both')
     */
    public function scopeForDeliveries(Builder $query): Builder
    {
        return $query->whereIn('type', [self::TYPE_CUSTOMER, self::TYPE_BOTH]);
    }

    /**
     * Check if this record is a customer (type = 'customer' or 'both')
     */
    public function isCustomer(): bool
    {
        return in_array($this->type, [self::TYPE_CUSTOMER, self::TYPE_BOTH]);
    }

    /**
     * Check if this record is a supplier (type = 'supplier' or 'both')
     */
    public function isSupplier(): bool
    {
        return in_array($this->type, [self::TYPE_SUPPLIER, self::TYPE_BOTH]);
    }

    /**
     * Check if this record can handle both customer and supplier operations
     */
    public function isBoth(): bool
    {
        return $this->type === self::TYPE_BOTH;
    }

    /**
     * Get the display name for the type
     */
    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            self::TYPE_CUSTOMER => 'Customer',
            self::TYPE_SUPPLIER => 'Supplier',
            self::TYPE_BOTH => 'Customer & Supplier',
            default => 'Unknown'
        };
    }

    /**
     * Get the label for the type (alias for getTypeDisplayAttribute)
     */
    public function getTypeLabel(): string
    {
        return $this->getTypeDisplayAttribute();
    }

    /**
     * Get all available types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_CUSTOMER => 'Customer',
            self::TYPE_SUPPLIER => 'Supplier',
            self::TYPE_BOTH => 'Customer & Supplier',
        ];
    }

    /**
     * Get delivery documents for this customer (when acting as customer)
     */
    public function deliveryDocuments(): HasMany
    {
        return $this->hasMany(DeliveryDocument::class, 'id_customer');
    }

    /**
     * Get receipt documents for this customer (when acting as supplier)
     */
    public function receiptDocuments(): HasMany
    {
        return $this->hasMany(ReceiptDocument::class, 'id_customer');
    }

    /**
     * Get all delivery document products through delivery documents
     */
    public function deliveryDocumentProducts(): HasManyThrough
    {
        return $this->hasManyThrough(
            DeliveryDocumentProduct::class,
            DeliveryDocument::class,
            'id_customer', // Foreign key on delivery_documents table
            'delivery_document_id', // Foreign key on delivery_document_products table
            'id', // Local key on customers table
            'id' // Local key on delivery_documents table
        );
    }

    /**
     * Get all receipt document products through receipt documents
     */
    public function receiptDocumentProducts(): HasManyThrough
    {
        return $this->hasManyThrough(
            ReceiptDocumentProduct::class,
            ReceiptDocument::class,
            'id_customer', // Foreign key on receipt_documents table
            'receipt_document_id', // Foreign key on receipt_document_products table
            'id', // Local key on customers table
            'id' // Local key on receipt_documents table
        );
    }
}
