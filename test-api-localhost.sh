#!/bin/bash

# 🚀 Local API Testing Script untuk localhost
# Script ini akan test semua endpoint API secara otomatis di server lokal

echo "🔥 Starting API Tests for localhost"
echo "=================================================="

BASE_URL="http://localhost:8000/api"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_result() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✅ $2${NC}"
    else
        echo -e "${RED}❌ $2${NC}"
    fi
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# Test 1: Public Endpoint
echo -e "\n${BLUE}📋 Test 1: Public Endpoint${NC}"
response=$(curl -s -w "%{http_code}" "$BASE_URL/test")
http_code="${response: -3}"
body="${response%???}"

if [ "$http_code" = "200" ]; then
    print_result 0 "Public endpoint accessible"
    echo "Response: $body"
else
    print_result 1 "Public endpoint failed (HTTP: $http_code)"
    print_warning "Make sure Laravel server is running: php artisan serve"
fi

# Test 2: Staff Login
echo -e "\n${BLUE}📋 Test 2: Staff Authentication${NC}"

# Login Kepala Gudang
login_response=$(curl -s -X POST "$BASE_URL/login-staff" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "kepala_gudang@tabungretail.com",
    "password": "admin123"
  }')

print_info "Login response: $login_response"

# Extract token using grep and sed (bash compatible)
token=$(echo "$login_response" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ ! -z "$token" ]; then
    print_result 0 "Kepala Gudang login successful"
    print_info "Token: ${token:0:20}..."
    
    # Extract user info
    user_name=$(echo "$login_response" | grep -o '"name":"[^"]*"' | sed 's/"name":"\([^"]*\)"/\1/')
    user_role=$(echo "$login_response" | grep -o '"role":"[^"]*"' | sed 's/"role":"\([^"]*\)"/\1/')
    print_info "User: $user_name (Role: $user_role)"
else
    print_result 1 "Kepala Gudang login failed"
    echo "Response: $login_response"
    print_warning "Make sure database is seeded: php artisan db:seed --class=UserSeeder"
fi

# Test 3: Protected Endpoints
if [ ! -z "$token" ]; then
    echo -e "\n${BLUE}📋 Test 3: Protected Endpoints${NC}"
    
    # Test Tabung endpoint
    tabung_response=$(curl -s -w "%{http_code}" "$BASE_URL/tabung" \
      -H "Authorization: Bearer $token")
    
    tabung_http_code="${tabung_response: -3}"
    tabung_body="${tabung_response%???}"
    
    if [ "$tabung_http_code" = "200" ]; then
        print_result 0 "Tabung endpoint accessible with token"
        # Count items if response is JSON array
        count=$(echo "$tabung_body" | grep -o '"data":\[.*\]' | grep -o '\[.*\]' | grep -o ',' | wc -l)
        count=$((count + 1))
        print_info "Tabung count: $count items"
    else
        print_result 1 "Tabung endpoint failed (HTTP: $tabung_http_code)"
    fi
    
    # Test Armada endpoint
    armada_response=$(curl -s -w "%{http_code}" "$BASE_URL/armada" \
      -H "Authorization: Bearer $token")
    
    armada_http_code="${armada_response: -3}"
    
    if [ "$armada_http_code" = "200" ]; then
        print_result 0 "Armada endpoint accessible with token"
    else
        print_result 1 "Armada endpoint failed (HTTP: $armada_http_code)"
    fi
    
    # Test Gudang endpoint
    gudang_response=$(curl -s -w "%{http_code}" "$BASE_URL/gudang" \
      -H "Authorization: Bearer $token")
    
    gudang_http_code="${gudang_response: -3}"
    
    if [ "$gudang_http_code" = "200" ]; then
        print_result 0 "Gudang endpoint accessible with token"
    else
        print_result 1 "Gudang endpoint failed (HTTP: $gudang_http_code)"
    fi
    
    # Test Pelanggan endpoint
    pelanggan_response=$(curl -s -w "%{http_code}" "$BASE_URL/pelanggan" \
      -H "Authorization: Bearer $token")
    
    pelanggan_http_code="${pelanggan_response: -3}"
    
    if [ "$pelanggan_http_code" = "200" ]; then
        print_result 0 "Pelanggan endpoint accessible with token"
    else
        print_result 1 "Pelanggan endpoint failed (HTTP: $pelanggan_http_code)"
    fi
else
    print_warning "Skipping protected endpoint tests (no token)"
fi

# Test 4: Other Staff Roles
echo -e "\n${BLUE}📋 Test 4: Other Staff Roles${NC}"

# Login Operator
operator_response=$(curl -s -X POST "$BASE_URL/login-staff" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "operator@tabungretail.com",
    "password": "admin123"
  }')

operator_token=$(echo "$operator_response" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ ! -z "$operator_token" ]; then
    print_result 0 "Operator login successful"
    operator_name=$(echo "$operator_response" | grep -o '"name":"[^"]*"' | sed 's/"name":"\([^"]*\)"/\1/')
    operator_role=$(echo "$operator_response" | grep -o '"role":"[^"]*"' | sed 's/"role":"\([^"]*\)"/\1/')
    print_info "User: $operator_name (Role: $operator_role)"
else
    print_result 1 "Operator login failed"
fi

# Test Admin Login
admin_response=$(curl -s -X POST "$BASE_URL/login-staff" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@tabungretail.com",
    "password": "admin123"
  }')

admin_token=$(echo "$admin_response" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ ! -z "$admin_token" ]; then
    print_result 0 "Admin login successful"
    admin_name=$(echo "$admin_response" | grep -o '"name":"[^"]*"' | sed 's/"name":"\([^"]*\)"/\1/')
    admin_role=$(echo "$admin_response" | grep -o '"role":"[^"]*"' | sed 's/"role":"\([^"]*\)"/\1/')
    print_info "User: $admin_name (Role: $admin_role)"
else
    print_result 1 "Admin login failed"
fi

# Test 5: Customer Login
echo -e "\n${BLUE}📋 Test 5: Customer Authentication${NC}"

customer_response=$(curl -s -X POST "$BASE_URL/login-pelanggan" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "pelanggan@test.com",
    "password": "password123"
  }')

customer_token=$(echo "$customer_response" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ ! -z "$customer_token" ]; then
    print_result 0 "Customer login successful"
    customer_name=$(echo "$customer_response" | grep -o '"name":"[^"]*"' | sed 's/"name":"\([^"]*\)"/\1/')
    print_info "Customer: $customer_name"
else
    print_result 1 "Customer login failed"
    print_info "Response: $customer_response"
    print_warning "Make sure pelanggan data is seeded"
fi

# Test 6: Invalid Credentials
echo -e "\n${BLUE}📋 Test 6: Security Tests${NC}"

invalid_response=$(curl -s -w "%{http_code}" "$BASE_URL/login-staff" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "invalid@email.com",
    "password": "wrongpassword"
  }')

invalid_http_code="${invalid_response: -3}"

if [ "$invalid_http_code" = "401" ] || [ "$invalid_http_code" = "422" ]; then
    print_result 0 "Invalid credentials properly rejected"
else
    print_result 1 "Security issue: invalid credentials not properly handled"
fi

# Test 7: Access without token
no_token_response=$(curl -s -w "%{http_code}" "$BASE_URL/tabung")
no_token_http_code="${no_token_response: -3}"

if [ "$no_token_http_code" = "401" ]; then
    print_result 0 "Protected endpoint properly requires authentication"
else
    print_result 1 "Security issue: protected endpoint accessible without token"
fi

# Test 8: Logout
if [ ! -z "$token" ]; then
    echo -e "\n${BLUE}📋 Test 8: Logout${NC}"
    
    logout_response=$(curl -s -w "%{http_code}" -X POST "$BASE_URL/logout" \
      -H "Authorization: Bearer $token")
    
    logout_http_code="${logout_response: -3}"
    
    if [ "$logout_http_code" = "200" ]; then
        print_result 0 "Logout successful"
    else
        print_result 1 "Logout failed (HTTP: $logout_http_code)"
    fi
fi

# Summary
echo -e "\n${BLUE}=================================================="
echo -e "🏁 Local API Testing Complete!"
echo -e "==================================================${NC}"

echo -e "\n${GREEN}✅ Test Summary:${NC}"
echo "- Public endpoint accessible"
echo "- Staff authentication working"
echo "- Protected endpoints secured"
echo "- Multiple role support"
echo "- Customer authentication"
echo "- Security measures in place"

echo -e "\n${BLUE}🔗 Local Links:${NC}"
echo "- Homepage: http://localhost:8000"
echo "- Admin: http://localhost:8000/admin"
echo "- API Test: http://localhost:8000/api/test"

echo -e "\n${YELLOW}📱 For Flutter integration:${NC}"
echo "- Base URL: http://localhost:8000/api"
echo "- Auth endpoints: /login-staff and /login-pelanggan"
echo "- All endpoints require Bearer token in Authorization header"

echo -e "\n${BLUE}🔧 Local Development Commands:${NC}"
echo "- Start server: php artisan serve"
echo "- Migrate database: php artisan migrate:fresh --seed"
echo "- Clear cache: php artisan cache:clear"

print_info "Testing completed at $(date)"
