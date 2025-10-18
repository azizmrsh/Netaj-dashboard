<?php

namespace Database\Seeders;

use App\Models\DeliveryDocument;
use Illuminate\Database\Seeder;

class DeliveryDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 100 delivery documents with existing relations
        DeliveryDocument::factory(100)->withExistingRelations()->create();
    }
}