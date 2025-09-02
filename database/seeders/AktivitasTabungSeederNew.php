<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AktivitasTabungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table first to avoid duplicate entries
        DB::table('aktivitas_tabung')->truncate();

        $data = [
            [
                'nama_aktivitas' => 'Terima Tabung',
                'dari' => 'GD-1',
                'tujuan' => 'AR-1',
                'tabung' => '["QR_tabung1","QR_tabung2","QR_tabung3"]',
                'keterangan' => 'Terima tabung dari armada 1',
                'nama_petugas' => 'Kepala Gudang Mobile',
                'id_user' => 1,
                'total_tabung' => 3,
                'tanggal' => '29/8/2025',
                'status' => 'Isi',
                'waktu' => now(),
            ],
            [
                'nama_aktivitas' => 'Kirim Tabung',
                'dari' => 'GD-2',
                'tujuan' => 'AR-2',
                'tabung' => '["QR_tabung4","QR_tabung5"]',
                'keterangan' => 'Kirim tabung ke armada 2',
                'nama_petugas' => 'Staff Gudang',
                'id_user' => 1,
                'total_tabung' => 2,
                'tanggal' => '29/8/2025',
                'status' => 'Kosong',
                'waktu' => now(),
            ],
            [
                'nama_aktivitas' => 'Pindah Tabung',
                'dari' => 'GD-1',
                'tujuan' => 'GD-2',
                'tabung' => '["QR_tabung6","QR_tabung7","QR_tabung8","QR_tabung9"]',
                'keterangan' => 'Pindah tabung antar gudang',
                'nama_petugas' => 'Supervisor',
                'id_user' => 1,
                'total_tabung' => 4,
                'tanggal' => '30/8/2025',
                'status' => 'Pending',
                'waktu' => now(),
            ],
            [
                'nama_aktivitas' => 'Terima Tabung',
                'dari' => 'AR-3',
                'tujuan' => 'GD-1',
                'tabung' => '["QR_tabung10","QR_tabung11"]',
                'keterangan' => 'Terima kembali dari armada 3',
                'nama_petugas' => 'Kepala Gudang',
                'id_user' => 1,
                'total_tabung' => 2,
                'tanggal' => '30/8/2025',
                'status' => 'Isi',
                'waktu' => now(),
            ],
            [
                'nama_aktivitas' => 'Maintenance',
                'dari' => 'GD-1',
                'tujuan' => 'Workshop',
                'tabung' => '["QR_tabung12"]',
                'keterangan' => 'Maintenance tabung rusak',
                'nama_petugas' => 'Teknisi',
                'id_user' => 1,
                'total_tabung' => 1,
                'tanggal' => '31/8/2025',
                'status' => 'Kosong',
                'waktu' => now(),
            ],
        ];

        DB::table('aktivitas_tabung')->insert($data);
    }
}
