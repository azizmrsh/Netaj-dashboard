<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
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
        'is_customer',
        'customer_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_customer' => 'boolean',
    ];

    /**
     * Get the customer that this supplier is linked to.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Check if this supplier is also a customer.
     */
    public function isCustomer(): bool
    {
        return $this->is_customer && $this->customer_id !== null;
    }

    /**
     * Copy data from a customer to this supplier.
     */
    public function copyFromCustomer(Customer $customer): void
    {
        $this->fill([
            'name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'name_company' => $customer->name_company,
            'country' => $customer->country,
            'address' => $customer->address,
            'tax_number' => $customer->tax_number,
            'zip_code' => $customer->zip_code,
            'national_number' => $customer->national_number,
            'commercial_registration_number' => $customer->commercial_registration_number,
            'is_customer' => true,
            'customer_id' => $customer->id,
        ]);
    }
}
