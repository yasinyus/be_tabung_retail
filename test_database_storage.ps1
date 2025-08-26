# PowerShell Script untuk Test API Terima Tabung dengan Database
$baseUrl = "http://192.168.1.7:8000/api/v1"

Write-Host "=== Test API Terima Tabung dengan Database ===" -ForegroundColor Green

# Test Login sebagai User
Write-Host "`n1. Login sebagai Admin User..." -ForegroundColor Yellow
$loginData = @{
    email = "admin@example.com"
    password = "password"
}

try {
    $loginResponse = Invoke-RestMethod -Uri "$baseUrl/auth/login" -Method POST -Body ($loginData | ConvertTo-Json) -ContentType "application/json"
    Write-Host "✓ Login berhasil sebagai: $($loginResponse.user.name)" -ForegroundColor Green
    $token = $loginResponse.token
    
    # Headers untuk request selanjutnya
    $headers = @{
        "Authorization" = "Bearer $token"
        "Content-Type" = "application/json"
        "Accept" = "application/json"
    }
    
    # Test Terima Tabung dengan Database Storage
    Write-Host "`n2. Test Terima Tabung dengan Database Storage..." -ForegroundColor Yellow
    $terimaTabungData = @{
        lokasi_qr = "GDG-001"
        armada_qr = "ARM-002" 
        tabung_qr = @("TBG-001", "TBG-002", "TBG-003", "TBG-004", "TBG-005")
        keterangan = "Terima tabung dari armada untuk gudang Jakarta - Test Database Storage"
    }
    
    $terimaResponse = Invoke-RestMethod -Uri "$baseUrl/mobile/terima-tabung" -Method POST -Headers $headers -Body ($terimaTabungData | ConvertTo-Json)
    
    Write-Host "✓ API Response:" -ForegroundColor Green
    Write-Host "  Status: $($terimaResponse.status)" -ForegroundColor Cyan
    Write-Host "  Message: $($terimaResponse.message)" -ForegroundColor Cyan
    Write-Host "  Transaksi ID: $($terimaResponse.data.transaksi_id)" -ForegroundColor Cyan
    Write-Host "  ID Aktivitas: $($terimaResponse.data.id_aktivitas)" -ForegroundColor Cyan
    Write-Host "  Total Tabung: $($terimaResponse.data.total_tabung)" -ForegroundColor Cyan
    Write-Host "  Status Transaksi: $($terimaResponse.data.status_transaksi)" -ForegroundColor Cyan
    Write-Host "  Notification: $($terimaResponse.notification.message)" -ForegroundColor Cyan
    
    # Test QR Code Invalid
    Write-Host "`n3. Test QR Code Invalid..." -ForegroundColor Yellow
    $invalidData = @{
        lokasi_qr = "INVALID-QR"
        armada_qr = "ARM-002"
        tabung_qr = @("TBG-001")
        keterangan = "Test invalid QR"
    }
    
    try {
        $invalidResponse = Invoke-RestMethod -Uri "$baseUrl/mobile/terima-tabung" -Method POST -Headers $headers -Body ($invalidData | ConvertTo-Json)
    }
    catch {
        $errorResponse = $_.ErrorDetails.Message | ConvertFrom-Json
        Write-Host "✓ Validasi QR berhasil - Error: $($errorResponse.message)" -ForegroundColor Green
    }
    
    # Test Tabung QR Invalid
    Write-Host "`n4. Test Tabung QR Invalid..." -ForegroundColor Yellow
    $invalidTabungData = @{
        lokasi_qr = "GDG-001"
        armada_qr = "ARM-002"
        tabung_qr = @("TBG-001", "INVALID-TABUNG", "TBG-003")
        keterangan = "Test invalid tabung QR"
    }
    
    try {
        $invalidTabungResponse = Invoke-RestMethod -Uri "$baseUrl/mobile/terima-tabung" -Method POST -Headers $headers -Body ($invalidTabungData | ConvertTo-Json)
    }
    catch {
        $errorResponse = $_.ErrorDetails.Message | ConvertFrom-Json
        Write-Host "✓ Validasi Tabung QR berhasil - Error: $($errorResponse.message)" -ForegroundColor Green
        if ($errorResponse.invalid_tabung) {
            Write-Host "  Invalid Tabung pada index: $($errorResponse.invalid_tabung -join ', ')" -ForegroundColor Red
        }
    }
    
}
catch {
    Write-Host "✗ Error pada login: $($_.Exception.Message)" -ForegroundColor Red
    if ($_.ErrorDetails) {
        $errorDetail = $_.ErrorDetails.Message | ConvertFrom-Json
        Write-Host "  Detail: $($errorDetail.message)" -ForegroundColor Red
    }
}

Write-Host "`n=== Test Database Storage Selesai ===" -ForegroundColor Green
Write-Host "Silakan cek database untuk memastikan data tersimpan dengan benar." -ForegroundColor Cyan
