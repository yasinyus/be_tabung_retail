<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin utama user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@tabungretail.com'],
            [
                'name' => 'Administrator Utama', 
                'role' => 'admin_utama',
                'password' => Hash::make('admin123'),
            ]
        );

        // Assign role admin_utama
        $adminUser->assignRole('admin_utama');

        // Create sample users for other roles
        $sampleUsers = [
            [
                'name' => 'Admin Umum',
                'email' => 'admin_umum@tabungretail.com',
                'role' => 'admin_umum',
                'password' => Hash::make('admin123'),
            ],
            [
                'name' => 'Kepala Gudang',
                'email' => 'kepala_gudang@tabungretail.com',
                'role' => 'kepala_gudang',
                'password' => Hash::make('admin123'),
            ],
            [
                'name' => 'Operator Retail',
                'email' => 'operator@tabungretail.com',
                'role' => 'operator_retail',
                'password' => Hash::make('admin123'),
            ],
        ];

        foreach ($sampleUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            $user->assignRole($userData['role']);
        }
    }
}
