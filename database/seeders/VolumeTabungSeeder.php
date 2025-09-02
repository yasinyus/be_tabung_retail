<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VolumeTabung;
use Carbon\Carbon;

class VolumeTabungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $volumeTabungs = [
            [
                'tanggal' => Carbon::now()->subDays(1),
                'lokasi' => 'Gudang A',
                'tabung' => [
                    ['qr_code' => 'TBG001', 'volume' => 1.25],
                    ['qr_code' => 'TBG002', 'volume' => 0],
                    ['qr_code' => 'TBG003', 'volume' => 1.25],
                ],
                'nama' => 'Ahmad Suryadi',
                'keterangan' => 'Pengukuran rutin bulanan. Tabung TBG002 kosong dan perlu diisi ulang.',
            ],
            [
                'tanggal' => Carbon::now(),
                'lokasi' => 'Gudang B',
                'tabung' => [
                    ['qr_code' => 'TBG004', 'volume' => 1.25],
                    ['qr_code' => 'TBG005', 'volume' => 1.25],
                ],
                'nama' => 'Siti Nurhaliza',
                'keterangan' => 'Kondisi semua tabung baik. Tidak ada kebocoran atau kerusakan.',
            ],
            [
                'tanggal' => Carbon::now()->subDays(2),
                'lokasi' => 'Gudang C',
                'tabung' => [
                    ['qr_code' => 'TBG006', 'volume' => 0],
                    ['qr_code' => 'TBG007', 'volume' => 1.25],
                    ['qr_code' => 'TBG008', 'volume' => 1.25],
                    ['qr_code' => 'TBG009', 'volume' => 0],
                ],
                'nama' => 'Budi Santoso',
                'keterangan' => 'Ditemukan 2 tabung kosong (TBG006 dan TBG009). Sudah dilakukan pengisian ulang.',
            ],
        ];

        foreach ($volumeTabungs as $data) {
            VolumeTabung::create($data);
        }
    }
}
