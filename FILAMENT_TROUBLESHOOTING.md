# Filament Troubleshooting Guide

## 🎯 Current Status

### Resources Available:
- ✅ **UserResource** - User Management
- ✅ **TabungResource** - Tabung Gas
- ✅ **ArmadaResource** - Armada Kendaraan
- ✅ **PelangganResource** - Pelanggan
- ✅ **GudangResource** - Gudang

### Expected Buttons & Actions:

#### Header Actions (Top Right):
- 🔘 **Create** button - Available on all resource lists

#### Row Actions (Each Row):
- 👁️ **View** - Blue eye icon, redirects to edit page
- ✏️ **Edit** - Orange pencil icon, redirects to edit page
- 🔄 **QR Code** - Blue QR icon, opens modal (Tabung, Armada, Pelanggan, Gudang only)
- 🗑️ **Delete** - Red trash icon with confirmation (Users only)

#### Bulk Actions:
- ☑️ **Select All** checkbox
- 🗑️ **Delete Selected** button

## 🔧 Quick Fix Commands

### If Create buttons are missing:
```bash
# Clear caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Update autoload
composer dump-autoload --optimize
```

### If QR Codes show "sedang diproses":
```bash
# Fix QR codes
php artisan qr:fix

# Create storage directory
mkdir -p storage/app/public/qr_codes
chmod 755 storage/app/public/qr_codes
php artisan storage:link
```

### If View/Edit buttons show 403 Forbidden:
```bash
# Already fixed - all authorization methods return true
# Just clear cache:
php artisan config:clear
```

### Complete fix (All issues):
```bash
bash fix_filament_complete.sh
```

## 🐛 Debugging

### Check resource status:
```bash
php debug_filament_resources.php
```

### Manual check - Create buttons should appear if:
1. ✅ `canCreate()` method returns `true` (already fixed)
2. ✅ `CreateAction::make()` exists in ListPage (already exists)
3. ✅ Correct namespace `Filament\Actions\CreateAction` (already correct)
4. ✅ Cache cleared (run cache clear commands)

### Manual check - View/Edit buttons should work if:
1. ✅ `canView()` and `canEdit()` methods return `true` (already fixed)
2. ✅ Correct URL generation with `Resource::getUrl()` (already fixed)
3. ✅ Proper namespace `Filament\Actions\Action` (already fixed)

### Manual check - QR codes should work if:
1. ✅ `getQrCodeBase64()` method returns valid base64 (already fixed)
2. ✅ SimpleSoftwareIO QR package installed (in composer.json)
3. ✅ Storage directory writable (fix with chmod commands)

## 🚀 Deploy Checklist

1. **Upload all files** to server
2. **Run fix script**: `bash fix_filament_complete.sh`
3. **Check admin panel**: Visit `/admin`
4. **Test each resource**:
   - Create button (top right)
   - View/Edit buttons (each row)
   - QR Code modals (Tabung, Armada, Pelanggan, Gudang)
   - Delete functionality (Users)

## 📞 If Issues Persist

1. Check error logs: `tail -f storage/logs/laravel.log`
2. Check web server error logs
3. Verify file permissions: `ls -la storage/`
4. Test in browser developer console for JS errors
5. Verify database connection and models

## ✅ Success Indicators

- All 5 resources visible in sidebar
- Create button appears on all list pages
- View/Edit buttons work without 403 errors
- QR Code modals show actual QR codes
- No console errors in browser
- All CRUD operations functional
