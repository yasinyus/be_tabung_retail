<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Utama
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@ptgas.com',
            'password' => Hash::make('password123'),
            'role' => 'admin_utama',
            'email_verified_at' => now(),
        ]);

        // Admin Umum
        User::create([
            'name' => 'Admin Umum',
            'email' => 'admin.umum@ptgas.com',
            'password' => Hash::make('password123'),
            'role' => 'admin_umum',
            'email_verified_at' => now(),
        ]);

        // Kepala Gudang
        User::create([
            'name' => 'Kepala Gudang',
            'email' => 'kepala.gudang@ptgas.com',
            'password' => Hash::make('password123'),
            'role' => 'kepala_gudang',
            'email_verified_at' => now(),
        ]);

        // Operator Retail
        User::create([
            'name' => 'Operator Retail 1',
            'email' => 'operator1@ptgas.com',
            'password' => Hash::make('password123'),
            'role' => 'operator_retail',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Operator Retail 2',
            'email' => 'operator2@ptgas.com',
            'password' => Hash::make('password123'),
            'role' => 'operator_retail',
            'email_verified_at' => now(),
        ]);

        // Driver
        User::create([
            'name' => 'Driver 1',
            'email' => 'driver1@ptgas.com',
            'password' => Hash::make('password123'),
            'role' => 'driver',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Driver 2',
            'email' => 'driver2@ptgas.com',
            'password' => Hash::make('password123'),
            'role' => 'driver',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Driver 3',
            'email' => 'driver3@ptgas.com',
            'password' => Hash::make('password123'),
            'role' => 'driver',
            'email_verified_at' => now(),
        ]);

        // Test Users untuk development
        User::create([
            'name' => 'Test User',
            'email' => 'test@ptgas.com',
            'password' => Hash::make('password'),
            'role' => 'admin_umum',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Users seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin Utama: admin@ptgas.com / password123');
        $this->command->info('Admin Umum: admin.umum@ptgas.com / password123');
        $this->command->info('Kepala Gudang: kepala.gudang@ptgas.com / password123');
        $this->command->info('Operator: operator1@ptgas.com / password123');
        $this->command->info('Driver: driver1@ptgas.com / password123');
    }
}
