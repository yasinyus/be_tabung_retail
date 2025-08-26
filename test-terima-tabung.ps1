# Test API Terima Tabung - PowerShell Script
Write-Host "🚛 Testing API Terima Tabung Armada" -ForegroundColor Green
Write-Host "===================================" -ForegroundColor Green

$apiUrl = "http://192.168.1.7:8000/api/v1"

# Step 1: Login untuk mendapatkan token
Write-Host "`n1. Login untuk mendapatkan token..." -ForegroundColor Yellow
$loginData = @{
    email = "driver@gmail.com"
    password = "password"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$apiUrl/auth/login" -Method POST -Body $loginData -ContentType "application/json" -Headers @{"Accept"="application/json"}
    $token = $loginResponse.token
    Write-Host "✅ Login berhasil, token didapat!" -ForegroundColor Green
    Write-Host "   User: $($loginResponse.user.name)" -ForegroundColor White
} catch {
    Write-Host "❌ Login gagal!" -ForegroundColor Red
    exit
}

# Step 2: Test API Terima Tabung
Write-Host "`n2. Testing API Terima Tabung..." -ForegroundColor Yellow

$terimaTabungData = @{
    lokasi_qr = "GDG-001"
    armada_qr = "ARM-001" 
    tabung_qr = @("TBG-001", "TBG-002", "TBG-003")
    keterangan = "Tabung dalam kondisi baik, tidak ada kerusakan"
} | ConvertTo-Json

$headers = @{
    "Accept" = "application/json"
    "Authorization" = "Bearer $token"
}

try {
    $response = Invoke-RestMethod -Uri "$apiUrl/mobile/terima-tabung" -Method POST -Body $terimaTabungData -ContentType "application/json" -Headers $headers
    
    Write-Host "✅ API Terima Tabung berhasil!" -ForegroundColor Green
    Write-Host "   Status: $($response.status)" -ForegroundColor White
    Write-Host "   Message: $($response.message)" -ForegroundColor White
    Write-Host ""
    Write-Host "📊 Data Transaksi:" -ForegroundColor Cyan
    Write-Host "   ID Transaksi: $($response.data.transaksi_id)" -ForegroundColor White
    Write-Host "   Tanggal: $($response.data.tanggal)" -ForegroundColor White
    Write-Host "   Lokasi QR: $($response.data.lokasi_qr)" -ForegroundColor White
    Write-Host "   Armada QR: $($response.data.armada_qr)" -ForegroundColor White
    Write-Host "   Total Tabung: $($response.data.total_tabung)" -ForegroundColor White
    Write-Host "   Nama User: $($response.data.nama_user)" -ForegroundColor White
    Write-Host "   Status: $($response.data.status_transaksi)" -ForegroundColor White
    Write-Host ""
    Write-Host "🔔 Notifikasi:" -ForegroundColor Yellow
    Write-Host "   Title: $($response.notification.title)" -ForegroundColor White
    Write-Host "   Message: $($response.notification.message)" -ForegroundColor White
    Write-Host "   Type: $($response.notification.type)" -ForegroundColor White
    
} catch {
    $errorDetails = $_.Exception.Response.GetResponseStream()
    $reader = New-Object System.IO.StreamReader($errorDetails)
    $responseBody = $reader.ReadToEnd()
    
    Write-Host "❌ API Terima Tabung gagal!" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "   Response: $responseBody" -ForegroundColor Red
}

# Step 3: Test dengan QR Code tidak valid
Write-Host "`n3. Testing dengan QR Code tidak valid..." -ForegroundColor Yellow

$invalidData = @{
    lokasi_qr = "INVALID-QR"
    armada_qr = "ARM-001"
    tabung_qr = @("TBG-001")
    keterangan = "Test dengan QR tidak valid"
} | ConvertTo-Json

try {
    $invalidResponse = Invoke-RestMethod -Uri "$apiUrl/mobile/terima-tabung" -Method POST -Body $invalidData -ContentType "application/json" -Headers $headers
    Write-Host "⚠️ Seharusnya error, tapi berhasil: $($invalidResponse.message)" -ForegroundColor Yellow
} catch {
    Write-Host "✅ Validasi QR berhasil - QR tidak valid ditolak!" -ForegroundColor Green
    $errorDetails = $_.Exception.Response.GetResponseStream()
    $reader = New-Object System.IO.StreamReader($errorDetails)
    $responseBody = $reader.ReadToEnd()
    $errorObj = $responseBody | ConvertFrom-Json
    Write-Host "   Error Message: $($errorObj.message)" -ForegroundColor White
}

Write-Host "`n🏁 Test API Terima Tabung selesai!" -ForegroundColor Green
