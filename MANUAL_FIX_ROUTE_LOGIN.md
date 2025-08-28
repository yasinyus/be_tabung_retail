# Manual Fix: Route [login] not defined

## 🚨 Masalah
```
Internal Server Error
Symfony\Component\Routing\Exception\RouteNotFoundException
Route [login] not defined.
POST 8.215.70.68
PHP 8.2.29 — Laravel 12.25.0
```

## 🔧 Solusi Manual

### Langkah 1: SSH ke Server
```bash
ssh root@8.215.70.68
cd /path/to/your/laravel/project
```

### Langkah 2: Backup Files
```bash
cp bootstrap/app.php bootstrap/app.php.backup.$(date +%Y%m%d_%H%M%S)
cp routes/web.php routes/web.php.backup.$(date +%Y%m%d_%H%M%S)
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
```

### Langkah 3: Fix bootstrap/app.php
```bash
# Edit bootstrap/app.php
nano bootstrap/app.php
```

**Hapus SEMUA blok withExceptions:**
```php
// HAPUS SEMUA INI:
->withExceptions(function (Exceptions $exceptions): void {
    // Handle API exceptions
    $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
        if ($request->is('api/*') || $request->wantsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.'
            ], 401);
        }
        
        return redirect()->guest('/admin/login');
    });
})
```

**Hasil akhir bootstrap/app.php harus seperti ini:**
```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
```

### Langkah 4: Tambah Login Route di web.php
```bash
# Edit routes/web.php
nano routes/web.php
```

**Tambah di akhir file (sebelum `?>` jika ada):**
```php
// Explicit login routes to prevent Route [login] not defined error
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::post('/login', function () {
    return redirect('/admin/login');
})->name('login.post');
```

### Langkah 5: Clear Semua Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Langkah 6: Rebuild Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Langkah 7: Fix Permissions
```bash
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data .
```

### Langkah 8: Restart Web Server
```bash
# Untuk Nginx
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

# Untuk Apache
sudo systemctl restart apache2
```

## 🧪 Testing

### Test 1: Route List
```bash
php artisan route:list --path=api
```

### Test 2: API Test Endpoint
```bash
curl -I http://your-domain.com/api/v1/test
```

### Test 3: Terima Tabung Endpoint
```bash
curl -X POST http://your-domain.com/api/v1/mobile/terima-tabung \
  -H "Content-Type: application/json" \
  -d '{"test":"data"}'
```

### Test 4: Emergency Test
```bash
# Buat file test
cat > emergency-test.php << 'EOF'
<?php
echo "<h1>Emergency Route Test</h1>";

try {
    require_once "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    echo "<p>✅ Laravel app loaded</p>";
    
    $router = $app->make("router");
    echo "<p>✅ Router loaded</p>";
    
    $routes = [
        "/api/v1/test",
        "/api/v1/auth/login", 
        "/api/v1/mobile/terima-tabung"
    ];
    
    foreach ($routes as $route) {
        try {
            $request = Illuminate\Http\Request::create($route, "GET");
            $response = $app->handle($request);
            echo "<p>✅ Route $route: HTTP " . $response->getStatusCode() . "</p>";
        } catch (Exception $e) {
            echo "<p>❌ Route $route: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
EOF

# Test via browser: http://your-domain.com/emergency-test.php
```

## 🔍 Troubleshooting

### Jika masih error:

#### 1. Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

#### 2. Check Web Server Logs
```bash
# Nginx
tail -f /var/log/nginx/error.log

# Apache
tail -f /var/log/apache2/error.log
```

#### 3. Check PHP Error Log
```bash
tail -f /var/log/php8.2-fpm.log
```

#### 4. Verify .env Configuration
```bash
cat .env | grep -E "(APP_|DB_|CACHE_)"
```

#### 5. Test Laravel Basic
```bash
php artisan --version
php artisan route:list
```

## 🚀 Quick Fix Commands

Jika ingin cepat, jalankan script otomatis:
```bash
# Upload fix_server_ultimate.php ke server
php fix_server_ultimate.php
```

## 📋 Checklist

- [ ] Backup files
- [ ] Remove withExceptions from bootstrap/app.php
- [ ] Add login routes to web.php
- [ ] Clear all caches
- [ ] Rebuild caches
- [ ] Fix permissions
- [ ] Restart web server
- [ ] Test API endpoints
- [ ] Check logs

## 🎯 Expected Result

Setelah fix, endpoint `api/v1/mobile/terima-tabung` seharusnya:
- ✅ Tidak lagi error "Route [login] not defined"
- ✅ Mengembalikan 401 Unauthorized (karena tidak ada token)
- ✅ Bisa diakses dengan token yang valid
