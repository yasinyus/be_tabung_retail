# 🚨 FIX 404 ERROR - /admin/login not found

## ❌ Masalah: `https://test.gasalamsolusi.my.id/admin/login` → 404

Ini terjadi karena:
1. ✅ Routing Laravel belum jalan
2. ✅ File `.env` masih config development
3. ✅ Cache masih nyimpan config lama

---

## ✅ SOLUSI STEP BY STEP:

### **STEP 1: Update .env untuk Production**
Ganti file `.env` di server dengan config ini:

```env
APP_NAME="Gas Alam Solusi"
APP_ENV=production
APP_KEY=base64:ZnhMKyFe9bSQPjrprBW6B4nSNxziPO0IPm++XoH1BRE=
APP_DEBUG=false
APP_URL=https://test.gasalamsolusi.my.id

LOG_CHANNEL=single
LOG_LEVEL=error

# DATABASE - GANTI SESUAI HOSTING ANDA
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

# PENTING: UBAH KE FILE STORAGE (BUKAN DATABASE)
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### **STEP 2: Clear ALL Cache Manual**
Hapus semua file di folder ini via File Manager:
- `bootstrap/cache/` → **hapus semua file**
- `storage/framework/cache/data/` → **hapus semua file**
- `storage/framework/views/` → **hapus semua file**
- `storage/framework/sessions/` → **hapus semua file**

### **STEP 3: Check .htaccess di Root**
Pastikan ada file `.htaccess` di root dengan isi:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### **STEP 4: Check index.php di Root**
Pastikan ada file `index.php` di root dengan isi:

```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

if (!file_exists(__DIR__.'/vendor/autoload.php')) {
    die('Error: Composer dependencies not installed. Please run: composer install');
}

require __DIR__.'/vendor/autoload.php';

try {
    $app = require_once __DIR__.'/bootstrap/app.php';
    
    $kernel = $app->make(Kernel::class);
    
    $response = $kernel->handle(
        $request = Request::capture()
    );
    
    $response->send();
    
    $kernel->terminate($request, $response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo 'Application Error: Please check configuration.';
    error_log('Laravel Error: ' . $e->getMessage());
}
```

---

## 🧪 TEST LANGKAH DEMI LANGKAH:

### 1. Test Basic PHP:
Buat file `test-basic.php`:
```php
<?php
echo "<h1>✅ PHP Works!</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";
?>
```
**Test:** `https://test.gasalamsolusi.my.id/test-basic.php`

### 2. Test Laravel Bootstrap:
Buat file `test-laravel.php`:
```php
<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
echo "<h1>✅ Laravel Bootstrap Works!</h1>";
?>
```
**Test:** `https://test.gasalamsolusi.my.id/test-laravel.php`

### 3. Test Main Site:
**Test:** `https://test.gasalamsolusi.my.id/`

### 4. Test Admin:
**Test:** `https://test.gasalamsolusi.my.id/admin`

---

## 🚨 TROUBLESHOOTING:

### Jika test-basic.php tidak jalan:
- Hosting bermasalah atau file tidak terupload

### Jika test-laravel.php error:
- Missing `vendor/autoload.php` → perlu `composer install`
- Missing `bootstrap/app.php` → file Laravel tidak lengkap

### Jika main site (/) error tapi test-laravel.php OK:
- Problem di `.htaccess` atau `index.php`

### Jika /admin tetap 404 padahal / jalan:
- Filament belum terinstall
- Routes belum ter-cache
- Database connection error

---

## 📞 BANTUAN HOSTING:

Kirim pesan ke hosting support:

> "Halo, saya butuh bantuan Laravel app saya:
> 1. Jalankan `composer install --no-dev` di root folder
> 2. Set permission: storage (755), bootstrap/cache (755)
> 3. Bantu config database yang benar (.env file)
> 4. URL admin /admin/login menunjukkan 404
> Terima kasih!"

---

## ✅ YANG HARUS BERHASIL:

1. ✅ `test-basic.php` → PHP OK
2. ✅ `test-laravel.php` → Laravel Bootstrap OK  
3. ✅ `/` → Homepage Laravel
4. ✅ `/admin` → Filament Admin Login

**Key point:** Fix `.env` config dulu, lalu clear cache manual!
