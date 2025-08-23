<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MobileUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create mobile app test users for each role

        // 1. Kepala Gudang
        $kepalaGudang = User::updateOrCreate(
            ['email' => 'kepala.gudang@mobile.test'],
            [
                'name' => 'Kepala Gudang Mobile',
                'email' => 'kepala.gudang@mobile.test',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ]
        );
        $kepalaGudang->assignRole('kepala_gudang');

        // 2. Operator
        $operator = User::updateOrCreate(
            ['email' => 'operator@mobile.test'],
            [
                'name' => 'Operator Mobile',
                'email' => 'operator@mobile.test',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ]
        );
        $operator->assignRole('operator');

        // 3. Driver
        $driver = User::updateOrCreate(
            ['email' => 'driver@mobile.test'],
            [
                'name' => 'Driver Mobile',
                'email' => 'driver@mobile.test',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ]
        );
        $driver->assignRole('driver');

        // 4. Test Pelanggan Agen
        $pelangganAgen = Pelanggan::updateOrCreate(
            ['email' => 'agen@mobile.test'],
            [
                'kode_pelanggan' => 'PLG-MOBILE-001',
                'nama_pelanggan' => 'Test Pelanggan Agen',
                'email' => 'agen@mobile.test',
                'password' => Hash::make('password123'),
                'lokasi_pelanggan' => 'Jakarta Mobile Test',
                'jenis_pelanggan' => 'agen',
                'harga_tabung' => 140000,
                'penanggung_jawab' => 'Admin Mobile Test',
            ]
        );

        // 5. Test Pelanggan Umum
        $pelangganUmum = Pelanggan::updateOrCreate(
            ['email' => 'umum@mobile.test'],
            [
                'kode_pelanggan' => 'PLG-MOBILE-002',
                'nama_pelanggan' => 'Test Pelanggan Umum',
                'email' => 'umum@mobile.test',
                'password' => Hash::make('password123'),
                'lokasi_pelanggan' => 'Bandung Mobile Test',
                'jenis_pelanggan' => 'umum',
                'harga_tabung' => 150000,
                'penanggung_jawab' => 'Admin Mobile Test',
            ]
        );

        $this->command->info('Mobile app test users created successfully!');
        $this->command->info('');
        $this->command->info('Test Credentials:');
        $this->command->info('- Kepala Gudang: kepala.gudang@mobile.test / password123');
        $this->command->info('- Operator: operator@mobile.test / password123');
        $this->command->info('- Driver: driver@mobile.test / password123');
        $this->command->info('- Pelanggan Agen: agen@mobile.test / password123');
        $this->command->info('- Pelanggan Umum: umum@mobile.test / password123');
    }
}
