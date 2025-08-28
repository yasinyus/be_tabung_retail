# Manual Restore Backup

## 🔄 Mengembalikan ke Versi Sebelumnya

Jika `fix_server_ultimate.php` menyebabkan server error, ikuti langkah-langkah ini untuk mengembalikan ke versi sebelumnya.

## 📦 Langkah 1: Cari File Backup

Script `fix_server_ultimate.php` membuat backup dengan format:
```
bootstrap/app.php.backup.YYYY-MM-DD-HH-MM-SS
.env.backup.YYYY-MM-DD-HH-MM-SS
routes/web.php.backup.YYYY-MM-DD-HH-MM-SS
routes/api.php.backup.YYYY-MM-DD-HH-MM-SS
```

```bash
# Cari file backup
ls -la *.backup.*
ls -la bootstrap/*.backup.*
ls -la routes/*.backup.*
```

## 🔄 Langkah 2: Restore Manual

### Restore bootstrap/app.php
```bash
# Cari backup terbaru
ls -t bootstrap/app.php.backup.* | head -1

# Restore (ganti TIMESTAMP dengan timestamp yang sesuai)
cp bootstrap/app.php.backup.TIMESTAMP bootstrap/app.php
```

### Restore .env
```bash
# Cari backup terbaru
ls -t .env.backup.* | head -1

# Restore
cp .env.backup.TIMESTAMP .env
```

### Restore routes/web.php
```bash
# Cari backup terbaru
ls -t routes/web.php.backup.* | head -1

# Restore
cp routes/web.php.backup.TIMESTAMP routes/web.php
```

### Restore routes/api.php
```bash
# Cari backup terbaru
ls -t routes/api.php.backup.* | head -1

# Restore
cp routes/api.php.backup.TIMESTAMP routes/api.php
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
curl -I http://your-domain.com/api/v1/test
```

## 🚀 Quick Restore Script

Atau gunakan script otomatis:
```bash
php restore_backup.php
```

## 📋 Checklist Restore

- [ ] Cari file backup
- [ ] Restore bootstrap/app.php
- [ ] Restore .env
- [ ] Restore routes/web.php
- [ ] Restore routes/api.php
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
cp bootstrap/app.php.backup.2024-01-15-10-30-00 bootstrap/app.php
cp .env.backup.2024-01-15-10-30-00 .env
cp routes/web.php.backup.2024-01-15-10-30-00 routes/web.php
cp routes/api.php.backup.2024-01-15-10-30-00 routes/api.php

# Clear cache dan restart
php artisan cache:clear && php artisan config:clear
sudo systemctl restart nginx
```
