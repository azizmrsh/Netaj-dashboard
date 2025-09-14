<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryDocumentProduct extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'delivery_document_products';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'delivery_document_id',
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
     * Get the delivery document that owns the product.
     */
    public function deliveryDocument(): BelongsTo
    {
        return $this->belongsTo(DeliveryDocument::class, 'delivery_document_id');
    }

    /**
     * Get the product that owns the delivery document product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->setDefaultValues();
        });

        static::updating(function ($model) {
            $model->calculateValues();
        });
    }

    /**
     * Set default values for the model.
     */
    protected function setDefaultValues()
    {
        if (is_null($this->unit_price)) {
            $this->unit_price = 0;
        }
        if (is_null($this->tax_rate)) {
            $this->tax_rate = 15.00;
        }
        $this->calculateValues();
    }

    /**
     * Calculate all derived values.
     */
    protected function calculateValues()
    {
        $this->tax_amount = ($this->unit_price * $this->tax_rate) / 100;
        $this->unit_price_with_tax = $this->unit_price + $this->tax_amount;
        $this->subtotal = $this->unit_price * $this->quantity;
        $this->total_tax = $this->tax_amount * $this->quantity;
        $this->total_with_tax = $this->subtotal + $this->total_tax;
    }
}
