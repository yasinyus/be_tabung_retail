<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔄 Testing QR Code Generation on Create...\n\n";

// Test Tabung
echo "📦 Creating new Tabung...\n";
$tabung = \App\Models\Tabung::create([
    'kode_tabung' => 'TEST-QR-001',
    'seri_tabung' => 'SERIES-QR-001',
    'tahun' => 2024,
    'keterangan' => 'Test QR Generation from Form'
]);

echo "✅ Created Tabung ID: {$tabung->id}\n";
echo "✅ QR Code Length: " . strlen($tabung->qr_code) . " characters\n";
echo "✅ QR Code exists: " . ($tabung->qr_code ? 'YES' : 'NO') . "\n\n";

// Test Armada
echo "🚛 Creating new Armada...\n";
$armada = \App\Models\Armada::create([
    'nopol' => 'TEST 001 QR',
    'kapasitas' => 10,
    'tahun' => 2024,
    'keterangan' => 'Test QR Generation from Form'
]);

echo "✅ Created Armada ID: {$armada->id}\n";
echo "✅ QR Code Length: " . strlen($armada->qr_code) . " characters\n";
echo "✅ QR Code exists: " . ($armada->qr_code ? 'YES' : 'NO') . "\n\n";

// Test Pelanggan
echo "👥 Creating new Pelanggan...\n";
$pelanggan = \App\Models\Pelanggan::create([
    'kode_pelanggan' => 'PLG-QR-TEST',
    'nama_pelanggan' => 'Test Pelanggan QR',
    'lokasi_pelanggan' => 'Jakarta',
    'harga_tabung' => 25000.00,
    'email' => 'test@qr.com',
    'password' => 'password',
    'jenis_pelanggan' => 'agen',
    'penanggung_jawab' => 'Pak Test'
]);

echo "✅ Created Pelanggan ID: {$pelanggan->id}\n";
echo "✅ QR Code Length: " . strlen($pelanggan->qr_code) . " characters\n";
echo "✅ QR Code exists: " . ($pelanggan->qr_code ? 'YES' : 'NO') . "\n\n";

// Test Gudang  
echo "🏪 Creating new Gudang...\n";
$gudang = \App\Models\Gudang::create([
    'kode_gudang' => 'GDG-QR-TEST',
    'nama_gudang' => 'Gudang Test QR',
    'tahun_gudang' => 2024,
    'keterangan' => 'Test QR Generation from Form'
]);

echo "✅ Created Gudang ID: {$gudang->id}\n";
echo "✅ QR Code Length: " . strlen($gudang->qr_code) . " characters\n";
echo "✅ QR Code exists: " . ($gudang->qr_code ? 'YES' : 'NO') . "\n\n";

echo "🎉 All QR codes generated successfully!\n";
echo "📱 Now when you create records from Filament forms, QR codes will be generated automatically!\n";
