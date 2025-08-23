<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'admin_utama',
            'admin_umum',
            'kepala_gudang',
            'operator_retail',
            'operator', // Added for mobile app
            'driver',
            'auditor',
            'pelanggan_umum',
            'pelanggan_agen'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
