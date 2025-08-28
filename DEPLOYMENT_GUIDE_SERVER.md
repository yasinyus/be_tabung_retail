# Panduan Deployment API ke Server

## Status: ✅ READY FOR DEPLOYMENT

API endpoint `api/v1/mobile/terima-tabung` sudah siap untuk di-deploy ke server.

## Prerequisites

### Server Requirements
- **PHP:** 8.1 atau lebih tinggi
- **Composer:** Latest version
- **MySQL/MariaDB:** 5.7 atau lebih tinggi
- **Web Server:** Apache/Nginx
- **SSL Certificate:** Untuk HTTPS (recommended)

### PHP Extensions
```bash
php-bcmath
php-curl
php-dom
php-fileinfo
php-gd
php-json
php-mbstring
php-mysql
php-opcache
php-pdo
php-tokenizer
php-xml
php-zip
```

## Deployment Steps

### 1. Upload Code ke Server

#### Option A: Git Clone (Recommended)
```bash
# Di server
cd /var/www/html/
git clone https://github.com/your-repo/BE_MOBILE.git
cd BE_MOBILE
```

#### Option B: Upload via FTP/SFTP
- Upload semua file ke direktori server
- Pastikan struktur folder tetap sama

### 2. Install Dependencies
```bash
# Install Composer dependencies
composer install --optimize-autoloader --no-dev

# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 3. Environment Configuration

#### Copy Environment File
```bash
cp .env.example .env
```

#### Configure .env File
```env
APP_NAME="PT GAS API"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Database Setup

#### Create Database
```sql
CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'your_database_user'@'localhost' IDENTIFIED BY 'your_database_password';
GRANT ALL PRIVILEGES ON your_database_name.* TO 'your_database_user'@'localhost';
FLUSH PRIVILEGES;
```

#### Run Migrations
```bash
php artisan migrate --force
```

#### Seed Database (Optional)
```bash
php artisan db:seed --force
```

### 6. Create Admin User
```bash
php artisan user:create-test
```

### 7. Web Server Configuration

#### Apache Configuration (.htaccess)
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

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/BE_MOBILE/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 8. Security Configuration

#### Set Proper Permissions
```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/html/BE_MOBILE

# Set directory permissions
sudo find /var/www/html/BE_MOBILE -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/html/BE_MOBILE -type f -exec chmod 644 {} \;

# Set special permissions for storage and cache
sudo chmod -R 775 /var/www/html/BE_MOBILE/storage
sudo chmod -R 775 /var/www/html/BE_MOBILE/bootstrap/cache
```

#### Configure Firewall
```bash
# Allow HTTP and HTTPS
sudo ufw allow 80
sudo ufw allow 443

# Allow SSH (if needed)
sudo ufw allow 22
```

### 9. SSL Certificate (HTTPS)

#### Using Let's Encrypt
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache

# Get SSL certificate
sudo certbot --apache -d your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 10. Testing Deployment

#### Test API Endpoints
```bash
# Test basic connectivity
curl -I https://your-domain.com/api/v1/test

# Test login
curl -X POST https://your-domain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Test terima-tabung (with token)
curl -X POST https://your-domain.com/api/v1/mobile/terima-tabung \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "lokasi_qr": "GDG-001",
    "armada_qr": "ARM-001",
    "tabung_qr": ["T-001", "T-002"],
    "keterangan": "Test deployment"
  }'
```

## Production Optimizations

### 1. Cache Configuration
```bash
# Cache routes
php artisan route:cache

# Cache config
php artisan config:cache

# Cache views
php artisan view:cache
```

### 2. Queue Configuration (Optional)
```bash
# Install Supervisor
sudo apt install supervisor

# Configure queue worker
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/BE_MOBILE/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/html/BE_MOBILE/storage/logs/worker.log
stopwaitsecs=3600
```

### 3. Monitoring Setup
```bash
# Install monitoring tools
sudo apt install htop iotop nethogs

# Set up log rotation
sudo nano /etc/logrotate.d/laravel
```

```conf
/var/www/html/BE_MOBILE/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    notifempty
    create 644 www-data www-data
}
```

## Troubleshooting

### Common Issues

#### 1. Permission Denied
```bash
sudo chown -R www-data:www-data /var/www/html/BE_MOBILE
sudo chmod -R 755 /var/www/html/BE_MOBILE
```

#### 2. Database Connection Error
- Check database credentials in `.env`
- Ensure database server is running
- Verify network connectivity

#### 3. 500 Internal Server Error
```bash
# Check Laravel logs
tail -f /var/www/html/BE_MOBILE/storage/logs/laravel.log

# Check web server logs
sudo tail -f /var/log/apache2/error.log
# or
sudo tail -f /var/log/nginx/error.log
```

#### 4. API Not Responding
```bash
# Test basic connectivity
curl -I https://your-domain.com

# Test API endpoint
curl -I https://your-domain.com/api/v1/test
```

## Maintenance

### Regular Tasks
```bash
# Clear old logs
php artisan log:clear

# Clear cache (if needed)
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Update application
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Backup Strategy
```bash
# Database backup
mysqldump -u your_user -p your_database > backup_$(date +%Y%m%d_%H%M%S).sql

# Code backup
tar -czf code_backup_$(date +%Y%m%d_%H%M%S).tar.gz /var/www/html/BE_MOBILE
```

## API Documentation for Mobile App

### Base URL
```
https://your-domain.com/api/v1
```

### Authentication Endpoint
```
POST /auth/login
```

### Terima Tabung Endpoint
```
POST /mobile/terima-tabung
Authorization: Bearer {token}
```

### Test Endpoint
```
GET /test
```

## Support

Jika mengalami masalah, periksa:
1. Laravel logs: `/var/www/html/BE_MOBILE/storage/logs/laravel.log`
2. Web server logs: `/var/log/apache2/error.log` atau `/var/log/nginx/error.log`
3. System logs: `journalctl -u apache2` atau `journalctl -u nginx`

## Summary

Setelah mengikuti panduan ini, API Anda akan:
- ✅ Berjalan di production server
- ✅ Menggunakan HTTPS
- ✅ Memiliki security yang proper
- ✅ Siap untuk mobile app integration
- ✅ Memiliki monitoring dan backup
