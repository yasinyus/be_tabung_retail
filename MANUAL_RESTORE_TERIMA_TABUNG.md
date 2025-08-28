# Manual Restore API Terima Tabung

## 🔄 Mengembalikan ke Versi Sebelumnya

Jika terjadi error setelah menjalankan script fix, ikuti langkah-langkah ini untuk mengembalikan ke versi sebelumnya.

## 📦 Langkah 1: Cari File Backup

Script `fix_terima_tabung_simple.php` membuat backup dengan format:
```
bootstrap/app.php.backup.YYYY-MM-DD-HH-MM-SS
routes/web.php.backup.YYYY-MM-DD-HH-MM-SS
app/Http/Controllers/Api/AuthController.php.backup.YYYY-MM-DD-HH-MM-SS
```

```bash
# Cari file backup
ls -la *.backup.*
ls -la bootstrap/*.backup.*
ls -la app/Http/Controllers/Api/*.backup.*
```

## 🔄 Langkah 2: Restore Manual

### Restore bootstrap/app.php
```bash
# Cari backup terbaru
ls -t bootstrap/app.php.backup.* | head -1

# Restore (ganti TIMESTAMP dengan timestamp yang sesuai)
cp bootstrap/app.php.backup.TIMESTAMP bootstrap/app.php
```

### Restore routes/web.php
```bash
# Cari backup terbaru
ls -t routes/web.php.backup.* | head -1

# Restore
cp routes/web.php.backup.TIMESTAMP routes/web.php
```

### Restore AuthController
```bash
# Cari backup terbaru
ls -t app/Http/Controllers/Api/AuthController.php.backup.* | head -1

# Restore
cp app/Http/Controllers/Api/AuthController.php.backup.TIMESTAMP app/Http/Controllers/Api/AuthController.php
```

## 🧹 Langkah 3: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

## 🔧 Langkah 4: Restart Web Server

```bash
# Untuk Nginx
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

# Untuk Apache
sudo systemctl restart apache2
```

## 🧪 Langkah 5: Test

```bash
# Test Laravel
php artisan --version

# Test routes
php artisan route:list --path=api

# Test API
curl -I http://localhost:8000/api/v1/test
```

## 🚀 Quick Restore Script

Atau gunakan script otomatis:
```bash
php restore_terima_tabung.php
```

## 📋 Checklist Restore

- [ ] Cari file backup
- [ ] Restore bootstrap/app.php
- [ ] Restore routes/web.php
- [ ] Restore AuthController
- [ ] Clear semua cache
- [ ] Restart web server
- [ ] Test aplikasi

## 🎯 Expected Result

Setelah restore:
- ✅ Aplikasi kembali ke kondisi sebelum fix
- ✅ Tidak ada server error
- ✅ API endpoints berfungsi normal
- ✅ Mungkin masih ada error "Route [login] not defined" (kondisi awal)

## 🔍 Troubleshooting

### Jika restore gagal:

#### 1. Check file permissions
```bash
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

#### 2. Check Laravel logs
```bash
tail -f storage/logs/laravel.log
```

#### 3. Check web server logs
```bash
# Nginx
tail -f /var/log/nginx/error.log

# Apache
tail -f /var/log/apache2/error.log
```

#### 4. Verify file integrity
```bash
# Check if files are readable
cat bootstrap/app.php | head -10
cat routes/web.php | head -10
```

## ⚠️ Important Notes

1. **Backup files are safe** - Script tidak menghapus backup
2. **Multiple backups** - Setiap kali script dijalankan, backup baru dibuat
3. **Choose latest backup** - Gunakan backup terbaru untuk hasil terbaik
4. **Test after restore** - Selalu test aplikasi setelah restore

## 🆘 Emergency Commands

Jika restore manual gagal:
```bash
# Force restore dengan timestamp spesifik
cp bootstrap/app.php.backup.2025-08-28-10-57-44 bootstrap/app.php
cp routes/web.php.backup.2025-08-28-10-57-44 routes/web.php
cp app/Http/Controllers/Api/AuthController.php.backup.2025-08-28-10-57-44 app/Http/Controllers/Api/AuthController.php

# Clear cache dan restart
php artisan cache:clear && php artisan config:clear
sudo systemctl restart nginx
```

## 🧪 Testing After Restore

### Test 1: Basic Functionality
```bash
# Test Laravel
php artisan --version

# Test route list
php artisan route:list --path=api
```

### Test 2: API Endpoints
```bash
# Test public endpoint
curl -I http://localhost:8000/api/v1/test

# Test terima-tabung (mungkin masih error)
curl -X POST http://localhost:8000/api/v1/mobile/terima-tabung \
  -H "Content-Type: application/json" \
  -d '{"test":"data"}'
```

### Test 3: Web Interface
```bash
# Test via browser
http://localhost:8000/
http://localhost:8000/restore-test.php
```

## 📊 Status After Restore

Setelah restore berhasil:
- ✅ **Laravel berfungsi** normal
- ✅ **Web server** berjalan
- ✅ **File structure** kembali ke kondisi awal
- ⚠️ **Mungkin masih ada** error "Route [login] not defined"
- ⚠️ **API endpoints** mungkin masih bermasalah

**Ini adalah kondisi normal sebelum fix, jadi tidak perlu khawatir jika masih ada error yang sama.**

## 🔄 Next Steps

Setelah restore berhasil, Anda bisa:

1. **Analisis error** - Cari tahu apa yang menyebabkan error
2. **Fix bertahap** - Perbaiki satu per satu masalah
3. **Test setiap perubahan** - Pastikan tidak ada error baru
4. **Backup sebelum fix** - Selalu backup sebelum melakukan perubahan

## 📝 Notes

- Script restore akan mengembalikan semua file ke kondisi sebelum fix
- Backup files tidak akan dihapus, jadi aman untuk restore
- Jika masih ada error setelah restore, berarti masalah ada di tempat lain
- Selalu test aplikasi setelah restore untuk memastikan berfungsi normal
