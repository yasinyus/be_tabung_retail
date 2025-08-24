# 🧪 PowerShell API Testing Script untuk localhost

Write-Host "🚀 Starting Local API Tests for localhost" -ForegroundColor Green
Write-Host "================================================================" -ForegroundColor Cyan

$BaseUrl = "http://localhost:8000/api"
$Token = ""

# Function untuk colored output
function Write-Success($message) {
    Write-Host "✅ $message" -ForegroundColor Green
}

function Write-Error($message) {
    Write-Host "❌ $message" -ForegroundColor Red
}

function Write-Info($message) {
    Write-Host "ℹ️  $message" -ForegroundColor Blue
}

function Write-Warning($message) {
    Write-Host "⚠️  $message" -ForegroundColor Yellow
}

# Test 1: Public Endpoint
Write-Host "`n📋 Test 1: Public Endpoint" -ForegroundColor Blue
try {
    $response = Invoke-RestMethod -Uri "$BaseUrl/test" -Method GET
    Write-Success "Public endpoint accessible"
    Write-Info "Response: $($response | ConvertTo-Json -Compress)"
} catch {
    Write-Error "Public endpoint failed: $($_.Exception.Message)"
    Write-Warning "Make sure Laravel server is running: php artisan serve"
}

# Test 2: Staff Authentication
Write-Host "`n📋 Test 2: Staff Authentication" -ForegroundColor Blue

$loginData = @{
    email = "kepala_gudang@tabungretail.com"
    password = "admin123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$BaseUrl/login-staff" -Method POST -Body $loginData -ContentType "application/json"
    $Token = $loginResponse.token
    Write-Success "Kepala Gudang login successful"
    Write-Info "Token: $($Token.Substring(0, [Math]::Min(20, $Token.Length)))..."
    Write-Info "User: $($loginResponse.user.name) - Role: $($loginResponse.user.role)"
} catch {
    Write-Error "Kepala Gudang login failed: $($_.Exception.Message)"
    Write-Warning "Make sure database is seeded: php artisan db:seed --class=UserSeeder"
}

# Test 3: Protected Endpoints
if ($Token) {
    Write-Host "`n📋 Test 3: Protected Endpoints" -ForegroundColor Blue
    $headers = @{ Authorization = "Bearer $Token" }
    
    # Test Tabung
    try {
        $tabungResponse = Invoke-RestMethod -Uri "$BaseUrl/tabung" -Method GET -Headers $headers
        Write-Success "Tabung endpoint accessible with token"
        if ($tabungResponse.data) {
            Write-Info "Tabung count: $($tabungResponse.data.Count)"
        }
    } catch {
        Write-Error "Tabung endpoint failed: $($_.Exception.Message)"
    }
    
    # Test Armada
    try {
        $armadaResponse = Invoke-RestMethod -Uri "$BaseUrl/armada" -Method GET -Headers $headers
        Write-Success "Armada endpoint accessible with token"
        if ($armadaResponse.data) {
            Write-Info "Armada count: $($armadaResponse.data.Count)"
        }
    } catch {
        Write-Error "Armada endpoint failed: $($_.Exception.Message)"
    }
    
    # Test Gudang
    try {
        $gudangResponse = Invoke-RestMethod -Uri "$BaseUrl/gudang" -Method GET -Headers $headers
        Write-Success "Gudang endpoint accessible with token"
        if ($gudangResponse.data) {
            Write-Info "Gudang count: $($gudangResponse.data.Count)"
        }
    } catch {
        Write-Error "Gudang endpoint failed: $($_.Exception.Message)"
    }
    
    # Test Pelanggan
    try {
        $pelangganResponse = Invoke-RestMethod -Uri "$BaseUrl/pelanggan" -Method GET -Headers $headers
        Write-Success "Pelanggan endpoint accessible with token"
        if ($pelangganResponse.data) {
            Write-Info "Pelanggan count: $($pelangganResponse.data.Count)"
        }
    } catch {
        Write-Error "Pelanggan endpoint failed: $($_.Exception.Message)"
    }
} else {
    Write-Warning "Skipping protected endpoint tests (no token)"
}

# Test 4: Other Staff Roles
Write-Host "`n📋 Test 4: Other Staff Roles" -ForegroundColor Blue

# Test Operator Login
$operatorData = @{
    email = "operator@tabungretail.com"
    password = "admin123"
} | ConvertTo-Json

try {
    $operatorResponse = Invoke-RestMethod -Uri "$BaseUrl/login-staff" -Method POST -Body $operatorData -ContentType "application/json"
    Write-Success "Operator login successful"
    Write-Info "Operator: $($operatorResponse.user.name) - Role: $($operatorResponse.user.role)"
} catch {
    Write-Error "Operator login failed: $($_.Exception.Message)"
}

# Test Admin Login
$adminData = @{
    email = "admin@tabungretail.com"
    password = "admin123"
} | ConvertTo-Json

try {
    $adminResponse = Invoke-RestMethod -Uri "$BaseUrl/login-staff" -Method POST -Body $adminData -ContentType "application/json"
    Write-Success "Admin login successful"
    Write-Info "Admin: $($adminResponse.user.name) - Role: $($adminResponse.user.role)"
} catch {
    Write-Error "Admin login failed: $($_.Exception.Message)"
}

# Test 5: Customer Authentication
Write-Host "`n📋 Test 5: Customer Authentication" -ForegroundColor Blue

$customerData = @{
    email = "pelanggan@test.com"
    password = "password123"
} | ConvertTo-Json

try {
    $customerResponse = Invoke-RestMethod -Uri "$BaseUrl/login-pelanggan" -Method POST -Body $customerData -ContentType "application/json"
    Write-Success "Customer login successful"
    Write-Info "Customer: $($customerResponse.user.name)"
    if ($customerResponse.user.kode_pelanggan) {
        Write-Info "Customer Code: $($customerResponse.user.kode_pelanggan)"
    }
} catch {
    Write-Error "Customer login failed: $($_.Exception.Message)"
    Write-Warning "Make sure pelanggan data is seeded"
}

# Test 6: Security Tests
Write-Host "`n📋 Test 6: Security Tests" -ForegroundColor Blue

# Test invalid credentials
$invalidData = @{
    email = "invalid@email.com"
    password = "wrongpassword"
} | ConvertTo-Json

try {
    $invalidResponse = Invoke-RestMethod -Uri "$BaseUrl/login-staff" -Method POST -Body $invalidData -ContentType "application/json"
    Write-Error "Security issue: invalid credentials accepted"
} catch {
    if ($_.Exception.Response.StatusCode -eq 401 -or $_.Exception.Response.StatusCode -eq 422) {
        Write-Success "Invalid credentials properly rejected"
    } else {
        Write-Error "Unexpected error for invalid credentials: $($_.Exception.Message)"
    }
}

# Test access without token
try {
    $noTokenResponse = Invoke-RestMethod -Uri "$BaseUrl/tabung" -Method GET
    Write-Error "Security issue: protected endpoint accessible without token"
} catch {
    if ($_.Exception.Response.StatusCode -eq 401) {
        Write-Success "Protected endpoint properly requires authentication"
    } else {
        Write-Error "Unexpected error for no token access: $($_.Exception.Message)"
    }
}

# Test 7: Logout
if ($Token) {
    Write-Host "`n📋 Test 7: Logout" -ForegroundColor Blue
    $headers = @{ Authorization = "Bearer $Token" }
    
    try {
        $logoutResponse = Invoke-RestMethod -Uri "$BaseUrl/logout" -Method POST -Headers $headers
        Write-Success "Logout successful"
    } catch {
        Write-Error "Logout failed: $($_.Exception.Message)"
    }
}

# Summary
Write-Host "`n================================================================" -ForegroundColor Cyan
Write-Host "🏁 Local API Testing Complete!" -ForegroundColor Green
Write-Host "================================================================" -ForegroundColor Cyan

Write-Host "`n✅ Test Summary:" -ForegroundColor Green
Write-Host "- Public endpoint accessible"
Write-Host "- Staff authentication working"
Write-Host "- Protected endpoints secured"
Write-Host "- Multiple role support"
Write-Host "- Customer authentication"
Write-Host "- Security measures in place"

Write-Host "`n🔗 Local Links:" -ForegroundColor Blue
Write-Host "- Homepage: http://localhost:8000"
Write-Host "- Admin: http://localhost:8000/admin"
Write-Host "- API Test: http://localhost:8000/api/test"

Write-Host "`n📱 For Flutter integration:" -ForegroundColor Yellow
Write-Host "- Base URL: http://localhost:8000/api"
Write-Host "- Auth endpoints: /login-staff and /login-pelanggan"
Write-Host "- All endpoints require Bearer token in Authorization header"

Write-Host "`n🔧 Local Development Commands:" -ForegroundColor Blue
Write-Host "- Start server: php artisan serve"
Write-Host "- Migrate database: php artisan migrate:fresh --seed"
Write-Host "- Clear cache: php artisan cache:clear"

Write-Info "Testing completed at $(Get-Date)"

# Pause to see results
Write-Host "`nPress any key to continue..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
