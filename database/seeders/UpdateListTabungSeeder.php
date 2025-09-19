<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LaporanPelanggan;

class UpdateListTabungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing laporan records with sample list_tabung data
        $laporanRecords = LaporanPelanggan::where('kode_pelanggan', 'PU002')->get();

        foreach ($laporanRecords as $laporan) {
            // Sample list_tabung data
            $sampleListTabung = [
                [
                    'kode_tabung' => 'TB001-' . $laporan->id,
                    'volume' => '3kg',
                    'jenis' => 'Gas LPG',
                    'harga' => 75000,
                    'brand' => 'Pertamina'
                ],
                [
                    'kode_tabung' => 'TB002-' . $laporan->id,
                    'volume' => '12kg',
                    'jenis' => 'Gas LPG',
                    'harga' => 175000,
                    'brand' => 'Pertamina'
                ]
            ];
            
            $laporan->update([
                'list_tabung' => $sampleListTabung
            ]);
            
            $this->command->info("Updated laporan ID {$laporan->id} with list_tabung data");
        }

        $this->command->info("All laporan records updated with sample list_tabung data!");
    }
}
