# 🚀 QR Code Live Server Deployment Script

Write-Host "🔧 QR Code Live Server Fix Script" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan

# Configuration
$LIVE_URL = "https://test.gasalamsolusi.my.id"
$SSH_HOST = "your-server-host"  # Ganti dengan host server
$SSH_USER = "your-username"     # Ganti dengan username

Write-Host ""
Write-Host "📋 Langkah-langkah untuk di Live Server:" -ForegroundColor Yellow
Write-Host ""

Write-Host "1️⃣  Upload Files Job yang Diperbaiki" -ForegroundColor Green
Write-Host "   - Upload app/Jobs/GenerateTabungQrCode.php"
Write-Host "   - Upload app/Jobs/GenerateArmadaQrCode.php"
Write-Host "   - Upload app/Jobs/GenerateGudangQrCode.php"
Write-Host "   - Upload app/Jobs/GeneratePelangganQrCode.php"
Write-Host ""

Write-Host "2️⃣  SSH ke Server dan Jalankan Commands:" -ForegroundColor Green
Write-Host "   ssh $SSH_USER@$SSH_HOST" -ForegroundColor Gray
Write-Host "   cd /path/to/your/laravel/project" -ForegroundColor Gray
Write-Host ""

Write-Host "3️⃣  Buat Storage Symbolic Link:" -ForegroundColor Green
Write-Host "   php artisan storage:link" -ForegroundColor Gray
Write-Host ""

Write-Host "4️⃣  Set Permission Storage:" -ForegroundColor Green
Write-Host "   chmod -R 755 storage/" -ForegroundColor Gray
Write-Host "   chmod -R 755 public/storage/" -ForegroundColor Gray
Write-Host ""

Write-Host "5️⃣  Clear Cache:" -ForegroundColor Green
Write-Host "   php artisan config:cache" -ForegroundColor Gray
Write-Host "   php artisan route:cache" -ForegroundColor Gray
Write-Host "   php artisan view:cache" -ForegroundColor Gray
Write-Host ""

Write-Host "6️⃣  Generate QR Codes (NEW COMMAND):" -ForegroundColor Green
Write-Host "   php artisan qr:fix-all" -ForegroundColor Gray
Write-Host "   # This will generate ALL QR codes at once"
Write-Host "   # Alternative individual commands:"
Write-Host "   # php artisan tabung:fix-qr" -ForegroundColor DarkGray
Write-Host "   # php artisan armada:fix-qr" -ForegroundColor DarkGray
Write-Host "   # php artisan gudang:fix-qr" -ForegroundColor DarkGray
Write-Host "   # php artisan pelanggan:fix-qr" -ForegroundColor DarkGray
Write-Host ""

Write-Host "7️⃣  Test QR Code Generation:" -ForegroundColor Green
Write-Host "   php artisan tabung:test-qr 1" -ForegroundColor Gray
Write-Host "   php artisan gudang:test-qr 1" -ForegroundColor Gray
Write-Host ""

Write-Host "8️⃣  Verify Storage Link:" -ForegroundColor Green
Write-Host "   ls -la public/storage" -ForegroundColor Gray
Write-Host "   ls -la storage/app/public/qr_codes/" -ForegroundColor Gray
Write-Host ""

# Test QR Code endpoint
Write-Host "9️⃣  Test QR Code Endpoint via API:" -ForegroundColor Green

$headers = @{
    'Content-Type' = 'application/json'
}

# Test login first
Write-Host "   Testing login..." -ForegroundColor Blue
$loginBody = @{
    email = "driver@gmail.com"
    password = "password"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$LIVE_URL/api/v1/auth/login" -Method Post -Body $loginBody -ContentType "application/json"
    $token = $loginResponse.token
    
    if ($token) {
        Write-Host "   ✅ Login successful" -ForegroundColor Green
        
        # Test QR scan endpoint
        Write-Host "   Testing QR scan endpoint..." -ForegroundColor Blue
        $scanHeaders = @{
            'Authorization' = "Bearer $token"
            'Content-Type' = 'application/json'
        }
        
        $scanBody = @{
            type = "tabung"
            id = 1
        } | ConvertTo-Json
        
        try {
            $scanResponse = Invoke-RestMethod -Uri "$LIVE_URL/api/v1/scan-qr" -Method Post -Body $scanBody -Headers $scanHeaders
            Write-Host "   ✅ QR scan endpoint working" -ForegroundColor Green
            Write-Host "   Response: $($scanResponse | ConvertTo-Json -Compress)" -ForegroundColor Gray
        } catch {
            Write-Host "   ❌ QR scan endpoint failed: $($_.Exception.Message)" -ForegroundColor Red
        }
        
    } else {
        Write-Host "   ❌ Login failed" -ForegroundColor Red
    }
} catch {
    Write-Host "   ❌ Cannot connect to live server: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "   💡 Server mungkin belum aktif atau URL salah" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "🔟 Manual Verification:" -ForegroundColor Green
Write-Host "   Buka browser dan cek:"
Write-Host "   - $LIVE_URL/storage/qr_codes/tabung/tabung_9.svg" -ForegroundColor Gray
Write-Host "   - $LIVE_URL/storage/qr_codes/gudang/gudang_1.svg" -ForegroundColor Gray
Write-Host "   - $LIVE_URL/storage/qr_codes/pelanggan/pelanggan_1.svg" -ForegroundColor Gray
Write-Host "   📱 QR codes now use SVG format (better compatibility)" -ForegroundColor Blue
Write-Host ""

Write-Host "📝 Environment Check:" -ForegroundColor Yellow
Write-Host "   Pastikan di .env live server:"
Write-Host "   APP_URL=$LIVE_URL" -ForegroundColor Gray
Write-Host "   QUEUE_CONNECTION=sync atau database" -ForegroundColor Gray
Write-Host ""

Write-Host "🆘 Troubleshooting:" -ForegroundColor Red
Write-Host "   Jika QR code masih tidak muncul:"
Write-Host "   1. Cek error log: tail -f storage/logs/laravel.log"
Write-Host "   2. Cek permission: ls -la storage/app/public/"
Write-Host "   3. Cek symbolic link: ls -la public/storage"
Write-Host "   4. Test manual generation:"
Write-Host "      php artisan tinker"
Write-Host "      \$tabung = App\Models\Tabung::first();"
Write-Host "      \$job = new App\Jobs\GenerateTabungQrCode(\$tabung);"
Write-Host "      \$job->handle();"
Write-Host ""

Write-Host "✅ Script completed! Follow the steps above for live server deployment." -ForegroundColor Green
Write-Host "📞 Contact: jika masih ada masalah, cek error logs dan permission" -ForegroundColor Blue
