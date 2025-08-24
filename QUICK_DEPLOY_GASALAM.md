# 🎯 QUICK DEPLOYMENT STEPS untuk test.gasalamsolusi.my.id

## ⚡ Ringkasan Langkah Deploy (30 menit)

### 1️⃣ **Preparation (5 menit)**
```powershell
# Local - jalankan di folder project
.\prepare_production.ps1
```
✅ Files ready: `laravel_app.zip`, `public_html.zip`, `database_backup.sql`

### 2️⃣ **Database Setup (5 menit)**
1. **cPanel** → **MySQL Databases**
2. **Create database:** `gass1498_tabung` (sesuaikan prefix)
3. **Create user:** `gass1498_user` dengan password kuat
4. **Assign user** ke database (ALL PRIVILEGES)
5. **phpMyAdmin** → Import `database_backup.sql`

### 3️⃣ **File Upload (10 menit)**
1. **Upload** `laravel_app.zip` → `/laravel_app/` → Extract
2. **Upload** `public_html.zip` → `/public_html/` → Extract
3. **Verify** struktur folder benar

### 4️⃣ **Quick Fix (5 menit)**
1. **Database:** https://test.gasalamsolusi.my.id/database_connection_fix.php
   - Login: `admin` / `deploy123`
   - Update DB credentials sesuai step 2
   - Test connection sampai berhasil

2. **Deploy:** https://test.gasalamsolusi.my.id/deploy.php
   - Login: `admin` / `deploy123`
   - Run deployment

### 5️⃣ **Test & Cleanup (5 menit)**
1. **Test access:**
   - Homepage: https://test.gasalamsolusi.my.id ✅
   - Admin: https://test.gasalamsolusi.my.id/admin ✅
   - Login: `admin@tabungretail.com` / `admin123`

2. **Cleanup:**
   - Delete semua `.php` deployment scripts
   - Verify security

---

## 🔧 Database Credentials (Update di step 4)

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=gass1498_tabung      # ← Sesuaikan nama DB Anda
DB_USERNAME=gass1498_user        # ← Sesuaikan username DB Anda  
DB_PASSWORD=YOUR_ACTUAL_PASSWORD # ← Password dari step 2
```

---

## 📋 Test Accounts

```
🔑 Admin Utama:
Email: admin@tabungretail.com
Password: admin123

🔑 Kepala Gudang:
Email: kepala_gudang@tabungretail.com  
Password: admin123

🔑 Operator:
Email: operator@tabungretail.com
Password: admin123
```

---

## 🚨 Jika Ada Error

### Error Database Connection:
→ **Fix:** https://test.gasalamsolusi.my.id/database_connection_fix.php

### Error Cache Table:
→ **Fix:** https://test.gasalamsolusi.my.id/fix_deployment.php

### Error Migration:
→ **Fix:** https://test.gasalamsolusi.my.id/migration_fix.php

### Error 500:
→ **Check:** cPanel Error Logs, file permissions

---

## ✅ Success Indicators

- ✅ Homepage loads without error
- ✅ Admin panel accessible dengan login
- ✅ CRUD operations working (User, Tabung, Armada, etc)
- ✅ QR codes generating
- ✅ API responding: https://test.gasalamsolusi.my.id/api/test

---

## 📞 Support Files

Semua panduan lengkap tersedia di:
- `DEPLOYMENT_GUIDE_GASALAM.md` - Panduan detail
- `CACHE_ERROR_SOLUTION.md` - Solusi error umum
- `DEPLOYMENT_FINAL_CHECKLIST.md` - Checklist lengkap

**🎉 Total waktu deployment: ~30 menit**
