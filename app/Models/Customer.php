<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
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
        'is_supplier',
        'supplier_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_supplier' => 'boolean',
    ];

    /**
     * Get the supplier that this customer is linked to.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Check if this customer is also a supplier.
     */
    public function isSupplier(): bool
    {
        return $this->is_supplier && $this->supplier_id !== null;
    }

    /**
     * Copy data from a supplier to this customer.
     */
    public function copyFromSupplier(Supplier $supplier): void
    {
        $this->fill([
            'name' => $supplier->name,
            'phone' => $supplier->phone,
            'email' => $supplier->email,
            'name_company' => $supplier->name_company,
            'country' => $supplier->country,
            'address' => $supplier->address,
            'tax_number' => $supplier->tax_number,
            'zip_code' => $supplier->zip_code,
            'national_number' => $supplier->national_number,
            'commercial_registration_number' => $supplier->commercial_registration_number,
            'is_supplier' => true,
            'supplier_id' => $supplier->id,
        ]);
    }
}
