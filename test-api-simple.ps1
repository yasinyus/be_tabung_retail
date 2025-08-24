# PowerShell API Testing Script for localhost
Write-Host "Starting Local API Tests..." -ForegroundColor Green

$BaseUrl = "http://localhost:8000/api"

# Test 1: Public Endpoint
Write-Host "`nTest 1: Public Endpoint" -ForegroundColor Blue
try {
    $response = Invoke-RestMethod -Uri "$BaseUrl/test" -Method GET
    Write-Host "SUCCESS: Public endpoint accessible" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Public endpoint failed" -ForegroundColor Red
    Write-Host "Make sure Laravel server is running: php artisan serve" -ForegroundColor Yellow
}

# Test 2: Staff Login
Write-Host "`nTest 2: Staff Authentication" -ForegroundColor Blue
$loginData = @{
    email = "kepala_gudang@tabungretail.com"
    password = "admin123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$BaseUrl/login-staff" -Method POST -Body $loginData -ContentType "application/json"
    $Token = $loginResponse.token
    Write-Host "SUCCESS: Staff login successful" -ForegroundColor Green
    Write-Host "User: $($loginResponse.user.name) - Role: $($loginResponse.user.role)" -ForegroundColor Cyan
} catch {
    Write-Host "ERROR: Staff login failed" -ForegroundColor Red
    Write-Host "Make sure database is seeded: php artisan db:seed" -ForegroundColor Yellow
}

# Test 3: Protected Endpoints
if ($Token) {
    Write-Host "`nTest 3: Protected Endpoints" -ForegroundColor Blue
    $headers = @{ Authorization = "Bearer $Token" }
    
    try {
        $tabungResponse = Invoke-RestMethod -Uri "$BaseUrl/tabung" -Method GET -Headers $headers
        Write-Host "SUCCESS: Tabung endpoint accessible" -ForegroundColor Green
    } catch {
        Write-Host "ERROR: Tabung endpoint failed" -ForegroundColor Red
    }
    
    try {
        $armadaResponse = Invoke-RestMethod -Uri "$BaseUrl/armada" -Method GET -Headers $headers
        Write-Host "SUCCESS: Armada endpoint accessible" -ForegroundColor Green
    } catch {
        Write-Host "ERROR: Armada endpoint failed" -ForegroundColor Red
    }
}

# Test 4: Customer Login
Write-Host "`nTest 4: Customer Authentication" -ForegroundColor Blue
$customerData = @{
    email = "pelanggan@test.com"
    password = "password123"
} | ConvertTo-Json

try {
    $customerResponse = Invoke-RestMethod -Uri "$BaseUrl/login-pelanggan" -Method POST -Body $customerData -ContentType "application/json"
    Write-Host "SUCCESS: Customer login successful" -ForegroundColor Green
    Write-Host "Customer: $($customerResponse.user.name)" -ForegroundColor Cyan
} catch {
    Write-Host "ERROR: Customer login failed" -ForegroundColor Red
}

Write-Host "`nAPI Testing Complete!" -ForegroundColor Green
Write-Host "Local server: http://localhost:8000" -ForegroundColor Blue
Write-Host "Admin panel: http://localhost:8000/admin" -ForegroundColor Blue
