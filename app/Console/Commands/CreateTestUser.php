<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test users for API testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test users...');

        // Create admin user
        $adminUser = User::where('email', 'admin@example.com')->first();
        
        if ($adminUser) {
            $this->info('✅ Admin user already exists: ' . $adminUser->email);
        } else {
            $adminUser = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]);
            $this->info('✅ Admin user created: ' . $adminUser->email);
        }

        // Create test pelanggan
        $pelanggan = Pelanggan::where('email', 'pelanggan@example.com')->first();
        
        if ($pelanggan) {
            $this->info('✅ Test pelanggan already exists: ' . $pelanggan->email);
        } else {
            $pelanggan = Pelanggan::create([
                'nama_pelanggan' => 'Test Pelanggan',
                'email' => 'pelanggan@example.com',
                'password' => Hash::make('password'),
                'lokasi_pelanggan' => 'Jl. Test No. 123',
                'kode_pelanggan' => 'PEL-' . time(),
                'jenis_pelanggan' => 'umum',
                'penanggung_jawab' => 'Test User',
                'harga_tabung' => 50000.00
            ]);
            $this->info('✅ Test pelanggan created: ' . $pelanggan->email);
        }

        $this->info('Test users ready!');
        $this->info('Admin: admin@example.com / password');
        $this->info('Pelanggan: pelanggan@example.com / password');
    }
}
