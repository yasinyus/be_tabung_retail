# =====================================
# TEST API TERIMA TABUNG ARMADA
# =====================================

$base_url = "http://192.168.1.7:8000/api/v1"

Write-Host "=== TEST API TERIMA TABUNG ARMADA ===" -ForegroundColor Green
Write-Host ""

# 1. Login dulu untuk mendapatkan token
Write-Host "1. Login untuk mendapatkan token..." -ForegroundColor Yellow

$loginData = @{
    email = "admin@example.com"
    password = "password"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$base_url/auth/login" -Method POST -Body $loginData -ContentType "application/json"
    
    if ($loginResponse.status -eq "success") {
        $token = $loginResponse.token
        Write-Host "Login berhasil!" -ForegroundColor Green
        Write-Host "User: $($loginResponse.user.name)" -ForegroundColor Cyan
        Write-Host "Token: $($token.Substring(0,20))..." -ForegroundColor Cyan
        Write-Host ""
    } else {
        Write-Host "Login gagal!" -ForegroundColor Red
        Write-Host "Response: $($loginResponse | ConvertTo-Json -Depth 3)" -ForegroundColor Red
        exit
    }
} catch {
    Write-Host "Error saat login: $($_.Exception.Message)" -ForegroundColor Red
    exit
}

# 2. Test API Terima Tabung
Write-Host "2. Testing API Terima Tabung Armada..." -ForegroundColor Yellow

$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
    "Accept" = "application/json"
}

$tabungData = @{
    lokasi_qr = "GDG-001"
    armada_qr = "ARM-001"
    tabung_qr = @("TBG-001", "TBG-002", "TBG-003")
    keterangan = "Test penerimaan tabung dari script PowerShell"
} | ConvertTo-Json

try {
    $tabungResponse = Invoke-RestMethod -Uri "$base_url/mobile/terima-tabung" -Method POST -Body $tabungData -Headers $headers
    
    if ($tabungResponse.status -eq "success") {
        Write-Host "Terima Tabung berhasil!" -ForegroundColor Green
        Write-Host ""
        Write-Host "=== DETAIL TRANSAKSI ===" -ForegroundColor Cyan
        Write-Host "Transaksi ID: $($tabungResponse.data.transaksi_id)" -ForegroundColor White
        Write-Host "Tanggal: $($tabungResponse.data.tanggal)" -ForegroundColor White
        Write-Host "Lokasi QR: $($tabungResponse.data.lokasi_qr)" -ForegroundColor White
        Write-Host "Armada QR: $($tabungResponse.data.armada_qr)" -ForegroundColor White
        Write-Host "Total Tabung: $($tabungResponse.data.total_tabung)" -ForegroundColor White
        Write-Host "Nama User: $($tabungResponse.data.nama_user)" -ForegroundColor White
        Write-Host "Status: $($tabungResponse.data.status_transaksi)" -ForegroundColor White
        Write-Host "Keterangan: $($tabungResponse.data.keterangan)" -ForegroundColor White
        
        Write-Host ""
        Write-Host "=== NOTIFIKASI ===" -ForegroundColor Cyan
        Write-Host "Title: $($tabungResponse.notification.title)" -ForegroundColor White
        Write-Host "Message: $($tabungResponse.notification.message)" -ForegroundColor White
        Write-Host "Type: $($tabungResponse.notification.type)" -ForegroundColor White
        
    } else {
        Write-Host "Terima Tabung gagal!" -ForegroundColor Red
        Write-Host "Response: $($tabungResponse | ConvertTo-Json -Depth 3)" -ForegroundColor Red
    }
} catch {
    Write-Host "Error saat terima tabung: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== TESTING SELESAI ===" -ForegroundColor Green

Write-Host ""
Write-Host "=== PANDUAN TESTING DI POSTMAN ===" -ForegroundColor Magenta
Write-Host ""
Write-Host "1. Login Endpoint:" -ForegroundColor Yellow
Write-Host "   URL: POST $base_url/auth/login"
Write-Host "   Body (JSON):"
Write-Host "   {"
Write-Host '     "email": "admin@example.com",'
Write-Host '     "password": "password"'
Write-Host "   }"
Write-Host ""
Write-Host "2. Terima Tabung Endpoint:" -ForegroundColor Yellow
Write-Host "   URL: POST $base_url/mobile/terima-tabung"
Write-Host "   Headers:"
Write-Host "   - Authorization: Bearer [TOKEN_DARI_LOGIN]"
Write-Host "   - Content-Type: application/json"
Write-Host "   Body (JSON):"
Write-Host "   {"
Write-Host '     "lokasi_qr": "GDG-001",'
Write-Host '     "armada_qr": "ARM-001",'
Write-Host '     "tabung_qr": ["TBG-001", "TBG-002", "TBG-003"],'
Write-Host '     "keterangan": "Test dari Postman"'
Write-Host "   }"
Write-Host ""
Write-Host "=== FORMAT QR CODE YANG VALID ===" -ForegroundColor Magenta
Write-Host "- Gudang: GDG-001, GDG-002, GDG-999, dll"
Write-Host "- Armada: ARM-001, ARM-002, ARM-999, dll"
Write-Host "- Tabung: TBG-001, TBG-002, TBG-999, dll"
Write-Host ""
