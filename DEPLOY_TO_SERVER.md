# 🚀 SERVER 403 FIX - DEPLOYMENT GUIDE

## STATUS
- ✅ **LOCAL:** Working perfectly
- ❌ **SERVER:** 403 Forbidden  

## ROOT CAUSE
Configuration files on server are not synced with working local files.

## SOLUTION: SYNC WORKING CONFIG

### 📁 FILES TO UPLOAD TO SERVER

Upload these exact files from local to server:

1. **`app/Providers/Filament/AdminPanelProvider.php`**
2. **`app/Models/User.php`**
3. **`app/Filament/Widgets/StatsOverview.php`**

### 🛠️ COMMANDS TO RUN ON SERVER

```bash
# 1. Clear all caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear

# 2. Regenerate autoloader  
composer dump-autoload --optimize

# 3. Fix permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 4. Restart web server
sudo systemctl restart apache2
# or
sudo systemctl restart nginx

# 5. Verify routes
php artisan route:list | grep admin
```

### 🔍 VERIFICATION STEPS

1. **Check routes:** `php artisan route:list | grep admin`
   - Should show multiple admin routes

2. **Test emergency admin:** `http://8.215.70.68/emergency.php`
   - Should still work (Laravel confirmation)

3. **Test main admin:** `http://8.215.70.68/admin`
   - Should redirect to login page

4. **Check logs:** `tail -f storage/logs/laravel.log`

### ⚠️ IF STILL 403 AFTER SYNC

The issue is server-level configuration:

1. **Web Server Configuration:**
   ```bash
   # Check Apache virtual host
   sudo nano /etc/apache2/sites-available/your-site.conf
   
   # Ensure AllowOverride All
   <Directory /var/www/be_tabung_retail/public>
       AllowOverride All
       Require all granted
   </Directory>
   ```

2. **File Ownership:**
   ```bash
   sudo chown -R www-data:www-data /var/www/be_tabung_retail
   ```

3. **SELinux (if enabled):**
   ```bash
   sudo setsebool -P httpd_can_network_connect 1
   sudo setsebool -P httpd_unified 1
   ```

4. **Apache Modules:**
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

### 🎯 PRIORITY ACTIONS

1. **Upload working files** ⭐⭐⭐
2. **Clear cache** ⭐⭐⭐  
3. **Fix permissions** ⭐⭐
4. **Restart web server** ⭐⭐
5. **Check server logs** ⭐

---

## 📞 IMMEDIATE HELP

If you need immediate assistance with any step, the priority is:

1. **Upload the 3 working files** to replace server versions
2. **Run cache clear commands**
3. **Test admin URL**

The local config is proven to work - we just need to get it onto the server!
