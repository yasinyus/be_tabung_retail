# Deployment Checklist - PT GAS API

## ✅ Pre-Deployment Checklist

### Server Requirements
- [ ] PHP 8.1+ installed
- [ ] Composer installed
- [ ] MySQL/MariaDB 5.7+ installed
- [ ] Web server (Apache/Nginx) installed
- [ ] SSL certificate ready (Let's Encrypt recommended)

### Domain & DNS
- [ ] Domain name registered
- [ ] DNS A record pointing to server IP
- [ ] DNS propagation completed (check with `nslookup`)

### Database Setup
- [ ] Database created
- [ ] Database user created with proper permissions
- [ ] Database credentials documented

### Code Preparation
- [ ] Code uploaded to server
- [ ] All files present in project directory
- [ ] No development files left behind

## 🚀 Deployment Steps

### 1. Server Preparation
- [ ] Update server packages: `sudo apt update && sudo apt upgrade`
- [ ] Install required PHP extensions
- [ ] Configure firewall (allow ports 80, 443, 22)
- [ ] Set up SSH access

### 2. Code Deployment
- [ ] Upload code to `/var/www/html/BE_MOBILE/`
- [ ] Set proper permissions
- [ ] Install Composer dependencies
- [ ] Copy `.env.example` to `.env`

### 3. Environment Configuration
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure database credentials
- [ ] Set `APP_URL=https://your-domain.com`
- [ ] Generate application key

### 4. Database Setup
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Create admin user: `php artisan user:create-test`
- [ ] Verify database connection

### 5. Web Server Configuration
- [ ] Configure Nginx/Apache virtual host
- [ ] Enable site
- [ ] Test configuration
- [ ] Reload web server

### 6. SSL Certificate
- [ ] Install Certbot
- [ ] Obtain SSL certificate
- [ ] Configure auto-renewal
- [ ] Test HTTPS access

### 7. Production Optimization
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache config: `php artisan config:cache`
- [ ] Cache views: `php artisan view:cache`
- [ ] Optimize Composer autoloader

## 🧪 Testing Checklist

### API Endpoints
- [ ] Test basic connectivity: `GET /api/v1/test`
- [ ] Test login endpoint: `POST /api/v1/auth/login`
- [ ] Test terima-tabung with authentication
- [ ] Test error responses (401, 404, 500)

### Security
- [ ] HTTPS working properly
- [ ] No sensitive files accessible
- [ ] Proper file permissions set
- [ ] Firewall configured

### Performance
- [ ] API response time < 2 seconds
- [ ] Database queries optimized
- [ ] Caching working
- [ ] No memory leaks

## 📋 Post-Deployment Checklist

### Documentation
- [ ] Update API documentation with new domain
- [ ] Document database credentials securely
- [ ] Create deployment report
- [ ] Update mobile app configuration

### Monitoring
- [ ] Set up log monitoring
- [ ] Configure error reporting
- [ ] Set up uptime monitoring
- [ ] Configure backup system

### Maintenance
- [ ] Schedule regular backups
- [ ] Set up log rotation
- [ ] Plan update procedures
- [ ] Document troubleshooting steps

## 🔧 Quick Deployment Commands

### Linux/Ubuntu Server
```bash
# Make script executable
chmod +x deploy.sh

# Run deployment
./deploy.sh "api.ptgas.com" "ptgas_api" "ptgas_user" "mypassword123"
```

### Windows Server
```powershell
# Run PowerShell script
.\deploy.ps1 -Domain "api.ptgas.com" -DatabaseName "ptgas_api" -DatabaseUser "ptgas_user" -DatabasePassword "mypassword123"
```

### Manual Deployment
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

# 4. Generate key and setup database
php artisan key:generate
php artisan migrate --force
php artisan user:create-test

# 5. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🚨 Emergency Procedures

### Rollback Plan
- [ ] Keep backup of previous version
- [ ] Document rollback commands
- [ ] Test rollback procedure

### Troubleshooting
- [ ] Check Laravel logs: `tail -f storage/logs/laravel.log`
- [ ] Check web server logs
- [ ] Verify database connectivity
- [ ] Test file permissions

### Support Contacts
- [ ] Server administrator contact
- [ ] Database administrator contact
- [ ] Domain registrar contact
- [ ] SSL certificate provider contact

## 📊 Success Metrics

### Performance Targets
- [ ] API response time < 2 seconds
- [ ] 99.9% uptime
- [ ] Zero security vulnerabilities
- [ ] All endpoints responding correctly

### Monitoring Alerts
- [ ] Server down alert
- [ ] High CPU/memory usage alert
- [ ] Database connection failure alert
- [ ] SSL certificate expiration alert

## ✅ Final Verification

### Before Going Live
- [ ] All tests passing
- [ ] Mobile app configured with new API
- [ ] Team notified of deployment
- [ ] Backup system verified
- [ ] Monitoring alerts configured

### Post-Live Verification
- [ ] Monitor API usage for 24 hours
- [ ] Check error logs
- [ ] Verify all endpoints working
- [ ] Confirm mobile app integration
- [ ] Document any issues found

---

**Deployment Status:** ✅ READY  
**Last Updated:** $(date)  
**Next Review:** 30 days after deployment
