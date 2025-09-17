<?php

use App\Models\Tabung;
use App\Models\StokTabung;

// Populate stok_tabung dengan data tabung yang ada
$tabungs = Tabung::all();

foreach ($tabungs as $tabung) {
    StokTabung::firstOrCreate([
        'kode_tabung' => $tabung->kode_tabung
    ], [
        'status' => 'Kosong',
        'posisi' => 'Gudang Utama',
        'tanggal_update' => now()
    ]);
}

echo "Berhasil membuat " . $tabungs->count() . " data stok tabung\n";
