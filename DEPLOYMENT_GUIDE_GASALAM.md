# 🚀 DEPLOYMENT GUIDE - Khusus untuk test.gasalamsolusi.my.id

## ✅ Prerequisites Hosting
- **Domain:** test.gasalamsolusi.my.id
- **PHP Version:** 8.2+ (check di cPanel)
- **MySQL:** Available
- **SSL Certificate:** Installed ✅
- **File Manager:** cPanel access

---

## 📋 Step-by-Step Deployment

### 1. **Persiapan Files (Local)**
```powershell
# Jalankan di folder project Laravel
.\prepare_production.ps1
```

**Output yang dihasilkan:**
- `laravel_app.zip` - Main Laravel application
- `public_html.zip` - Public files untuk root domain
- `database_backup.sql` - Database export
- 4 script deployment untuk fix issues

### 2. **Database Setup di cPanel**

#### A. Buat Database:
1. Login ke cPanel hosting gasalamsolusi.my.id
2. Go to **MySQL Databases**
3. Buat database baru: `gass1498_tabung` (adjust sesuai prefix Anda)
4. Buat user: `gass1498_user` 
5. Set password yang kuat
6. Assign user ke database dengan **ALL PRIVILEGES**

#### B. Import Database:
1. Go to **phpMyAdmin**
2. Select database `gass1498_tabung`
3. Import file `database_backup.sql`
4. Verify semua tabel terimport (users, tabung, armada, gudang, pelanggan, dll)

### 3. **Upload Files**

#### A. Upload Laravel App:
1. **File Manager** → Go to `/laravel_app/` folder (buat jika belum ada)
2. Upload `laravel_app.zip`
3. **Extract** di dalam folder `/laravel_app/`
4. **Delete** file zip setelah extract

#### B. Upload Public Files:
1. **File Manager** → Go to `/public_html/`
2. Upload `public_html.zip`  
3. **Extract** langsung di `/public_html/`
4. **Delete** file zip setelah extract

### 4. **Configuration**

#### Update .env file:
1. Buka `laravel_app/.env`
2. Update database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=gass1498_tabung    # Sesuaikan dengan nama DB Anda
DB_USERNAME=gass1498_user      # Sesuaikan dengan username DB Anda
DB_PASSWORD=YOUR_ACTUAL_PASSWORD
```

### 5. **Fix Issues (jika ada error)**

#### A. Test akses website dulu:
- Buka: https://test.gasalamsolusi.my.id
- Jika error, lanjut ke langkah fix

#### B. Fix Database Connection (jika error login):
- Akses: https://test.gasalamsolusi.my.id/database_connection_fix.php
- Login: admin / deploy123
- Follow panduan untuk fix database credentials

#### C. Fix Cache Issues (jika error cache):
- Akses: https://test.gasalamsolusi.my.id/fix_deployment.php
- Login: admin / deploy123
- Run fix untuk create cache tables

#### D. Fix Migration Issues (jika error migration):
- Akses: https://test.gasalamsolusi.my.id/migration_fix.php
- Login: admin / deploy123
- Pilih "Mark Existing Tables as Migrated"

### 6. **Final Deployment**
- Akses: https://test.gasalamsolusi.my.id/deploy.php
- Login: admin / deploy123
- Run final deployment (optimize, cache, permissions)

### 7. **Test & Verify**

#### Test Basic Access:
- **Homepage:** https://test.gasalamsolusi.my.id
- **Admin Panel:** https://test.gasalamsolusi.my.id/admin

#### Test Login Accounts:
```
Admin Utama:
Email: admin@tabungretail.com
Password: admin123

Kepala Gudang:  
Email: kepala_gudang@tabungretail.com
Password: admin123

Operator:
Email: operator@tabungretail.com  
Password: admin123
```

#### Test API Endpoints:
```bash
# Test API authentication
curl -X POST https://test.gasalamsolusi.my.id/api/login-staff \
  -H "Content-Type: application/json" \
  -d '{"email":"kepala_gudang@tabungretail.com","password":"admin123"}'

# Test data endpoint (dengan token)
curl -X GET https://test.gasalamsolusi.my.id/api/tabung \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 8. **Security & Cleanup**

#### A. Delete Deployment Scripts:
```
❌ DELETE semua file ini setelah sukses:
- database_connection_fix.php
- fix_deployment.php  
- migration_fix.php
- deploy.php
```

#### B. Verify Security:
- Test admin panel requires login
- Test API requires authentication
- Verify no debug info exposed
- Check 404 pages work correctly

---

## 🚨 Common Issues & Solutions

### Issue 1: 500 Internal Server Error
**Solution:**
1. Check error logs di cPanel
2. Verify .htaccess file exists di public_html
3. Check file permissions (755 folders, 644 files)

### Issue 2: Database Connection Failed  
**Solution:**
1. Use database_connection_fix.php script
2. Verify credentials di cPanel MySQL section
3. Ensure database user has full privileges

### Issue 3: Admin Panel Not Found
**Solution:**
1. Verify public_html files uploaded correctly
2. Check .htaccess rules
3. Run deploy.php to set proper routing

### Issue 4: QR Codes Not Generating
**Solution:**
1. Check storage permissions (775)
2. Verify storage symlink exists
3. Run deploy.php to create symlinks

---

## 📞 Support & Monitoring

### Performance Monitoring:
- **Page Load:** Should be under 3 seconds
- **Admin Panel:** Should be responsive
- **API Response:** Should be under 1 second

### Error Monitoring:
- Check cPanel error logs regularly
- Monitor database connection issues
- Watch for memory/CPU usage

### Backup Strategy:
- **Database:** Export weekly via phpMyAdmin
- **Files:** Download critical files monthly
- **Configuration:** Keep .env backup secure

---

## ✅ Success Checklist

- [ ] Domain accessible: https://test.gasalamsolusi.my.id
- [ ] Admin panel working: https://test.gasalamsolusi.my.id/admin
- [ ] Database connected successfully
- [ ] All CRUD operations working
- [ ] QR codes generating properly
- [ ] API endpoints responding
- [ ] Authentication working
- [ ] Role permissions enforced
- [ ] Deployment scripts deleted
- [ ] Error logs clean
- [ ] Performance acceptable

**🎉 Deployment Complete!** Project Tabung Retail ready for production use.

---

### Quick Links untuk test.gasalamsolusi.my.id:
- **Homepage:** https://test.gasalamsolusi.my.id
- **Admin:** https://test.gasalamsolusi.my.id/admin  
- **API Test:** https://test.gasalamsolusi.my.id/api/test
- **Fix Scripts:** https://test.gasalamsolusi.my.id/database_connection_fix.php
