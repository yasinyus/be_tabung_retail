<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pelanggan;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pelanggans = [
            [
                'kode_pelanggan' => 'PLG-001',
                'nama_pelanggan' => 'PT Maju Sejahtera',
                'lokasi_pelanggan' => 'Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta 10220',
                'harga_tabung' => 15000.00,
                'email' => 'admin@majusejahtera.com',
                'password' => 'password123',
                'jenis_pelanggan' => 'agen',
                'penanggung_jawab' => 'Budi Santoso'
            ],
            [
                'kode_pelanggan' => 'PLG-002',
                'nama_pelanggan' => 'Warung Pak Hasan',
                'lokasi_pelanggan' => 'Jl. Kebon Jeruk Raya No. 45, Jakarta Barat, DKI Jakarta 11530',
                'harga_tabung' => 12000.00,
                'email' => 'hasan.warung@gmail.com',
                'password' => 'password123',
                'jenis_pelanggan' => 'umum',
                'penanggung_jawab' => null
            ],
            [
                'kode_pelanggan' => 'PLG-003',
                'nama_pelanggan' => 'CV Berkah Jaya',
                'lokasi_pelanggan' => 'Jl. Raya Bogor KM 25, Depok, Jawa Barat 16454',
                'harga_tabung' => 14500.00,
                'email' => 'info@berkahjaya.co.id',
                'password' => 'password123',
                'jenis_pelanggan' => 'agen',
                'penanggung_jawab' => 'Siti Aminah'
            ],
            [
                'kode_pelanggan' => 'PLG-004',
                'nama_pelanggan' => 'Rumah Makan Sederhana',
                'lokasi_pelanggan' => 'Jl. Ahmad Yani No. 78, Bekasi, Jawa Barat 17141',
                'harga_tabung' => 13000.00,
                'email' => 'rm.sederhana@yahoo.com',
                'password' => 'password123',
                'jenis_pelanggan' => 'umum',
                'penanggung_jawab' => 'Ahmad Fauzi'
            ],
            [
                'kode_pelanggan' => 'PLG-005',
                'nama_pelanggan' => 'Toko Gas Abadi',
                'lokasi_pelanggan' => 'Jl. Pahlawan No. 156, Tangerang, Banten 15118',
                'harga_tabung' => 16000.00,
                'email' => 'tokogasabadi@gmail.com',
                'password' => 'password123',
                'jenis_pelanggan' => 'agen',
                'penanggung_jawab' => 'Rina Marlina'
            ],
            [
                'kode_pelanggan' => 'PLG-006',
                'nama_pelanggan' => 'Ibu Sari',
                'lokasi_pelanggan' => 'Jl. Mawar No. 23, RT 05/02, Kelapa Gading, Jakarta Utara 14240',
                'harga_tabung' => 11500.00,
                'email' => 'sari.kelapa@gmail.com',
                'password' => 'password123',
                'jenis_pelanggan' => 'umum',
                'penanggung_jawab' => null
            ],
            [
                'kode_pelanggan' => 'PLG-007',
                'nama_pelanggan' => 'PT Karya Mandiri',
                'lokasi_pelanggan' => 'Komplek Industri Pulogadung, Jl. Raya Bekasi KM 22, Jakarta Timur 13920',
                'harga_tabung' => 17500.00,
                'email' => 'procurement@karyamandiri.com',
                'password' => 'password123',
                'jenis_pelanggan' => 'agen',
                'penanggung_jawab' => 'Dedi Kurniawan'
            ],
            [
                'kode_pelanggan' => 'PLG-008',
                'nama_pelanggan' => 'Warung Bu Ningsih',
                'lokasi_pelanggan' => 'Jl. Raya Serpong No. 89, Serpong, Tangerang Selatan 15310',
                'harga_tabung' => 12500.00,
                'email' => 'ningsih.warung@gmail.com',
                'password' => 'password123',
                'jenis_pelanggan' => 'umum',
                'penanggung_jawab' => null
            ],
            [
                'kode_pelanggan' => 'PLG-009',
                'nama_pelanggan' => 'Hotel Bintang Lima',
                'lokasi_pelanggan' => 'Jl. Thamrin No. 1, Jakarta Pusat, DKI Jakarta 10310',
                'harga_tabung' => 18000.00,
                'email' => 'kitchen@hotelbintanglima.com',
                'password' => 'password123',
                'jenis_pelanggan' => 'agen',
                'penanggung_jawab' => 'Chef Michael'
            ],
            [
                'kode_pelanggan' => 'PLG-010',
                'nama_pelanggan' => 'Pak Joko',
                'lokasi_pelanggan' => 'Jl. Melati No. 67, RT 08/03, Pondok Indah, Jakarta Selatan 12310',
                'harga_tabung' => 11000.00,
                'email' => 'joko.pondokindah@gmail.com',
                'password' => 'password123',
                'jenis_pelanggan' => 'umum',
                'penanggung_jawab' => null
            ],
        ];

        foreach ($pelanggans as $pelangganData) {
            Pelanggan::create($pelangganData);
        }
    }
}
