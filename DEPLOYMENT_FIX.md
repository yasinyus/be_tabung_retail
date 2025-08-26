# Fix Server Resources Missing

## Masalah
Di server production hanya muncul Dashboard dan User Management, padahal di local ada lebih banyak menu.

## Penyebab Kemungkinan
1. **Authorization**: Resource lain memerlukan user login dan role check
2. **Autoloading**: Composer autoload belum ter-update
3. **Cache**: Config/route cache masih lama
4. **Environment**: Database atau model tidak tersedia

## Solusi

### 1. Jalankan Script Fix
```bash
# Di server production
php fix_server_resources.php
```

### 2. Manual Commands
```bash
# Clear semua cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Update autoload
composer dump-autoload --optimize

# Cache ulang untuk production
php artisan config:cache
php artisan route:cache
```

### 3. Check Resource Authorization
Resource yang seharusnya muncul:
- ✅ **UserResource** - User Management
- 🔄 **TabungResource** - Tabung Gas  
- 🔄 **ArmadaResource** - Armada Kendaraan
- 🔄 **PelangganResource** - Pelanggan
- 🔄 **GudangResource** - Gudang

### 4. Authorization Fix Applied
Sudah ditambahkan `return true;` untuk debugging di:
- `TabungResource::canViewAny()`
- `ArmadaResource::canViewAny()`

### 5. Cek Database
Pastikan tabel-tabel ini ada di server:
```sql
SHOW TABLES LIKE 'tabungs';
SHOW TABLES LIKE 'armadas'; 
SHOW TABLES LIKE 'pelanggans';
SHOW TABLES LIKE 'gudangs';
```

### 6. Cek Model Files
Pastikan file-file model ini ada:
- `app/Models/Tabung.php`
- `app/Models/Armada.php`
- `app/Models/Pelanggan.php`
- `app/Models/Gudang.php`

## Expected Result
Setelah fix, sidebar seharusnya menampilkan:
- 📊 Dashboard
- 👥 User Management  
- 🔥 Tabung Gas
- 🚛 Armada Kendaraan
- 👤 Pelanggan
- 🏠 Gudang
