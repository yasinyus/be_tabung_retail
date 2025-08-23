#!/bin/bash

# Laravel Production Preparation Script
# Run this script before uploading to shared hosting

echo "🚀 Preparing Laravel for Production Deployment"
echo "=============================================="

# Create production directory
echo "📁 Creating production directory..."
mkdir -p production_files
mkdir -p production_files/laravel_app
mkdir -p production_files/public_html

# Clear all caches first
echo "🧹 Clearing development caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Install production dependencies
echo "📦 Installing production dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# Copy Laravel application files (excluding public)
echo "📋 Copying Laravel application files..."
rsync -av --exclude='node_modules' --exclude='.git' --exclude='production_files' --exclude='public' --exclude='storage/logs/*' --exclude='storage/framework/cache/*' --exclude='storage/framework/sessions/*' --exclude='storage/framework/views/*' . production_files/laravel_app/

# Copy public files separately
echo "📋 Copying public files..."
cp -r public/* production_files/public_html/

# Copy production htaccess
echo "📋 Setting up production .htaccess..."
cp .htaccess.production production_files/public_html/.htaccess

# Create production .env
echo "📋 Creating production .env template..."
cat > production_files/laravel_app/.env.production << EOF
APP_NAME="Tabung Retail"
APP_ENV=production
APP_KEY=base64:$(php artisan key:generate --show)
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database Configuration (UPDATE THESE!)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Mail Configuration (UPDATE THESE!)
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="\${APP_NAME}"

# API Configuration
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com
SESSION_DOMAIN=.yourdomain.com
EOF

# Update index.php for production path
echo "📋 Updating index.php for production paths..."
cat > production_files/public_html/index.php << 'EOF'
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Adjust paths for shared hosting structure
// Assuming Laravel app is in ../laravel_app/ relative to public_html
if (file_exists($maintenance = __DIR__.'/../laravel_app/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../laravel_app/vendor/autoload.php';

$app = require_once __DIR__.'/../laravel_app/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
EOF

# Create database export
echo "🗄️ Exporting database..."
php artisan schema:dump --prune
mysqldump -u root -p --databases tabung_retail > production_files/database_backup.sql 2>/dev/null || echo "⚠️ Database export failed - export manually"

# Create storage directories with proper permissions
echo "📁 Setting up storage structure..."
mkdir -p production_files/laravel_app/storage/logs
mkdir -p production_files/laravel_app/storage/framework/cache
mkdir -p production_files/laravel_app/storage/framework/sessions
mkdir -p production_files/laravel_app/storage/framework/views
mkdir -p production_files/laravel_app/storage/app/public
chmod -R 775 production_files/laravel_app/storage
chmod -R 775 production_files/laravel_app/bootstrap/cache

# Copy deployment script
echo "📋 Adding deployment script..."
cp deploy.php production_files/laravel_app/

# Create upload instructions
echo "📋 Creating upload instructions..."
cat > production_files/UPLOAD_INSTRUCTIONS.md << 'EOF'
# 📁 Upload Instructions for Shared Hosting

## Directory Structure on Hosting:
```
/home/yourusername/
├── public_html/               # Upload contents of public_html/ here
│   ├── index.php
│   ├── .htaccess  
│   ├── css/
│   ├── js/
│   └── storage/ (symlink)
├── laravel_app/               # Upload contents of laravel_app/ here
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env (rename from .env.production)
│   ├── artisan
│   ├── composer.json
│   └── deploy.php
```

## Upload Steps:

### 1. Upload Laravel Application:
- Upload `laravel_app/` contents to `/home/yourusername/laravel_app/`
- Set permissions: 755 for directories, 644 for files
- Set permissions: 775 for `storage/` and `bootstrap/cache/`

### 2. Upload Public Files:
- Upload `public_html/` contents to `/home/yourusername/public_html/`

### 3. Database Setup:
- Create MySQL database in cPanel
- Import `database_backup.sql`
- Update database credentials in `.env`

### 4. Configuration:
- Rename `.env.production` to `.env`
- Update all configuration values marked with "UPDATE THESE!"
- Set APP_URL to your actual domain

### 5. Run Deployment:
- Access: https://yourdomain.com/deploy.php
- Username: admin
- Password: deploy123
- Follow deployment steps
- DELETE deploy.php after completion

### 6. Create Storage Symlink:
Via SSH: `cd public_html && ln -s ../laravel_app/storage/app/public storage`
Or manually copy files from `laravel_app/storage/app/public/` to `public_html/storage/`

### 7. Test Application:
- Homepage: https://yourdomain.com
- Admin: https://yourdomain.com/admin  
- API: https://yourdomain.com/api/test
EOF

# Create ZIP files for easy upload
echo "📦 Creating ZIP files for upload..."
cd production_files
zip -r laravel_app.zip laravel_app/
zip -r public_html.zip public_html/
cd ..

# Final summary
echo ""
echo "✅ Production files prepared successfully!"
echo "===========================================" 
echo "📁 Files created in: production_files/"
echo "📦 Ready to upload:"
echo "   - laravel_app.zip (upload to server, extract to laravel_app/)"
echo "   - public_html.zip (upload to server, extract to public_html/)"
echo "   - database_backup.sql (import to your hosting database)"
echo "   - UPLOAD_INSTRUCTIONS.md (follow these steps)"
echo ""
echo "🔧 Next steps:"
echo "   1. Upload files to your shared hosting"
echo "   2. Update .env with your hosting database credentials"
echo "   3. Run deploy.php via browser"
echo "   4. Test your application"
echo ""
echo "⚠️  Important:"
echo "   - Update all 'yourdomain.com' references with your actual domain"
echo "   - Change deploy.php authentication credentials"
echo "   - Delete deploy.php after deployment"
echo "   - Set up SSL certificate"
echo ""
