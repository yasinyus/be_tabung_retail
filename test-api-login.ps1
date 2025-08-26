# Test API Login - PowerShell Script
Write-Host "🧪 Testing API V1 Login Endpoint" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green

$apiUrl = "http://192.168.1.7:8000/api/v1"

# Test 1: Check API status
Write-Host "`n1. Testing API Status..." -ForegroundColor Yellow
try {
    $testResponse = Invoke-RestMethod -Uri "$apiUrl/test" -Method GET -Headers @{"Accept"="application/json"}
    Write-Host "✅ API Status: $($testResponse.status)" -ForegroundColor Green
    Write-Host "   Message: $($testResponse.message)" -ForegroundColor White
} catch {
    Write-Host "❌ API Test Failed: $($_.Exception.Message)" -ForegroundColor Red
    exit
}

# Test 2: Login with dummy credentials
Write-Host "`n2. Testing Login Endpoint..." -ForegroundColor Yellow
$loginData = @{
    email = "driver@gmail.com"
    password = "password"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$apiUrl/auth/login" -Method POST -Body $loginData -ContentType "application/json" -Headers @{"Accept"="application/json"}
    
    Write-Host "✅ Login Response:" -ForegroundColor Green
    Write-Host "   Status: $($loginResponse.status)" -ForegroundColor White
    Write-Host "   Message: $($loginResponse.message)" -ForegroundColor White
    Write-Host "   User Type: $($loginResponse.user_type)" -ForegroundColor White
    Write-Host "   User ID: $($loginResponse.user.id)" -ForegroundColor White
    Write-Host "   User Name: $($loginResponse.user.name)" -ForegroundColor White
    Write-Host "   User Email: $($loginResponse.user.email)" -ForegroundColor White
    Write-Host "   User Roles: $($loginResponse.user.roles)" -ForegroundColor Cyan
    Write-Host "   Token: $($loginResponse.token.Substring(0,20))..." -ForegroundColor White
    
    # Test 3: Dashboard with token
    Write-Host "`n3. Testing Dashboard with Token..." -ForegroundColor Yellow
    $token = $loginResponse.token
    $headers = @{
        "Accept" = "application/json"
        "Authorization" = "Bearer $token"
    }
    
    $dashboardResponse = Invoke-RestMethod -Uri "$apiUrl/mobile/dashboard" -Method GET -Headers $headers
    Write-Host "✅ Dashboard Response:" -ForegroundColor Green
    Write-Host "   Status: $($dashboardResponse.status)" -ForegroundColor White
    Write-Host "   User Type: $($dashboardResponse.data.user_type)" -ForegroundColor White
    Write-Host "   User Name: $($dashboardResponse.data.user_name)" -ForegroundColor White
    
} catch {
    $errorDetails = $_.Exception.Response.GetResponseStream()
    $reader = New-Object System.IO.StreamReader($errorDetails)
    $responseBody = $reader.ReadToEnd()
    
    Write-Host "❌ Login Failed:" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "   Response: $responseBody" -ForegroundColor Red
}

Write-Host "`n🏁 Test Completed!" -ForegroundColor Green
