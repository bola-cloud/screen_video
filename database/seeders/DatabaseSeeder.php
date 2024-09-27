<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call both the TvsSeeder and AdminUserSeeder
        $this->call([
            TvsSeeder::class, // Calls the TV seed data
            AdminUserSeeder::class, // Calls the Admin User seed data
        ]);
    }
}
