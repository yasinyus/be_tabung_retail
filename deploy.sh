#!/bin/bash

# Deployment Script untuk PT GAS API
# Usage: ./deploy.sh [domain] [database_name] [database_user] [database_password]

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   print_error "This script should not be run as root"
   exit 1
fi

# Check parameters
if [ $# -lt 4 ]; then
    print_error "Usage: $0 <domain> <database_name> <database_user> <database_password>"
    print_error "Example: $0 api.ptgas.com ptgas_api ptgas_user mypassword123"
    exit 1
fi

DOMAIN=$1
DB_NAME=$2
DB_USER=$3
DB_PASSWORD=$4
PROJECT_DIR="/var/www/html/BE_MOBILE"

print_status "Starting deployment for domain: $DOMAIN"
print_status "Database: $DB_NAME"
print_status "Project directory: $PROJECT_DIR"

# Check if project directory exists
if [ ! -d "$PROJECT_DIR" ]; then
    print_error "Project directory $PROJECT_DIR does not exist"
    print_status "Please upload your code to $PROJECT_DIR first"
    exit 1
fi

cd "$PROJECT_DIR"

# Step 1: Install/Update Composer Dependencies
print_status "Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# Step 2: Set proper permissions
print_status "Setting proper permissions..."
sudo chown -R www-data:www-data "$PROJECT_DIR"
sudo chmod -R 755 "$PROJECT_DIR"
sudo chmod -R 775 "$PROJECT_DIR/storage"
sudo chmod -R 775 "$PROJECT_DIR/bootstrap/cache"

# Step 3: Environment Configuration
print_status "Configuring environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    print_warning "Created .env file from .env.example"
fi

# Update .env file with provided values
sed -i "s/APP_URL=.*/APP_URL=https:\/\/$DOMAIN/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env

print_status "Environment file updated"

# Step 4: Generate Application Key
print_status "Generating application key..."
php artisan key:generate --force

# Step 5: Database Setup
print_status "Setting up database..."
print_warning "Please ensure database '$DB_NAME' exists and user '$DB_USER' has proper permissions"

# Run migrations
php artisan migrate --force

# Create admin user if not exists
print_status "Creating admin user..."
php artisan user:create-test

# Step 6: Cache Configuration
print_status "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 7: Create Nginx configuration
print_status "Creating Nginx configuration..."
sudo tee /etc/nginx/sites-available/$DOMAIN > /dev/null <<EOF
server {
    listen 80;
    server_name $DOMAIN;
    root $PROJECT_DIR/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Enable site
sudo ln -sf /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

print_status "Nginx configuration created and enabled"

# Step 8: SSL Certificate (Let's Encrypt)
print_status "Setting up SSL certificate..."
if command -v certbot &> /dev/null; then
    sudo certbot --nginx -d $DOMAIN --non-interactive --agree-tos --email admin@$DOMAIN
    print_status "SSL certificate installed"
else
    print_warning "Certbot not found. Please install it manually:"
    print_warning "sudo apt install certbot python3-certbot-nginx"
fi

# Step 9: Test API
print_status "Testing API endpoints..."
sleep 5  # Wait for nginx to reload

# Test basic connectivity
if curl -s -o /dev/null -w "%{http_code}" https://$DOMAIN/api/v1/test | grep -q "200"; then
    print_status "✅ API test endpoint working"
else
    print_warning "⚠️  API test endpoint not responding"
fi

# Step 10: Create deployment info
cat > deployment_info.txt <<EOF
Deployment completed successfully!

Domain: https://$DOMAIN
Database: $DB_NAME
Project Directory: $PROJECT_DIR

API Endpoints:
- Test: https://$DOMAIN/api/v1/test
- Login: https://$DOMAIN/api/v1/auth/login
- Terima Tabung: https://$DOMAIN/api/v1/mobile/terima-tabung

Test Users:
- Admin: admin@example.com / password
- Pelanggan: pelanggan@example.com / password

Next Steps:
1. Test all API endpoints
2. Configure mobile app to use new domain
3. Set up monitoring and backup
4. Update DNS records if needed

Deployment Date: $(date)
EOF

print_status "Deployment completed successfully!"
print_status "Check deployment_info.txt for details"

# Display test commands
echo ""
print_status "Test your API with these commands:"
echo "curl -I https://$DOMAIN/api/v1/test"
echo "curl -X POST https://$DOMAIN/api/v1/auth/login -H 'Content-Type: application/json' -d '{\"email\":\"admin@example.com\",\"password\":\"password\"}'"
echo ""
print_status "Deployment script completed!"
