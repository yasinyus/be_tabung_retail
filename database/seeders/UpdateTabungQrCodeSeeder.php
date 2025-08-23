<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tabung;

class UpdateTabungQrCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tabungs = Tabung::whereNull('qr_code')->get();
        
        foreach ($tabungs as $tabung) {
            $tabung->qr_code = base64_encode($tabung->generateQrCode());
            $tabung->save();
            
            $this->command->info("QR Code generated for: {$tabung->kode_tabung}");
        }
        
        $this->command->info("QR Code generation completed for {$tabungs->count()} tabungs!");
    }
}
