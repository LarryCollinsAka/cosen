<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if an admin user already exists to prevent duplicates
        $admin = User::firstOrCreate(
            [
                'email' => 'admin@admin.com'
            ],
            [
                'name' => 'Ngwa Edwin',
                'password' => Hash::make('password'), // Use a secure password in production!
                'phone' => '+237671110080',
            ]
        );

        // Assign the 'administrator' role to the user
        $admin->assignRole('administrator');
    }
}
