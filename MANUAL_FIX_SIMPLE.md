# Manual Fix Sederhana: Route [login] not defined

## 🚨 Masalah
```
Internal Server Error
Symfony\Component\Routing\Exception\RouteNotFoundException
Route [login] not defined.
POST 8.215.70.68
PHP 8.2.29 — Laravel 12.25.0
```

## 🔧 Solusi Sederhana & Aman

### Langkah 1: SSH ke Server
```bash
ssh root@8.215.70.68
cd /path/to/your/laravel/project
```

### Langkah 2: Backup File
```bash
cp bootstrap/app.php bootstrap/app.php.backup.$(date +%Y%m%d_%H%M%S)
```

### Langkah 3: Edit bootstrap/app.php
```bash
nano bootstrap/app.php
```

**Cari dan ganti baris ini:**
```php
// DARI:
return redirect()->guest(route('login'));

// KE:
return redirect()->guest('/admin/login');
```

**Atau jika ada multiple instances, ganti semua:**
```php
// DARI:
route('login')

// KE:
'/admin/login'
```

### Langkah 4: Tambah Route Login di web.php
```bash
nano routes/web.php
```

**Tambah di akhir file:**
```php
// Simple login route to prevent Route [login] not defined error
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');
```

### Langkah 5: Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Langkah 6: Test
```bash
# Test route list
php artisan route:list --path=api

# Test API endpoint
curl -X POST http://your-domain.com/api/v1/mobile/terima-tabung \
  -H "Content-Type: application/json" \
  -d '{"test":"data"}'
```

## 🚀 Quick Fix Script

Atau gunakan script otomatis:
```bash
php fix_route_login_simple.php
```

## 🧪 Testing

### Test 1: Route Generation
```bash
# Test route login
php artisan tinker
>>> route('login')
```

### Test 2: API Endpoint
```bash
# Test tanpa auth (harus return 401)
curl -X POST http://your-domain.com/api/v1/mobile/terima-tabung \
  -H "Content-Type: application/json" \
  -d '{"test":"data"}'
```

### Test 3: Simple Test File
```bash
# Buat file test
cat > simple-test.php << 'EOF'
<?php
echo "<h1>Simple Route Test</h1>";

try {
    require_once "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    echo "<p>✅ Laravel app loaded</p>";
    
    // Test route generation
    try {
        $url = route("login");
        echo "<p>✅ Route login generated: $url</p>";
    } catch (Exception $e) {
        echo "<p>❌ Route login failed: " . $e->getMessage() . "</p>";
    }
    
    // Test API route
    try {
        $request = Illuminate\Http\Request::create("/api/v1/mobile/terima-tabung", "POST");
        $response = $app->handle($request);
        echo "<p>✅ API route working: HTTP " . $response->getStatusCode() . "</p>";
    } catch (Exception $e) {
        echo "<p>❌ API route failed: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
EOF

# Test via browser: http://your-domain.com/simple-test.php
```

## 📋 Checklist

- [ ] Backup bootstrap/app.php
- [ ] Ganti `route('login')` dengan `'/admin/login'` di bootstrap/app.php
- [ ] Tambah route login di web.php
- [ ] Clear cache
- [ ] Test route generation
- [ ] Test API endpoint

## 🎯 Expected Result

Setelah fix:
- ✅ **Tidak ada error** "Route [login] not defined"
- ✅ **API endpoint** `api/v1/mobile/terima-tabung` berfungsi
- ✅ **Return 401 Unauthorized** untuk request tanpa token
- ✅ **Route login** redirect ke `/admin/login`

## 🔍 Troubleshooting

### Jika masih error:

#### 1. Check bootstrap/app.php
```bash
# Cari semua instance route('login')
grep -n "route('login')" bootstrap/app.php
```

#### 2. Check web.php
```bash
# Pastikan route login ada
grep -n "Route::get('/login'" routes/web.php
```

#### 3. Check Laravel logs
```bash
tail -f storage/logs/laravel.log
```

#### 4. Restart web server
```bash
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

## ⚠️ Important Notes

1. **Minimal changes** - Hanya mengubah yang diperlukan
2. **Safe approach** - Tidak menghapus struktur aplikasi
3. **Backup first** - Selalu backup sebelum edit
4. **Test thoroughly** - Test semua endpoint setelah fix

## 🆘 Emergency Commands

Jika fix gagal:
```bash
# Restore backup
cp bootstrap/app.php.backup.TIMESTAMP bootstrap/app.php

# Clear cache dan restart
php artisan cache:clear
sudo systemctl restart nginx
```
