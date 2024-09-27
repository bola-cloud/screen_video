<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@goo.com',
            'phone' => '0000000000', // Default phone number
            'company' => null,
            'category' => 'admin', // Set the category to admin
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'), // Hashed password
            'remember_token' => \Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Client account
        DB::table('users')->insert([
            'name' => 'Client',
            'email' => 'client@goo.com',
            'phone' => '1111111111', // Default phone number for the client
            'company' => 'Client Company', // Client's company (can be null if needed)
            'category' => 'client', // Set the category to client
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'), // Same password (or you can use a different one)
            'remember_token' => \Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}