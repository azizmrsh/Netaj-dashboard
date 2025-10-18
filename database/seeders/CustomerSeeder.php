<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 customers
        Customer::factory(50)->customer()->create();
        
        // Create 30 suppliers
        Customer::factory(30)->supplier()->create();
        
        // Create 15 both (customer and supplier)
        Customer::factory(15)->both()->create();
        
        // Create 5 inactive customers
        Customer::factory(5)->inactive()->create();
    }
}