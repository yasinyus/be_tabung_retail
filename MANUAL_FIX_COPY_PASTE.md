# 🚨 SOLUSI MANUAL SUPER SEDERHANA - COPY PASTE SAJA!

## ❌ Masalah: Script tidak bisa diupload atau 404

Kalau script tidak bisa diupload, lakukan manual saja dengan langkah berikut:

---

## ✅ LANGKAH 1: Edit AppServiceProvider.php
Buka file `app/Providers/AppServiceProvider.php` via File Manager hosting dan ganti SEMUA isinya dengan:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
```

## ✅ LANGKAH 2: Hapus File Cache
Hapus file-file ini via File Manager (kalau ada):
- `bootstrap/cache/config.php`
- `bootstrap/cache/routes.php`
- `bootstrap/cache/services.php`
- `bootstrap/cache/packages.php`
- `bootstrap/cache/compiled.php`

## ✅ LANGKAH 3: Buat packages.php Baru
Buat file baru `bootstrap/cache/packages.php` dengan isi:

```php
<?php
return [
    "providers" => [],
    "eager" => [],
    "deferred" => [],
    "when" => []
];
```

## ✅ LANGKAH 4: Edit .env
Pastikan file `.env` memiliki baris ini:
```
APP_ENV=production
APP_DEBUG=false
```

## ✅ LANGKAH 5: Buat/Edit index.php di Root
Buat atau edit file `index.php` di folder root (sejajar dengan .env) dengan isi:

```php
<?php

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

if (!file_exists(__DIR__.'/vendor/autoload.php')) {
    die('Error: Composer dependencies not installed. Please contact admin.');
}

require __DIR__.'/vendor/autoload.php';

try {
    $app = require_once __DIR__.'/bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    $response->send();
    
    $kernel->terminate($request, $response);
    
} catch (Exception $e) {
    http_response_code(500);
    if (env('APP_DEBUG', false)) {
        echo 'Error: ' . $e->getMessage();
    } else {
        echo 'Application temporarily unavailable. Please try again later.';
    }
}
```

## ✅ LANGKAH 6: Buat .htaccess di Root
Buat file `.htaccess` di root dengan isi:

```
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    
    # Redirect to index.php
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 🧪 TEST SETELAH LANGKAH DI ATAS

1. **Coba akses:** `https://test.gasalamsolusi.my.id/`
2. **Coba admin:** `https://test.gasalamsolusi.my.id/admin`
3. **Coba API:** `https://test.gasalamsolusi.my.id/api/v1/auth/login`

---

## 🚨 JIKA MASIH ERROR:

### Error "vendor/autoload.php not found"
- Jalankan di terminal hosting: `composer install`
- Atau contact hosting support

### Error "Permission denied"
- Set permission folder `storage` ke 755
- Set permission folder `bootstrap/cache` ke 755

### Error Database Connection (seperti yang Anda alami)
- Edit file `.env` dan pastikan database config benar:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=username_database_anda
DB_PASSWORD=password_database_anda
```

### Error "Class not found"
- Jalankan: `composer dump-autoload`

### Cache Error (JANGAN pakai artisan cache:clear)
- Hapus manual file-file di `storage/framework/cache/data/`
- Hapus manual file-file di `storage/framework/views/`
- Hapus manual file-file di `bootstrap/cache/`

### Masih 500 Error
- Check log error di `storage/logs/laravel.log`
- Contact hosting support dengan log error

---

## 📞 BANTUAN HOSTING

Kalau masih tidak bisa, kirim pesan ke hosting support:

> "Halo, saya butuh bantuan untuk Laravel app saya. Bisa tolong:
> 1. Jalankan `composer install` di folder root
> 2. Set permission folder storage dan bootstrap/cache ke 755
> 3. Bantu setup database connection yang benar
> 4. JANGAN jalankan artisan cache:clear (ada error DB)
> Terima kasih!"

---

## ✅ SELESAI!

Dengan langkah manual di atas, aplikasi Laravel harusnya sudah bisa jalan tanpa error Pail!
