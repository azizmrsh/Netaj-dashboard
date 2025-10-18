<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 80 active products
        Product::factory(80)->create();
        
        // Create 15 premium products
        Product::factory(15)->premium()->create();
        
        // Create 5 inactive products
        Product::factory(5)->inactive()->create();
    }
}