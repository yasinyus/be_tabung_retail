<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gudang;

class GudangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gudangs = [
            [
                'kode_gudang' => 'GDG-001',
                'nama_gudang' => 'Gudang Pusat Jakarta',
                'tahun_gudang' => 2020,
                'keterangan' => 'Gudang utama untuk distribusi wilayah Jakarta dan sekitarnya'
            ],
            [
                'kode_gudang' => 'GDG-002', 
                'nama_gudang' => 'Gudang Tangerang',
                'tahun_gudang' => 2019,
                'keterangan' => 'Gudang cabang untuk melayani wilayah Tangerang dan Banten'
            ],
            [
                'kode_gudang' => 'GDG-003',
                'nama_gudang' => 'Gudang Bekasi',
                'tahun_gudang' => 2021,
                'keterangan' => 'Gudang untuk area Bekasi dan Jakarta Timur'
            ],
            [
                'kode_gudang' => 'GDG-004',
                'nama_gudang' => 'Gudang Depok',
                'tahun_gudang' => 2018,
                'keterangan' => 'Gudang untuk wilayah Depok dan Jakarta Selatan'
            ],
            [
                'kode_gudang' => 'GDG-005',
                'nama_gudang' => 'Gudang Bogor',
                'tahun_gudang' => 2022,
                'keterangan' => 'Gudang baru untuk ekspansi ke area Bogor'
            ],
            [
                'kode_gudang' => 'GDG-006',
                'nama_gudang' => 'Gudang Cikampek',
                'tahun_gudang' => 2017,
                'keterangan' => 'Gudang transit untuk distribusi ke luar kota'
            ],
            [
                'kode_gudang' => 'GDG-007',
                'nama_gudang' => 'Gudang Bandung',
                'tahun_gudang' => 2023,
                'keterangan' => 'Gudang cabang untuk melayani wilayah Jawa Barat'
            ],
            [
                'kode_gudang' => 'GDG-008',
                'nama_gudang' => 'Gudang Semarang',
                'tahun_gudang' => 2020,
                'keterangan' => 'Gudang untuk distribusi Jawa Tengah'
            ],
            [
                'kode_gudang' => 'GDG-009',
                'nama_gudang' => 'Gudang Surabaya',
                'tahun_gudang' => 2016,
                'keterangan' => 'Gudang veteran untuk wilayah Jawa Timur'
            ],
            [
                'kode_gudang' => 'GDG-010',
                'nama_gudang' => 'Gudang Karawang',
                'tahun_gudang' => 2024,
                'keterangan' => 'Gudang terbaru dengan fasilitas modern'
            ],
        ];

        foreach ($gudangs as $gudangData) {
            Gudang::create($gudangData);
        }
    }
}
