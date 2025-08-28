# Deployment Script untuk PT GAS API (Windows Server)
# Usage: .\deploy.ps1 -Domain "api.ptgas.com" -DatabaseName "ptgas_api" -DatabaseUser "ptgas_user" -DatabasePassword "mypassword123"

param(
    [Parameter(Mandatory=$true)]
    [string]$Domain,
    
    [Parameter(Mandatory=$true)]
    [string]$DatabaseName,
    
    [Parameter(Mandatory=$true)]
    [string]$DatabaseUser,
    
    [Parameter(Mandatory=$true)]
    [string]$DatabasePassword
)

# Function to print colored output
function Write-Status {
    param([string]$Message)
    Write-Host "[INFO] $Message" -ForegroundColor Green
}

function Write-Warning {
    param([string]$Message)
    Write-Host "[WARNING] $Message" -ForegroundColor Yellow
}

function Write-Error {
    param([string]$Message)
    Write-Host "[ERROR] $Message" -ForegroundColor Red
}

# Check if running as Administrator
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Error "This script must be run as Administrator"
    exit 1
}

$ProjectDir = "C:\inetpub\wwwroot\BE_MOBILE"

Write-Status "Starting deployment for domain: $Domain"
Write-Status "Database: $DatabaseName"
Write-Status "Project directory: $ProjectDir"

# Check if project directory exists
if (-not (Test-Path $ProjectDir)) {
    Write-Error "Project directory $ProjectDir does not exist"
    Write-Status "Please upload your code to $ProjectDir first"
    exit 1
}

Set-Location $ProjectDir

# Step 1: Install/Update Composer Dependencies
Write-Status "Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# Step 2: Environment Configuration
Write-Status "Configuring environment..."
if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Warning "Created .env file from .env.example"
}

# Update .env file with provided values
$envContent = Get-Content ".env" -Raw
$envContent = $envContent -replace "APP_URL=.*", "APP_URL=https://$Domain"
$envContent = $envContent -replace "DB_DATABASE=.*", "DB_DATABASE=$DatabaseName"
$envContent = $envContent -replace "DB_USERNAME=.*", "DB_USERNAME=$DatabaseUser"
$envContent = $envContent -replace "DB_PASSWORD=.*", "DB_PASSWORD=$DatabasePassword"
$envContent = $envContent -replace "APP_ENV=.*", "APP_ENV=production"
$envContent = $envContent -replace "APP_DEBUG=.*", "APP_DEBUG=false"
$envContent | Set-Content ".env"

Write-Status "Environment file updated"

# Step 3: Generate Application Key
Write-Status "Generating application key..."
php artisan key:generate --force

# Step 4: Database Setup
Write-Status "Setting up database..."
Write-Warning "Please ensure database '$DatabaseName' exists and user '$DatabaseUser' has proper permissions"

# Run migrations
php artisan migrate --force

# Create admin user if not exists
Write-Status "Creating admin user..."
php artisan user:create-test

# Step 5: Cache Configuration
Write-Status "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 6: IIS Configuration
Write-Status "Configuring IIS..."
$webConfig = @"
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="Imported Rule 1" stopProcessing="true">
                    <match url="^(.*)/$" ignoreCase="false" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Redirect" redirectType="Permanent" url="/{R:1}" />
                </rule>
                <rule name="Imported Rule 2" stopProcessing="true">
                    <match url="^" ignoreCase="false" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
"@

$webConfig | Out-File -FilePath "public\web.config" -Encoding UTF8

# Step 7: Set proper permissions
Write-Status "Setting proper permissions..."
$acl = Get-Acl $ProjectDir
$accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("IIS_IUSRS", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.SetAccessRule($accessRule)
Set-Acl $ProjectDir $acl

# Step 8: Test API
Write-Status "Testing API endpoints..."
Start-Sleep -Seconds 5

# Test basic connectivity
try {
    $response = Invoke-WebRequest -Uri "https://$Domain/api/v1/test" -Method GET -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Status "✅ API test endpoint working"
    } else {
        Write-Warning "⚠️ API test endpoint not responding correctly"
    }
} catch {
    Write-Warning "⚠️ API test endpoint not responding"
}

# Step 9: Create deployment info
$deploymentInfo = @"
Deployment completed successfully!

Domain: https://$Domain
Database: $DatabaseName
Project Directory: $ProjectDir

API Endpoints:
- Test: https://$Domain/api/v1/test
- Login: https://$Domain/api/v1/auth/login
- Terima Tabung: https://$Domain/api/v1/mobile/terima-tabung

Test Users:
- Admin: admin@example.com / password
- Pelanggan: pelanggan@example.com / password

Next Steps:
1. Test all API endpoints
2. Configure mobile app to use new domain
3. Set up monitoring and backup
4. Update DNS records if needed

Deployment Date: $(Get-Date)
"@

$deploymentInfo | Out-File -FilePath "deployment_info.txt" -Encoding UTF8

Write-Status "Deployment completed successfully!"
Write-Status "Check deployment_info.txt for details"

# Display test commands
Write-Host ""
Write-Status "Test your API with these commands:"
Write-Host "Invoke-WebRequest -Uri 'https://$Domain/api/v1/test' -Method GET"
Write-Host "Invoke-WebRequest -Uri 'https://$Domain/api/v1/auth/login' -Method POST -ContentType 'application/json' -Body '{\"email\":\"admin@example.com\",\"password\":\"password\"}'"
Write-Host ""
Write-Status "Deployment script completed!"
