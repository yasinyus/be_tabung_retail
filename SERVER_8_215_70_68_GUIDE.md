# Server 8.215.70.68 - PT GAS API Fix Guide

## 🌐 Server Status
- **IP:** 8.215.70.68
- **Status:** Laravel running (menampilkan "Let's get started")
- **Issue:** Route [login] not defined pada API endpoint

## 🔧 Solusi untuk Server 8.215.70.68

### Langkah 1: SSH ke Server
```bash
ssh root@8.215.70.68
cd /var/www/html  # atau path Laravel project Anda
```

### Langkah 2: Jalankan Script Fix
```bash
# Upload dan jalankan script khusus server
php fix_server_8_215_70_68.php
```

### Langkah 3: Test Server
```bash
# Test via browser
http://8.215.70.68/server-test.php

# Test API endpoints
curl -I http://8.215.70.68/api/v1/test
curl -X POST http://8.215.70.68/api/v1/mobile/terima-tabung \
  -H "Content-Type: application/json" \
  -d '{"test":"data"}'
```

## 🧪 Testing Commands

### Test 1: Basic Laravel
```bash
# Test Laravel version
php artisan --version

# Test route list
php artisan route:list --path=api
```

### Test 2: API Endpoints
```bash
# Test public endpoint
curl -I http://8.215.70.68/api/v1/test

# Test auth endpoint
curl -X POST http://8.215.70.68/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Test terima-tabung (tanpa auth - harus return 401)
curl -X POST http://8.215.70.68/api/v1/mobile/terima-tabung \
  -H "Content-Type: application/json" \
  -d '{"test":"data"}'
```

### Test 3: Web Server
```bash
# Test web server status
sudo systemctl status nginx
sudo systemctl status php8.2-fpm

# Check web server logs
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log
```

## 🔍 Troubleshooting

### Jika masih error "Route [login] not defined":

#### 1. Check bootstrap/app.php
```bash
# Cari semua instance route('login')
grep -n "route('login')" bootstrap/app.php

# Jika ada, ganti manual
nano bootstrap/app.php
# Ganti: route('login') -> '/admin/login'
```

#### 2. Check web.php
```bash
# Pastikan route login ada
grep -n "Route::get('/login'" routes/web.php

# Jika tidak ada, tambah manual
nano routes/web.php
# Tambah di akhir:
# Route::get('/login', function () { return redirect('/admin/login'); })->name('login');
```

#### 3. Clear Cache & Restart
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

## 📊 Expected Results

### Setelah Fix Berhasil:
- ✅ **http://8.215.70.68/** - Menampilkan aplikasi PT GAS
- ✅ **http://8.215.70.68/server-test.php** - Test page berfungsi
- ✅ **http://8.215.70.68/api/v1/test** - Return JSON response
- ✅ **http://8.215.70.68/api/v1/mobile/terima-tabung** - Return 401 (no auth)

### Test Response Examples:

#### API Test Endpoint:
```json
{
  "status": "success",
  "message": "API V1 is working!",
  "timestamp": "2024-01-15 10:30:00",
  "endpoints": [...]
}
```

#### Terima Tabung (tanpa auth):
```json
{
  "status": "error",
  "message": "Unauthorized"
}
```

## 🚨 Emergency Commands

### Jika script gagal:
```bash
# Restore backup
ls -la *.backup.*
cp bootstrap/app.php.backup.TIMESTAMP bootstrap/app.php

# Manual fix
nano bootstrap/app.php
# Hapus semua withExceptions blocks

# Clear cache dan restart
php artisan cache:clear
sudo systemctl restart nginx
```

### Check Laravel Logs:
```bash
tail -f storage/logs/laravel.log
```

### Check File Permissions:
```bash
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data .
```

## 📋 Checklist Server 8.215.70.68

- [ ] SSH ke server
- [ ] Jalankan `php fix_server_8_215_70_68.php`
- [ ] Test `http://8.215.70.68/server-test.php`
- [ ] Test `http://8.215.70.68/api/v1/test`
- [ ] Test `http://8.215.70.68/api/v1/mobile/terima-tabung`
- [ ] Restart web server jika diperlukan
- [ ] Check logs jika masih error

## 🎯 Success Criteria

Server 8.215.70.68 dianggap berhasil jika:
1. **Tidak ada error** "Route [login] not defined"
2. **API endpoints** berfungsi normal
3. **Terima-tabung endpoint** return 401 untuk request tanpa auth
4. **Web interface** dapat diakses normal
