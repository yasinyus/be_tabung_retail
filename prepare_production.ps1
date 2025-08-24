# Laravel Production Preparation Script for Windows
# Run this script in PowerShell before uploading to shared hosting

Write-Host "🚀 Preparing Laravel for Production Deployment" -ForegroundColor Green
Write-Host "=============================================="

# Create production directory
Write-Host "📁 Creating production directory..." -ForegroundColor Yellow
New-Item -ItemType Directory -Force -Path "production_files"
New-Item -ItemType Directory -Force -Path "production_files\laravel_app"
New-Item -ItemType Directory -Force -Path "production_files\public_html"

# Clear all caches first
Write-Host "🧹 Clearing development caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Install production dependencies
Write-Host "📦 Installing production dependencies..." -ForegroundColor Yellow
composer install --optimize-autoloader --no-dev --no-interaction

# Copy Laravel application files (excluding public)
Write-Host "📋 Copying Laravel application files..." -ForegroundColor Yellow
$excludePaths = @("node_modules", ".git", "production_files", "public", "storage\logs\*", "storage\framework\cache\*", "storage\framework\sessions\*", "storage\framework\views\*")

Get-ChildItem -Path "." -Recurse | Where-Object {
    $relativePath = $_.FullName.Substring((Get-Location).Path.Length + 1)
    $shouldExclude = $false
    foreach ($exclude in $excludePaths) {
        if ($relativePath -like "*$exclude*") {
            $shouldExclude = $true
            break
        }
    }
    return -not $shouldExclude
} | Copy-Item -Destination { 
    $relativePath = $_.FullName.Substring((Get-Location).Path.Length + 1)
    Join-Path "production_files\laravel_app" $relativePath 
} -Force

# Copy public files separately
Write-Host "📋 Copying public files..." -ForegroundColor Yellow
Copy-Item -Path "public\*" -Destination "production_files\public_html" -Recurse -Force

# Copy production htaccess
Write-Host "📋 Setting up production .htaccess..." -ForegroundColor Yellow
Copy-Item -Path ".htaccess.production" -Destination "production_files\public_html\.htaccess" -Force

# Generate APP_KEY
$appKey = php artisan key:generate --show

# Create production .env
Write-Host "📋 Creating production .env template..." -ForegroundColor Yellow
$envContent = @"
APP_NAME="Tabung Retail"
APP_ENV=production
APP_KEY=base64:$appKey
APP_DEBUG=false
APP_URL=https://test.gasalamsolusi.my.id

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database Configuration (UPDATE THESE!)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=gass1498_tabung
DB_USERNAME=gass1498_user
DB_PASSWORD=YOUR_DATABASE_PASSWORD

BROADCAST_DRIVER=log
CACHE_DRIVER=file
CACHE_STORE=file
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
MAIL_FROM_NAME="`${APP_NAME}"

# API Configuration
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com
SESSION_DOMAIN=.yourdomain.com
"@

Set-Content -Path "production_files\laravel_app\.env.production" -Value $envContent

# Update index.php for production path
Write-Host "📋 Updating index.php for production paths..." -ForegroundColor Yellow
$indexContent = @'
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
'@

Set-Content -Path "production_files\public_html\index.php" -Value $indexContent

# Create database export
Write-Host "🗄️ Exporting database..." -ForegroundColor Yellow
try {
    php artisan schema:dump --prune
    mysqldump -u root -p --databases tabung_retail > production_files\database_backup.sql
} catch {
    Write-Host "⚠️ Database export failed - export manually" -ForegroundColor Red
}

# Create storage directories
Write-Host "📁 Setting up storage structure..." -ForegroundColor Yellow
New-Item -ItemType Directory -Force -Path "production_files\laravel_app\storage\logs"
New-Item -ItemType Directory -Force -Path "production_files\laravel_app\storage\framework\cache"
New-Item -ItemType Directory -Force -Path "production_files\laravel_app\storage\framework\sessions"
New-Item -ItemType Directory -Force -Path "production_files\laravel_app\storage\framework\views"
New-Item -ItemType Directory -Force -Path "production_files\laravel_app\storage\app\public"

# Copy deployment script
Write-Host "📋 Adding deployment scripts..." -ForegroundColor Yellow
Copy-Item -Path "deploy.php" -Destination "production_files\laravel_app\deploy.php" -Force
Copy-Item -Path "fix_deployment.php" -Destination "production_files\laravel_app\fix_deployment.php" -Force
Copy-Item -Path "migration_fix.php" -Destination "production_files\laravel_app\migration_fix.php" -Force
Copy-Item -Path "database_connection_fix.php" -Destination "production_files\laravel_app\database_connection_fix.php" -Force

# Copy documentation files
Write-Host "📋 Adding documentation..." -ForegroundColor Yellow
Copy-Item -Path "DEPLOYMENT_GUIDE.md" -Destination "production_files\laravel_app\DEPLOYMENT_GUIDE.md" -Force
Copy-Item -Path "DEPLOYMENT_GUIDE_GASALAM.md" -Destination "production_files\laravel_app\DEPLOYMENT_GUIDE_GASALAM.md" -Force
Copy-Item -Path "QUICK_DEPLOY_GASALAM.md" -Destination "production_files\laravel_app\QUICK_DEPLOY_GASALAM.md" -Force
Copy-Item -Path "LIVE_API_TESTING_GUIDE.md" -Destination "production_files\laravel_app\LIVE_API_TESTING_GUIDE.md" -Force
Copy-Item -Path "API_CHANGES_NO_ROLE.md" -Destination "production_files\laravel_app\API_CHANGES_NO_ROLE.md" -Force
Copy-Item -Path "CACHE_ERROR_SOLUTION.md" -Destination "production_files\laravel_app\CACHE_ERROR_SOLUTION.md" -Force
Copy-Item -Path "DEPLOYMENT_FINAL_CHECKLIST.md" -Destination "production_files\laravel_app\DEPLOYMENT_FINAL_CHECKLIST.md" -Force
Copy-Item -Path "api-documentation.md" -Destination "production_files\laravel_app\api-documentation.md" -Force

# Copy API testing files
Write-Host "📋 Adding API testing files..." -ForegroundColor Yellow
Copy-Item -Path "api-tests-live.http" -Destination "production_files\laravel_app\api-tests-live.http" -Force
Copy-Item -Path "test-api-live.sh" -Destination "production_files\laravel_app\test-api-live.sh" -Force
Copy-Item -Path "test-api-live.ps1" -Destination "production_files\laravel_app\test-api-live.ps1" -Force
Copy-Item -Path "Tabung_Retail_API_Live.postman_collection.json" -Destination "production_files\laravel_app\Tabung_Retail_API_Live.postman_collection.json" -Force

# Create upload instructions
Write-Host "📋 Creating upload instructions..." -ForegroundColor Yellow
$instructionsContent = @'
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

### 5. Run Deployment Fix (if needed):
- If you get cache table errors, run first: https://yourdomain.com/fix_deployment.php
- Then run: https://yourdomain.com/deploy.php

### 6. Main Deployment:
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
'@

Set-Content -Path "production_files\UPLOAD_INSTRUCTIONS.md" -Value $instructionsContent

# Create ZIP files for easy upload
Write-Host "📦 Creating ZIP files for upload..." -ForegroundColor Yellow
if (Get-Command Compress-Archive -ErrorAction SilentlyContinue) {
    Compress-Archive -Path "production_files\laravel_app\*" -DestinationPath "production_files\laravel_app.zip" -Force
    Compress-Archive -Path "production_files\public_html\*" -DestinationPath "production_files\public_html.zip" -Force
} else {
    Write-Host "⚠️ Compress-Archive not available. Create ZIP files manually." -ForegroundColor Red
}

# Final summary
Write-Host ""
Write-Host "✅ Production files prepared successfully!" -ForegroundColor Green
Write-Host "==========================================" 
Write-Host "📁 Files created in: production_files\"
Write-Host "📦 Ready to upload:"
Write-Host "   - laravel_app.zip (upload to server, extract to laravel_app/)"
Write-Host "   - public_html.zip (upload to server, extract to public_html/)"
Write-Host "   - database_backup.sql (import to your hosting database)"
Write-Host "   - UPLOAD_INSTRUCTIONS.md (follow these steps)"
Write-Host ""
Write-Host "🔧 Next steps:"
Write-Host "   1. Upload files to your shared hosting"
Write-Host "   2. Update .env with your hosting database credentials"
Write-Host "   3. Run deploy.php via browser"
Write-Host "   4. Test your application"
Write-Host ""
Write-Host "⚠️  Important:" -ForegroundColor Yellow
Write-Host "   - Update all 'yourdomain.com' references with your actual domain"
Write-Host "   - Change deploy.php authentication credentials"
Write-Host "   - Delete deploy.php after deployment"
Write-Host "   - Set up SSL certificate"
Write-Host ""
