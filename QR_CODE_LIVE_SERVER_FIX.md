# 🔧 QR Code Fix untuk Live Server

## Masalah QR Code di Live Server

Kemungkinan masalah:
1. **Symbolic link belum dibuat** - File QR code tidak bisa diakses via URL
2. **Permission issue** - Storage folder tidak writable
3. **Jobs tidak running** - QR code generation gagal
4. **URL configuration** - Base URL tidak sesuai

## Langkah-langkah Perbaikan:

### 1. Buat Symbolic Link Storage
```bash
# Di server live, jalankan:
php artisan storage:link
```

### 2. Set Permission yang Benar
```bash
# Set permission untuk storage
chmod -R 775 storage/
chmod -R 775 public/storage/

# Atau untuk shared hosting:
chmod -R 755 storage/
chmod -R 755 public/storage/
```

### 3. Cek Konfigurasi .env
```env
# Pastikan APP_URL sesuai domain live
APP_URL=https://test.gasalamsolusi.my.id

# Queue configuration untuk job processing
QUEUE_CONNECTION=database
# atau
QUEUE_CONNECTION=sync
```

### 4. Process QR Code Jobs
```bash
# Jika menggunakan queue database
php artisan queue:work

# Atau regenerate semua QR code secara sync
php artisan tabung:fix-qr
php artisan armada:fix-qr
php artisan gudang:fix-qr
php artisan pelanggan:fix-qr
```

### 5. Cek File Permission Detail
```bash
# Cek apakah folder ada
ls -la storage/app/public/qr_codes/

# Cek permission
ls -la public/storage/
```

### 6. Test QR Code Generation
```bash
# Test generate QR code untuk satu item
php artisan tabung:test-qr 1
php artisan gudang:test-qr 1
```

## Debugging Commands:

### Cek Storage Link
```bash
# Cek apakah symbolic link ada
ls -la public/storage

# Hapus dan buat ulang jika perlu
rm public/storage
php artisan storage:link
```

### Manual QR Code Test
```bash
# Buka tinker untuk test manual
php artisan tinker

# Test QR generation:
$tabung = App\Models\Tabung::first();
$qr = $tabung->generateQrCode();
echo "QR Generated: " . ($qr ? "YES" : "NO");

# Test file access:
$path = storage_path('app/public/qr_codes/tabung/tabung_' . $tabung->id . '.png');
echo "File exists: " . (file_exists($path) ? "YES" : "NO");
```

## Solusi untuk Berbagai Hosting:

### Shared Hosting (cPanel)
1. Upload via File Manager
2. Set permission via cPanel
3. Pastikan symbolic link via cPanel atau manual

### VPS/Cloud Server
1. SSH access untuk commands
2. Set proper web server configuration
3. Ensure storage folder is writable

### Docker/Container
1. Mount storage volume
2. Set container permissions
3. Ensure base URL configuration

## Quick Fix Commands untuk Live Server:

```bash
# All-in-one fix command
php artisan storage:link && \
chmod -R 755 storage/ && \
chmod -R 755 public/storage/ && \
php artisan config:cache && \
php artisan route:cache && \
php artisan tabung:fix-qr && \
php artisan armada:fix-qr && \
php artisan gudang:fix-qr && \
php artisan pelanggan:fix-qr
```
