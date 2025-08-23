<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Armada;

class ArmadaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $armadas = [
            [
                'nopol' => 'B 1234 ABC',
                'kapasitas' => 5,
                'tahun' => 2020,
                'keterangan' => 'Truk pengangkut tabung gas untuk wilayah Jakarta Selatan dan sekitarnya'
            ],
            [
                'nopol' => 'B 5678 DEF',
                'kapasitas' => 8,
                'tahun' => 2019,
                'keterangan' => 'Truk besar untuk distribusi antar kota'
            ],
            [
                'nopol' => 'D 9876 GHI',
                'kapasitas' => 3,
                'tahun' => 2021,
                'keterangan' => 'Kendaraan kecil untuk area perkotaan padat'
            ],
            [
                'nopol' => 'F 2468 JKL',
                'kapasitas' => 10,
                'tahun' => 2018,
                'keterangan' => 'Truk terbesar untuk rute distribusi utama'
            ],
            [
                'nopol' => 'B 1357 MNO',
                'kapasitas' => 5,
                'tahun' => 2022,
                'keterangan' => 'Kendaraan cadangan untuk peak season'
            ],
            [
                'nopol' => 'A 3691 PQR',
                'kapasitas' => 8,
                'tahun' => 2017,
                'keterangan' => 'Truk untuk distribusi wilayah Banten'
            ],
            [
                'nopol' => 'E 7531 STU',
                'kapasitas' => 3,
                'tahun' => 2023,
                'keterangan' => 'Kendaraan baru untuk ekspansi wilayah timur'
            ],
            [
                'nopol' => 'H 9513 VWX',
                'kapasitas' => 5,
                'tahun' => 2020,
                'keterangan' => 'Kendaraan untuk rute pegunungan'
            ],
            [
                'nopol' => 'AB 8642 YZ',
                'kapasitas' => 10,
                'tahun' => 2016,
                'keterangan' => 'Truk veteran dengan track record terbaik'
            ],
            [
                'nopol' => 'B 4681 ABC',
                'kapasitas' => 1,
                'tahun' => 2024,
                'keterangan' => 'Motor roda tiga untuk deliveri cepat area sempit'
            ],
        ];

        foreach ($armadas as $armadaData) {
            Armada::create($armadaData);
        }
    }
}
