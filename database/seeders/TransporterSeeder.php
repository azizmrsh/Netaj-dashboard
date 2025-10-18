<?php

namespace Database\Seeders;

use App\Models\Transporter;
use Illuminate\Database\Seeder;

class TransporterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 95 active transporters
        Transporter::factory(95)->create();
        
        // Create 5 inactive transporters
        Transporter::factory(5)->inactive()->create();
    }
}