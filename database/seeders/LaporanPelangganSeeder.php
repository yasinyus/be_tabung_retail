<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanPelanggan;
use App\Models\Pelanggan;

class LaporanPelangganSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get first pelanggan to create sample data
        $pelanggan = Pelanggan::first();
        
        if ($pelanggan) {
            // Create sample laporan data
            LaporanPelanggan::create([
                'tanggal' => '2025-01-01',
                'kode_pelanggan' => $pelanggan->kode_pelanggan,
                'keterangan' => 'Deposit',
                'tabung' => null,
                'harga' => null,
                'tambahan_deposit' => 10000,
                'pengurangan_deposit' => null,
                'sisa_deposit' => 10000,
                'konfirmasi' => true,
                'list_tabung' => [],
            ]);

            LaporanPelanggan::create([
                'tanggal' => '2025-02-01',
                'kode_pelanggan' => $pelanggan->kode_pelanggan,
                'keterangan' => 'Tagihan',
                'tabung' => 10,
                'harga' => 0,
                'tambahan_deposit' => null,
                'pengurangan_deposit' => 5000,
                'sisa_deposit' => 5000,
                'konfirmasi' => true,
                'list_tabung' => [],
            ]);

            LaporanPelanggan::create([
                'tanggal' => '2025-02-01',
                'kode_pelanggan' => $pelanggan->kode_pelanggan,
                'keterangan' => 'Kembali',
                'tabung' => 5,
                'harga' => null,
                'tambahan_deposit' => null,
                'pengurangan_deposit' => null,
                'sisa_deposit' => 5000,
                'konfirmasi' => false,
                'list_tabung' => [],
            ]);
        }
    }
}
