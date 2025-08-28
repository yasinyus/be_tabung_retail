# 🚀 Deployment Guide - PT GAS API

## 📋 Overview

Panduan lengkap untuk deployment API PT GAS ke server production. API ini sudah siap untuk di-deploy dan digunakan oleh aplikasi mobile.

## ✅ Status

- **API Endpoint:** `api/v1/mobile/terima-tabung` ✅ BERFUNGSI
- **Authentication:** Bearer Token ✅ BERFUNGSI  
- **Database:** MySQL/MariaDB ✅ SIAP
- **Security:** HTTPS, CORS, Rate Limiting ✅ SIAP

## 🎯 Quick Start

### Option 1: Automated Deployment (Recommended)

#### Linux/Ubuntu Server
```bash
# 1. Upload code to server
# 2. Make script executable
chmod +x deploy.sh

# 3. Run deployment
./deploy.sh "api.ptgas.com" "ptgas_api" "ptgas_user" "mypassword123"
```

#### Windows Server
```powershell
# 1. Upload code to server
# 2. Run PowerShell script as Administrator
.\deploy.ps1 -Domain "api.ptgas.com" -DatabaseName "ptgas_api" -DatabaseUser "ptgas_user" -DatabasePassword "mypassword123"
```

### Option 2: Manual Deployment

```bash
# 1. Install dependencies
composer install --optimize-autoloader --no-dev

# 2. Set permissions
sudo chown -R www-data:www-data /var/www/html/BE_MOBILE
sudo chmod -R 755 /var/www/html/BE_MOBILE
sudo chmod -R 775 /var/www/html/BE_MOBILE/storage

# 3. Configure environment
cp .env.example .env
# Edit .env file with your settings

# 4. Setup database
php artisan key:generate
php artisan migrate --force
php artisan user:create-test

# 5. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📁 File Structure

```
BE_MOBILE/
├── app/
│   ├── Http/Controllers/Api/
│   │   └── AuthController.php          # API Controller
│   └── Models/
│       ├── User.php                    # Admin User Model
│       ├── Pelanggan.php               # Customer Model
│       └── TabungActivity.php          # Activity Model
├── routes/
│   └── api.php                         # API Routes
├── database/
│   └── migrations/                     # Database Migrations
├── deploy.sh                           # Linux Deployment Script
├── deploy.ps1                          # Windows Deployment Script
├── DEPLOYMENT_GUIDE_SERVER.md          # Detailed Guide
├── DEPLOYMENT_CHECKLIST.md             # Deployment Checklist
└── API_TERIMA_TABUNG_WORKING.md        # API Documentation
```

## 🔧 Server Requirements

### Minimum Requirements
- **PHP:** 8.1+
- **MySQL:** 5.7+
- **Composer:** Latest
- **Web Server:** Apache/Nginx
- **SSL Certificate:** Required for HTTPS

### PHP Extensions
```bash
php-bcmath php-curl php-dom php-fileinfo 
php-gd php-json php-mbstring php-mysql 
php-opcache php-pdo php-tokenizer php-xml php-zip
```

## 🌐 API Endpoints

### Base URL
```
https://your-domain.com/api/v1
```

### Available Endpoints

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/test` | GET | ❌ | Test API connectivity |
| `/auth/login` | POST | ❌ | User authentication |
| `/auth/logout` | POST | ✅ | User logout |
| `/auth/profile` | GET | ✅ | Get user profile |
| `/mobile/dashboard` | GET | ✅ | Mobile dashboard |
| `/mobile/terima-tabung` | POST | ✅ | Terima tabung endpoint |

### Authentication
```bash
# Login to get token
curl -X POST https://your-domain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Use token for authenticated requests
curl -X POST https://your-domain.com/api/v1/mobile/terima-tabung \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "lokasi_qr": "GDG-001",
    "armada_qr": "ARM-001", 
    "tabung_qr": ["T-001", "T-002"],
    "keterangan": "Test terima tabung"
  }'
```

## 🗄️ Database Setup

### Create Database
```sql
CREATE DATABASE ptgas_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ptgas_user'@'localhost' IDENTIFIED BY 'mypassword123';
GRANT ALL PRIVILEGES ON ptgas_api.* TO 'ptgas_user'@'localhost';
FLUSH PRIVILEGES;
```

### Environment Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ptgas_api
DB_USERNAME=ptgas_user
DB_PASSWORD=mypassword123
```

## 🔒 Security Configuration

### SSL Certificate (Let's Encrypt)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d api.ptgas.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### File Permissions
```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/html/BE_MOBILE

# Set directory permissions
sudo find /var/www/html/BE_MOBILE -type d -exec chmod 755 {} \;

# Set file permissions  
sudo find /var/www/html/BE_MOBILE -type f -exec chmod 644 {} \;

# Set special permissions
sudo chmod -R 775 /var/www/html/BE_MOBILE/storage
sudo chmod -R 775 /var/www/html/BE_MOBILE/bootstrap/cache
```

## 🧪 Testing

### Test Users
```bash
# Admin User
Email: admin@example.com
Password: password

# Customer User  
Email: pelanggan@example.com
Password: password
```

### Test Commands
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

## 📊 Monitoring & Maintenance

### Log Monitoring
```bash
# Laravel logs
tail -f /var/www/html/BE_MOBILE/storage/logs/laravel.log

# Web server logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/apache2/error.log
```

### Backup Strategy
```bash
# Database backup
mysqldump -u ptgas_user -p ptgas_api > backup_$(date +%Y%m%d_%H%M%S).sql

# Code backup
tar -czf code_backup_$(date +%Y%m%d_%H%M%S).tar.gz /var/www/html/BE_MOBILE
```

### Performance Optimization
```bash
# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize Composer
composer install --optimize-autoloader --no-dev
```

## 🚨 Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check file permissions
sudo chown -R www-data:www-data /var/www/html/BE_MOBILE
sudo chmod -R 755 /var/www/html/BE_MOBILE
```

#### 2. Database Connection Error
```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();

# Check .env configuration
cat .env | grep DB_
```

#### 3. API Not Responding
```bash
# Test web server
curl -I https://your-domain.com

# Test API endpoint
curl -I https://your-domain.com/api/v1/test
```

#### 4. Permission Denied
```bash
# Fix permissions
sudo chown -R www-data:www-data /var/www/html/BE_MOBILE
sudo chmod -R 755 /var/www/html/BE_MOBILE
sudo chmod -R 775 /var/www/html/BE_MOBILE/storage
```

## 📞 Support

### Documentation Files
- `DEPLOYMENT_GUIDE_SERVER.md` - Panduan detail deployment
- `DEPLOYMENT_CHECKLIST.md` - Checklist deployment
- `API_TERIMA_TABUNG_WORKING.md` - Dokumentasi API

### Log Locations
- Laravel logs: `/var/www/html/BE_MOBILE/storage/logs/`
- Nginx logs: `/var/log/nginx/`
- Apache logs: `/var/log/apache2/`
- System logs: `journalctl -u nginx`

### Emergency Contacts
- Server administrator
- Database administrator  
- Domain registrar
- SSL certificate provider

## ✅ Success Criteria

### Performance Targets
- API response time < 2 seconds
- 99.9% uptime
- Zero security vulnerabilities
- All endpoints responding correctly

### Monitoring Alerts
- Server down alert
- High CPU/memory usage alert
- Database connection failure alert
- SSL certificate expiration alert

## 🎉 Deployment Complete!

Setelah deployment berhasil, API Anda akan:
- ✅ Berjalan di production server
- ✅ Menggunakan HTTPS
- ✅ Memiliki security yang proper
- ✅ Siap untuk mobile app integration
- ✅ Memiliki monitoring dan backup

**API siap untuk digunakan oleh aplikasi mobile!** 🚀
