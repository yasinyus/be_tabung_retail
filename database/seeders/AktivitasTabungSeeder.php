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
                'tujuan' => 'AR-!',
                'tabung' => '["QR_tabung1","QR_tabung2","QR_tabung3"]',
                'keterangan' => 'keterangan opsional',
                'nama_petugas' => 'Kepala Gudang Mobile',
                'id_user' => 1,
                'total_tabung' => 3,
                'tanggal' => '29/8/2025',
                'status' => 'Kosong',
                'waktu' => now(),
            ],
            [
                'id' => 27,
                'nama_aktivitas' => 'Terima Tabung Universal',
                'dari' => 'GD-1',
                'tujuan' => 'AR-!',
                'tabung' => '["QR_tabung1","QR_tabung2","QR_tabung3"]',
                'keterangan' => 'keterangan opsional',
                'nama_petugas' => 'Driver Mobile',
                'id_user' => 9,
                'total_tabung' => 3,
                'tanggal' => '29/8/2025',
                'status' => 'Kosong',
                'waktu' => '2025-08-29 13:57:00',
            ],
            [
                'id' => 35,
                'nama_aktivitas' => 'Terima Tabung Dari Armada',
                'dari' => 'B 5678 DEF',
                'tujuan' => 'GDG-002',
                'tabung' => '["T-1001","TBG-0064","T-0011","TBG-001"]',
                'keterangan' => 'Dinisjsjs',
                'nama_petugas' => 'Kepala Gudang Mobile',
                'id_user' => 7,
                'total_tabung' => 4,
                'tanggal' => '30/8/2025',
                'status' => 'Kosong',
                'waktu' => '2025-08-30 10:45:25',
            ],
            [
                'id' => 36,
                'nama_aktivitas' => 'Kirim Tabung ke Armada',
                'dari' => 'GDG-002',
                'tujuan' => 'B 5678 DEF',
                'tabung' => '["T-0011","TBG-001"]',
                'keterangan' => 'keterangan opsional',
                'nama_petugas' => 'Kepala Gudang Mobile',
                'id_user' => 7,
                'total_tabung' => 2,
                'tanggal' => '30/8/2025',
                'status' => 'Kosong',
                'waktu' => '2025-08-30 22:04:00',
            ],
            [
                'id' => 37,
                'nama_aktivitas' => 'Kirim Tabung ke Agen',
                'dari' => 'GDG-002',
                'tujuan' => 'hdhs',
                'tabung' => '["aa","aa","aa","aa"]',
                'keterangan' => 'ijejd',
                'nama_petugas' => 'Kepala Gudang Mobile',
                'id_user' => 7,
                'total_tabung' => 4,
                'tanggal' => '31/8/2025',
                'status' => 'Kosong',
                'waktu' => '2025-08-31 00:57:06',
            ],
            [
                'id' => 38,
                'nama_aktivitas' => 'Terima Tabung Dari Armada',
                'dari' => 'B 5678 DEF',
                'tujuan' => 'GDG-002',
                'tabung' => '["T-1001","TBG-0064","T-0011"]',
                'keterangan' => 'keterangan opsional',
                'nama_petugas' => 'Kepala Gudang Mobile',
                'id_user' => 7,
                'total_tabung' => 3,
                'tanggal' => '31/8/2025',
                'status' => 'Kosong',
                'waktu' => '2025-08-31 14:52:17',
            ],
            [
                'id' => 39,
                'nama_aktivitas' => 'Terima Tabung Dari Armada',
                'dari' => 'B 5678 DEF',
                'tujuan' => 'GDG-002',
                'tabung' => '["T-0011","Scan QR"]',
                'keterangan' => 'keterangan opsional',
                'nama_petugas' => 'Kepala Gudang Mobile',
                'id_user' => 7,
                'total_tabung' => 2,
                'tanggal' => '31/8/2025',
                'status' => 'Kosong',
                'waktu' => '2025-08-31 16:00:29',
            ],
            [
                'id' => 40,
                'nama_aktivitas' => 'Terima Tabung Dari Agen',
                'dari' => 'GDG-002',
                'tujuan' => 'GDG-001',
                'tabung' => '["T-0011","TBG-001"]',
                'keterangan' => 'keterangan opsional',
                'nama_petugas' => 'Kepala Gudang Mobile',
                'id_user' => 7,
                'total_tabung' => 2,
                'tanggal' => '31/8/2025',
                'status' => 'Kosong',
                'waktu' => '2025-08-31 16:01:08',
            ],
            [
                'id' => 41,
                'nama_aktivitas' => 'Kirim Tabung ke Armada',
                'dari' => 'GDG-002',
                'tujuan' => 'B 5678 DEF',
                'tabung' => '["Scan QR","Scan QR"]',
                'keterangan' => 'keterangan opsional',
                'nama_petugas' => 'Kepala Gudang Mobile',
                'id_user' => 7,
                'total_tabung' => 2,
                'tanggal' => '31/8/2025',
                'status' => 'Kosong',
                'waktu' => '2025-08-31 16:01:43',
            ],
            [
                'id' => 42,
                'nama_aktivitas' => 'Kirim Tabung ke Agen',
                'dari' => 'GDG-002',
                'tujuan' => 'GDG-002',
                'tabung' => '["GDG-002","T-0011","TBG-001"]',
                'keterangan' => 'keterangan opsional',
                'nama_petugas' => 'Kepala Gudang Mobile',
                'id_user' => 7,
                'total_tabung' => 3,
                'tanggal' => '31/8/2025',
                'status' => 'Kosong',
                'waktu' => '2025-08-31 19:48:49',
            ],
        ];

        foreach ($data as $item) {
            DB::table('aktivitas_tabung')->insert($item);
        }
    }
}
