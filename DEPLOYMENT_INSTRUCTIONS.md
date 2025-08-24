# 🚀 Live Server Deployment Instructions

## 📁 Files Created for Deployment:

1. **setup.php** - Manual system setup (storage, cache, permissions)
2. **qr-generator.php** - Generate all QR codes 
3. **test-api.php** - Test all API endpoints

## 📋 Deployment Steps:

### Step 1: Push Files to Git
```bash
git add setup.php qr-generator.php test-api.php
git commit -m "Add manual deployment scripts for live server"
git push origin main
```

### Step 2: Pull on Live Server
```bash
# SSH ke server
ssh username@test.gasalamsolusi.my.id
cd /path/to/laravel/project

# Pull latest
git pull origin main
```

### Step 3: Run Setup (via Browser)
```
https://test.gasalamsolusi.my.id/setup.php
```
**Expected output:**
- ✅ Directories created
- ✅ Storage link created
- ✅ Cache cleared
- ✅ Permissions set

### Step 4: Generate QR Codes (via Browser)
```
https://test.gasalamsolusi.my.id/qr-generator.php
```
**Expected output:**
- ✅ Laravel bootstrapped
- ✅ Generated QR for each model
- ✅ Summary with total generated

### Step 5: Test APIs (via Browser)
```
https://test.gasalamsolusi.my.id/test-api.php
```
**Expected output:**
- ✅ API test endpoint: OK
- ✅ Login successful
- ✅ QR scan successful
- ✅ Dashboard accessible
- ✅ QR code file accessible

### Step 6: Clean Up
```bash
# Remove deployment files (optional)
rm setup.php qr-generator.php test-api.php
```

## 🎯 Success Indicators:

### After setup.php:
- Storage link exists: `public/storage` → `../storage/app/public`
- Directories created: `storage/app/public/qr_codes/[tabung|armada|gudang|pelanggan]`
- Test file accessible: `https://domain.com/storage/test.txt`

### After qr-generator.php:
- QR codes generated for all models
- Database updated with qr_code paths
- Files exist: `storage/app/public/qr_codes/tabung/tabung_1.svg`

### After test-api.php:
- Login works without role parameter
- QR scan returns item data
- QR code files accessible via URL

## 🆘 Troubleshooting:

### If setup.php fails:
- Check file permissions on server
- Verify PHP can create directories
- Check web server configuration

### If qr-generator.php fails:
- Verify composer packages installed
- Check database connection
- Ensure SimpleSoftwareIO\QrCode package available

### If test-api.php fails:
- Check .env configuration (APP_URL, database)
- Verify routes are working
- Check storage link created properly

## 📱 Mobile App Integration:

After successful deployment, update mobile app endpoints:

### Universal Login (No Role Parameter):
```json
POST https://test.gasalamsolusi.my.id/api/v1/auth/login
{
  "email": "driver@gmail.com", 
  "password": "password"
}
```

### QR Code Scan:
```json
POST https://test.gasalamsolusi.my.id/api/v1/scan-qr
{
  "type": "tabung",
  "id": 1
}
```

### QR Code Display:
```
https://test.gasalamsolusi.my.id/storage/qr_codes/tabung/tabung_1.svg
```

## ✅ Final Checklist:

- [ ] Files pushed to Git
- [ ] setup.php run successfully  
- [ ] qr-generator.php completed
- [ ] test-api.php all tests pass
- [ ] QR codes accessible via URL
- [ ] API endpoints working
- [ ] Mobile app updated
- [ ] Deployment files removed

**Ready for production! 🎉**
