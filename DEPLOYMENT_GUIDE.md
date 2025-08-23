# 🚀 Panduan Deployment ke Shared Hosting

## 📋 Prerequisites

### Shared Hosting Requirements:
- ✅ PHP 8.2+ 
- ✅ MySQL Database
- ✅ Composer Support (atau manual upload vendor)
- ✅ SSH Access (optional, tapi recommended)
- ✅ File Manager / cPanel
- ✅ Subdomain/Domain support

### Popular Shared Hosting yang Support Laravel:
- **Hostinger** (recommended)
- **Niagahoster** 
- **DomainRacer**
- **A2 Hosting**
- **SiteGround**

## 🔧 Step 1: Persiapan Project Local

### 1.1 Update Environment untuk Production
```bash
# Copy .env untuk production
cp .env .env.production
```

Edit `.env.production`:
```env
APP_NAME="Tabung Retail"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database (akan diisi sesuai hosting)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# Queue (gunakan database untuk shared hosting)
QUEUE_CONNECTION=database
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Mail (sesuaikan dengan SMTP hosting)
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Disable unnecessary services for shared hosting
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error
```

### 1.2 Optimize untuk Production
```bash
# Clear semua cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Generate production optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update composer untuk production
composer install --optimize-autoloader --no-dev
```

### 1.3 Database Migration File
```bash
# Export database structure dan data
php artisan schema:dump --prune
mysqldump -u root -p tabung_retail > database_backup.sql
```

## 🗂️ Step 2: Persiapan File untuk Upload

### 2.1 Struktur File yang akan diupload:
```
📁 Production Files
├── 📁 app/                 # Upload ke folder private
├── 📁 bootstrap/           # Upload ke folder private  
├── 📁 config/              # Upload ke folder private
├── 📁 database/            # Upload ke folder private
├── 📁 resources/           # Upload ke folder private
├── 📁 routes/              # Upload ke folder private
├── 📁 storage/             # Upload ke folder private (set permissions)
├── 📁 vendor/              # Upload atau install via composer
├── 📁 public/              # Upload ke public_html/domain folder
├── .env                    # Upload ke folder private (edit sesuai hosting)
├── artisan                 # Upload ke folder private
├── composer.json           # Upload ke folder private
└── composer.lock           # Upload ke folder private
```

### 2.2 Update public/index.php
Edit `public/index.php` untuk menyesuaikan path:
```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Sesuaikan path ini dengan struktur hosting
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Path ke autoload - sesuaikan dengan struktur hosting
require __DIR__.'/../vendor/autoload.php';

// Path ke bootstrap - sesuaikan dengan struktur hosting  
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

## 🌐 Step 3: Setup di Shared Hosting

### 3.1 Struktur Folder di Hosting:
```
📁 Home Directory (biasanya /home/username/)
├── 📁 public_html/         # Folder web utama
│   ├── 📁 yourdomain.com/  # Atau langsung di public_html
│   │   ├── index.php       # Dari folder public Laravel
│   │   ├── .htaccess       # Dari folder public Laravel
│   │   ├── 📁 css/         # Assets dari public Laravel
│   │   ├── 📁 js/          # Assets dari public Laravel
│   │   └── 📁 storage/     # Symlink ke storage/app/public
├── 📁 laravel_app/         # Folder private untuk Laravel
│   ├── 📁 app/
│   ├── 📁 bootstrap/
│   ├── 📁 config/
│   ├── 📁 database/
│   ├── 📁 resources/
│   ├── 📁 routes/
│   ├── 📁 storage/
│   ├── 📁 vendor/
│   ├── .env
│   ├── artisan
│   └── composer.json
```

### 3.2 Upload Files:

#### Via File Manager (cPanel):
1. **Login ke cPanel**
2. **Buka File Manager**
3. **Upload laravel_app.zip** ke home directory
4. **Extract** di folder `laravel_app/`
5. **Upload public files** ke `public_html/yourdomain.com/`

#### Via FTP/SFTP:
```bash
# Upload dengan FileZilla atau WinSCP
# Struktur sama seperti di atas
```

### 3.3 Set File Permissions:
```bash
# Via SSH atau File Manager
chmod -R 755 laravel_app/
chmod -R 775 laravel_app/storage/
chmod -R 775 laravel_app/bootstrap/cache/
chmod 644 laravel_app/.env
```

## 🗄️ Step 4: Setup Database

### 4.1 Buat Database di cPanel:
1. **Login cPanel** → **MySQL Databases**
2. **Create Database**: `username_tabung_retail`
3. **Create User**: `username_tabung_user`
4. **Set Password** dan **Add User to Database**
5. **Grant ALL PRIVILEGES**

### 4.2 Import Database:
```bash
# Via phpMyAdmin atau command line
mysql -u username_tabung_user -p username_tabung_retail < database_backup.sql
```

### 4.3 Update .env file:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_tabung_retail
DB_USERNAME=username_tabung_user
DB_PASSWORD=your_db_password
```

## ⚙️ Step 5: Final Configuration

### 5.1 Update index.php paths:
Edit `public_html/yourdomain.com/index.php`:
```php
require __DIR__.'/../../laravel_app/vendor/autoload.php';
$app = require_once __DIR__.'/../../laravel_app/bootstrap/app.php';
```

### 5.2 Create Storage Symlink:
Via SSH:
```bash
cd /home/username/public_html/yourdomain.com/
ln -s ../../laravel_app/storage/app/public storage
```

Via File Manager (jika no SSH):
1. Buat folder `storage` di public_html
2. Copy isi `laravel_app/storage/app/public/` ke `public_html/storage/`

### 5.3 Setup Cron Jobs (untuk Queue):
Di cPanel → Cron Jobs:
```bash
# Every minute
* * * * * cd /home/username/laravel_app && php artisan schedule:run >> /dev/null 2>&1
```

### 5.4 Install/Update Composer Dependencies:
Via SSH:
```bash
cd /home/username/laravel_app/
composer install --optimize-autoloader --no-dev
```

Via Manual (jika no SSH):
- Download vendor folder dari local
- Upload via FTP

## 🔧 Step 6: Laravel Optimization

### 6.1 Run Artisan Commands via SSH:
```bash
cd /home/username/laravel_app/

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Generate app key (jika belum)
php artisan key:generate

# Cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link
```

### 6.2 Via Manual (jika no SSH):
Buat file `deploy.php` di laravel_app:
```php
<?php
// deploy.php - Run via browser: yourdomain.com/deploy.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Starting deployment...\n";

// Run artisan commands
$commands = [
    'config:clear',
    'cache:clear', 
    'route:clear',
    'view:clear',
    'config:cache',
    'route:cache',
    'view:cache',
    'migrate --force'
];

foreach ($commands as $command) {
    echo "Running: php artisan $command\n";
    $kernel->call($command);
    echo "✅ Done\n";
}

echo "Deployment completed!\n";
// Delete this file after use for security
unlink(__FILE__);
?>
```

Access via: `https://yourdomain.com/deploy.php`

## 🔒 Step 7: Security & .htaccess

### 7.1 Create .htaccess di public_html:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to Laravel public folder
    RewriteCond %{REQUEST_URI} !^/yourdomain.com/
    RewriteRule ^(.*)$ /yourdomain.com/$1 [L]
</IfModule>
```

### 7.2 Secure Laravel folder:
Create `.htaccess` di `laravel_app/`:
```apache
# Deny access to Laravel app folder
<Files "*">
    Order Deny,Allow
    Deny from all
</Files>
```

## ✅ Step 8: Testing & Verification

### 8.1 Test Checklist:
- ✅ **Homepage loads**: `https://yourdomain.com`
- ✅ **Admin panel**: `https://yourdomain.com/admin`
- ✅ **API endpoints**: `https://yourdomain.com/api/test`
- ✅ **Database connection** working
- ✅ **File uploads** working
- ✅ **QR codes** generating
- ✅ **Email** sending (if configured)

### 8.2 Common Issues & Solutions:

#### Issue: 500 Internal Server Error
```bash
# Check error logs
tail -f /home/username/logs/error_log

# Common fixes:
- Check file permissions (755/775)
- Verify .env database credentials
- Ensure vendor folder uploaded correctly
- Check PHP version compatibility
```

#### Issue: APP_KEY not set
```bash
# Generate new key
php artisan key:generate
# Or manual: base64:RANDOM_32_CHARS
```

#### Issue: Storage not accessible
```bash
# Create symlink
php artisan storage:link
# Or manual copy files
```

## 🌟 Step 9: Performance Optimization

### 9.1 Enable OPcache (if available):
Add to `.htaccess`:
```apache
# Enable OPcache
php_value opcache.enable 1
php_value opcache.memory_consumption 128
php_value opcache.max_accelerated_files 4000
```

### 9.2 Enable Gzip Compression:
```apache
# Add to .htaccess in public folder
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```

## 📞 Step 10: Post-Deployment

### 10.1 Setup Monitoring:
- **Uptime monitoring** (UptimeRobot)
- **Error tracking** (Sentry, Bugsnag)
- **Performance monitoring** (New Relic)

### 10.2 Backup Strategy:
```bash
# Daily database backup script
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# Weekly file backup
tar -czf files_backup_$(date +%Y%m%d).tar.gz laravel_app/
```

### 10.3 SSL Certificate:
- **Let's Encrypt** (free, via cPanel)
- **Cloudflare** (free SSL proxy)
- **Hosting provider SSL**

## 🎯 Domain & Subdomain Setup

### For Main Domain:
- Upload public files to `/public_html/`
- Laravel app in `/laravel_app/`

### For Subdomain (api.yourdomain.com):
- Create subdomain in cPanel
- Upload public files to `/public_html/api/`
- Point to same Laravel app

### For Testing (staging.yourdomain.com):
- Create subdomain
- Separate Laravel installation
- Different database for testing

## 🚀 Production Checklist

- ✅ **Environment**: APP_ENV=production
- ✅ **Debug**: APP_DEBUG=false  
- ✅ **HTTPS**: SSL certificate installed
- ✅ **Database**: Production database configured
- ✅ **Storage**: Symlink created
- ✅ **Permissions**: Correct file permissions set
- ✅ **Cache**: Production cache enabled
- ✅ **Backups**: Backup strategy implemented
- ✅ **Monitoring**: Error tracking setup
- ✅ **Security**: .htaccess protection enabled

**🎉 Your Laravel application is now live on shared hosting!**

## 📚 Additional Resources

- [Laravel Deployment Guide](https://laravel.com/docs/11.x/deployment)
- [Shared Hosting Laravel Tutorial](https://laravel.com/docs/11.x/deployment#shared-hosting)
- [cPanel Laravel Setup](https://docs.cpanel.net/knowledge-base/web-services/how-to-install-laravel-on-cpanel/)

## 🆘 Support

If you encounter issues:
1. Check hosting provider documentation
2. Contact hosting support
3. Check Laravel community forums
4. Review error logs for specific issues
