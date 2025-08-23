# 📋 Deployment Checklist - Tabung Retail

## 🎯 Pre-Deployment Checklist

### Local Development
- [ ] All features tested and working
- [ ] Database migrations completed
- [ ] Seeds/sample data created
- [ ] QR code generation working
- [ ] File uploads working
- [ ] API endpoints tested
- [ ] Admin panel accessible
- [ ] No debug code left in production
- [ ] Error handling implemented
- [ ] Security vulnerabilities checked

### Project Preparation
- [ ] Run `prepare_production.ps1` script
- [ ] Production files created in `production_files/` directory
- [ ] Database exported to `database_backup.sql`
- [ ] `.env.production` template created
- [ ] Public files separated from Laravel app files
- [ ] Composer dependencies optimized for production

## 🏠 Hosting Setup Checklist

### Hosting Requirements
- [ ] PHP 8.2+ available
- [ ] MySQL database available
- [ ] Composer support (or manual vendor upload)
- [ ] SSH access (recommended)
- [ ] File Manager / cPanel access
- [ ] Domain/subdomain configured

### Popular Hosting Providers
- [ ] **Hostinger** - Good Laravel support, affordable
- [ ] **Niagahoster** - Local Indonesian provider
- [ ] **DomainRacer** - Good performance
- [ ] **A2 Hosting** - Fast SSD hosting
- [ ] **SiteGround** - Managed WordPress/Laravel hosting

## 📁 File Upload Checklist

### Directory Structure
- [ ] `/home/username/laravel_app/` - Laravel application files
- [ ] `/home/username/public_html/` - Public web files (or subdomain folder)
- [ ] Correct path references in `public_html/index.php`

### File Permissions
- [ ] `755` permissions for directories
- [ ] `644` permissions for files
- [ ] `775` permissions for `storage/` directory
- [ ] `775` permissions for `bootstrap/cache/` directory
- [ ] `600` permissions for `.env` file

### File Uploads
- [ ] `laravel_app.zip` uploaded and extracted
- [ ] `public_html.zip` uploaded and extracted
- [ ] `vendor/` folder uploaded (if not using Composer on server)
- [ ] `.env.production` renamed to `.env`
- [ ] File ownership set correctly

## 🗄️ Database Setup Checklist

### Database Creation
- [ ] MySQL database created via cPanel
- [ ] Database user created
- [ ] User assigned to database with ALL PRIVILEGES
- [ ] Database connection tested

### Database Import
- [ ] `database_backup.sql` imported successfully
- [ ] All tables created
- [ ] Sample data imported
- [ ] Database size reasonable for hosting plan

### Database Configuration
- [ ] `DB_HOST` updated in `.env`
- [ ] `DB_DATABASE` updated in `.env`
- [ ] `DB_USERNAME` updated in `.env`
- [ ] `DB_PASSWORD` updated in `.env`
- [ ] Connection tested

## ⚙️ Configuration Checklist

### Environment Configuration
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` updated to actual domain
- [ ] `APP_KEY` generated and set
- [ ] Database credentials configured
- [ ] Mail configuration updated
- [ ] Queue driver set to `database`
- [ ] Cache driver appropriate for hosting
- [ ] Session configuration updated

### Security Configuration
- [ ] Strong `APP_KEY` generated
- [ ] Secure database password
- [ ] SANCTUM domains configured
- [ ] Session domain configured
- [ ] HTTPS enforced (if SSL available)

## 🚀 Deployment Process Checklist

### Initial Deployment
- [ ] Access `https://yourdomain.com/deploy.php`
- [ ] Authentication credentials working (admin/deploy123)
- [ ] All artisan commands executed successfully
- [ ] Database connection tested
- [ ] Migrations run successfully
- [ ] Storage symlink created
- [ ] Caches generated

### Post-Deployment
- [ ] `deploy.php` deleted for security
- [ ] Homepage loads correctly
- [ ] Admin panel accessible (`/admin`)
- [ ] API endpoints working (`/api/test`)
- [ ] File uploads working
- [ ] QR code generation working
- [ ] Email sending working (if configured)

## 🔗 Storage & Assets Checklist

### Storage Symlink
- [ ] Storage symlink created via SSH: `ln -s ../laravel_app/storage/app/public storage`
- [ ] OR manual copy: `laravel_app/storage/app/public/` → `public_html/storage/`
- [ ] Storage directory accessible via web
- [ ] File uploads saving correctly

### Asset Compilation
- [ ] CSS files accessible
- [ ] JavaScript files accessible
- [ ] Images loading correctly
- [ ] Fonts loading correctly
- [ ] Filament admin assets working

## 🔒 Security Checklist

### File Security
- [ ] `.htaccess` files in place
- [ ] Laravel app directory not web-accessible
- [ ] `.env` file not web-accessible
- [ ] Sensitive files denied via `.htaccess`
- [ ] Directory browsing disabled

### Application Security
- [ ] CSRF protection enabled
- [ ] XSS protection headers set
- [ ] SQL injection protection (Eloquent ORM)
- [ ] File upload restrictions in place
- [ ] API rate limiting configured

## 🌐 SSL & Domain Checklist

### SSL Certificate
- [ ] SSL certificate installed
- [ ] HTTPS redirect enabled
- [ ] Mixed content issues resolved
- [ ] Security headers configured

### Domain Configuration
- [ ] Domain pointing to correct directory
- [ ] WWW vs non-WWW redirect configured
- [ ] Subdomain configuration (if applicable)
- [ ] DNS records propagated

## 🧪 Testing Checklist

### Functionality Testing
- [ ] **Homepage**: `https://yourdomain.com` loads
- [ ] **Admin Panel**: `https://yourdomain.com/admin` accessible
- [ ] **API Test**: `https://yourdomain.com/api/test` responds
- [ ] **Login**: Admin login working
- [ ] **CRUD Operations**: Create, read, update, delete working
- [ ] **File Uploads**: Image/file uploads working
- [ ] **QR Codes**: QR code generation and display working

### Performance Testing
- [ ] Page load times acceptable
- [ ] Database queries optimized
- [ ] Caching working correctly
- [ ] Images optimized
- [ ] Gzip compression enabled

### API Testing
- [ ] Authentication endpoints working
- [ ] Role-based access control working
- [ ] Mobile API endpoints responding
- [ ] Error handling working correctly
- [ ] Rate limiting in place

## 📊 Monitoring & Maintenance Checklist

### Error Monitoring
- [ ] Error logging configured
- [ ] Log rotation set up
- [ ] Error notification system (optional)
- [ ] Application monitoring (optional)

### Backup Strategy
- [ ] Database backup schedule
- [ ] File backup schedule
- [ ] Backup storage location
- [ ] Backup restoration tested

### Maintenance
- [ ] Update schedule planned
- [ ] Security patch process
- [ ] Performance monitoring
- [ ] Disk space monitoring

## 🆘 Troubleshooting Checklist

### Common Issues
- [ ] **500 Error**: Check error logs, file permissions, .env configuration
- [ ] **Database Connection**: Verify credentials, test connection
- [ ] **Missing Assets**: Check public file upload, storage symlink
- [ ] **Admin Panel 404**: Verify route caching, URL configuration
- [ ] **API Errors**: Check authentication, middleware, route caching

### Debug Steps
- [ ] Check hosting error logs
- [ ] Verify file permissions recursively
- [ ] Test database connection separately
- [ ] Clear all caches via deploy script
- [ ] Verify .env configuration
- [ ] Check Laravel logs in `storage/logs/`

## ✅ Post-Launch Checklist

### Immediate Actions
- [ ] Change default admin password
- [ ] Update deploy script credentials (before deletion)
- [ ] Set up regular backups
- [ ] Monitor for errors in first 24 hours
- [ ] Test all critical functionality

### Long-term Setup
- [ ] SSL certificate auto-renewal
- [ ] Monitoring and alerting
- [ ] Performance optimization
- [ ] SEO configuration
- [ ] Analytics setup (if applicable)

---

## 📞 Support Resources

- **Laravel Documentation**: https://laravel.com/docs/11.x/deployment
- **Filament Documentation**: https://filamentphp.com/docs
- **Hosting Provider Support**: Contact your hosting provider
- **Community Support**: Laravel community forums

---

**✅ Deployment Complete!**

Once all items are checked, your Laravel Tabung Retail application should be successfully deployed and running on shared hosting!
