<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::factory()->create([
            'name' => 'مدير النظام',
            'email' => 'admin@netaj.com',
            'password' => bcrypt('password'),
        ]);

        // Create 99 additional users
        User::factory(99)->create();
    }
}