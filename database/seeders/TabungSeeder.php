<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tabung;

class TabungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tabungs = [
            [
                'kode_tabung' => 'TBG-001',
                'seri_tabung' => 'A123456789',
                'tahun' => 2023,
                'keterangan' => 'Tabung gas 3kg kondisi baik'
            ],
            [
                'kode_tabung' => 'TBG-002',
                'seri_tabung' => 'B234567890',
                'tahun' => 2023,
                'keterangan' => 'Tabung gas 12kg untuk retail'
            ],
            [
                'kode_tabung' => 'TBG-003',
                'seri_tabung' => 'C345678901',
                'tahun' => 2024,
                'keterangan' => 'Tabung gas 5.5kg baru'
            ],
            [
                'kode_tabung' => 'TBG-004',
                'seri_tabung' => 'D456789012',
                'tahun' => 2024,
                'keterangan' => 'Tabung gas 3kg untuk pengiriman'
            ],
            [
                'kode_tabung' => 'TBG-005',
                'seri_tabung' => 'E567890123',
                'tahun' => 2022,
                'keterangan' => 'Tabung gas 12kg perlu inspeksi'
            ],
            [
                'kode_tabung' => 'TBG-006',
                'seri_tabung' => 'F678901234',
                'tahun' => 2025,
                'keterangan' => 'Tabung gas 3kg terbaru'
            ],
            [
                'kode_tabung' => 'TBG-007',
                'seri_tabung' => 'G789012345',
                'tahun' => 2024,
                'keterangan' => 'Tabung gas 5.5kg ready stock'
            ],
            [
                'kode_tabung' => 'TBG-008',
                'seri_tabung' => 'H890123456',
                'tahun' => 2023,
                'keterangan' => 'Tabung gas 12kg cadangan'
            ],
        ];

        foreach ($tabungs as $tabung) {
            Tabung::firstOrCreate(
                ['kode_tabung' => $tabung['kode_tabung']],
                $tabung
            );
        }

        $this->command->info('Tabung dummy data created successfully!');
    }
}
