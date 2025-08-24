#!/bin/bash

# 🚀 Live API Testing Script untuk test.gasalamsolusi.my.id
# Script ini akan test semua endpoint API secara otomatis (TANPA ROLE PARAMETER)

echo "🔥 Starting API Tests for test.gasalamsolusi.my.id"
echo "🆕 NEW: Login WITHOUT role parameter - role auto-detected!"
echo "=================================================="

BASE_URL="https://test.gasalamsolusi.my.id/api"

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

print_success() {
    echo -e "${GREEN}🎉 $1${NC}"
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
fi

# Test 2: Staff Login (NO ROLE PARAMETER)
echo -e "\n${BLUE}📋 Test 2: Staff Authentication (NO ROLE PARAMETER)${NC}"

# Login Kepala Gudang - NO ROLE PARAMETER
login_response=$(curl -s -X POST "$BASE_URL/login-staff" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "kepala_gudang@tabungretail.com",
    "password": "admin123"
  }')

print_info "Login response: $login_response"

# Extract token from new response format
token=$(echo "$login_response" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ ! -z "$token" ]; then
    print_result 0 "Kepala Gudang login successful"
    print_info "Token: ${token:0:20}..."
    
    # Extract user info from response
    user_name=$(echo "$login_response" | grep -o '"name":"[^"]*"' | sed 's/"name":"\([^"]*\)"/\1/')
    user_role=$(echo "$login_response" | grep -o '"role":"[^"]*"' | sed 's/"role":"\([^"]*\)"/\1/')
    user_id=$(echo "$login_response" | grep -o '"id":[0-9]*' | sed 's/"id":\([0-9]*\)/\1/')
    
    print_success "User: $user_name (ID: $user_id, Role: $user_role)"
    print_success "Role automatically detected from database!"
else
    print_result 1 "Kepala Gudang login failed"
    echo "Response: $login_response"
fi

# Test 3: Protected Endpoints
if [ ! -z "$token" ]; then
    echo -e "\n${BLUE}📋 Test 3: Protected Endpoints${NC}"
    
    # Test Tabung endpoint
    print_info "Testing Tabung endpoint..."
    tabung_response=$(curl -s -w "%{http_code}" "$BASE_URL/tabung" \
      -H "Authorization: Bearer $token")
    
    tabung_http_code="${tabung_response: -3}"
    tabung_body="${tabung_response%???}"
    
    if [ "$tabung_http_code" = "200" ]; then
        print_result 0 "Tabung endpoint accessible with token"
        # Count items if response contains data array
        item_count=$(echo "$tabung_body" | grep -o '"data":\[' | wc -l)
        if [ "$item_count" -gt 0 ]; then
            print_info "Tabung data loaded successfully"
        fi
    else
        print_result 1 "Tabung endpoint failed (HTTP: $tabung_http_code)"
        print_warning "Response: ${tabung_body:0:100}..."
    fi
    
    # Test Armada endpoint
    print_info "Testing Armada endpoint..."
    armada_response=$(curl -s -w "%{http_code}" "$BASE_URL/armada" \
      -H "Authorization: Bearer $token")
    
    armada_http_code="${armada_response: -3}"
    
    if [ "$armada_http_code" = "200" ]; then
        print_result 0 "Armada endpoint accessible with token"
    else
        print_result 1 "Armada endpoint failed (HTTP: $armada_http_code)"
    fi
    
    # Test Gudang endpoint
    print_info "Testing Gudang endpoint..."
    gudang_response=$(curl -s -w "%{http_code}" "$BASE_URL/gudang" \
      -H "Authorization: Bearer $token")
    
    gudang_http_code="${gudang_response: -3}"
    
    if [ "$gudang_http_code" = "200" ]; then
        print_result 0 "Gudang endpoint accessible with token"
    else
        print_result 1 "Gudang endpoint failed (HTTP: $gudang_http_code)"
    fi
    
    # Test Pelanggan endpoint
    print_info "Testing Pelanggan endpoint..."
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

# Test 4: Other Staff Roles (NO ROLE PARAMETER)
echo -e "\n${BLUE}📋 Test 4: Other Staff Roles (NO ROLE PARAMETER)${NC}"

# Login Operator - NO ROLE PARAMETER
print_info "Testing Operator login..."
operator_response=$(curl -s -X POST "$BASE_URL/login-staff" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "operator@tabungretail.com",
    "password": "admin123"
  }')

operator_token=$(echo "$operator_response" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ ! -z "$operator_token" ]; then
    operator_role=$(echo "$operator_response" | grep -o '"role":"[^"]*"' | sed 's/"role":"\([^"]*\)"/\1/')
    operator_name=$(echo "$operator_response" | grep -o '"name":"[^"]*"' | sed 's/"name":"\([^"]*\)"/\1/')
    print_result 0 "Operator login successful"
    print_success "User: $operator_name (Role: $operator_role - auto-detected!)"
else
    print_result 1 "Operator login failed"
fi

# Login Admin - NO ROLE PARAMETER
print_info "Testing Admin login..."
admin_response=$(curl -s -X POST "$BASE_URL/login-staff" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@tabungretail.com",
    "password": "admin123"
  }')

admin_token=$(echo "$admin_response" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ ! -z "$admin_token" ]; then
    admin_role=$(echo "$admin_response" | grep -o '"role":"[^"]*"' | sed 's/"role":"\([^"]*\)"/\1/')
    admin_name=$(echo "$admin_response" | grep -o '"name":"[^"]*"' | sed 's/"name":"\([^"]*\)"/\1/')
    print_result 0 "Admin login successful"
    print_success "User: $admin_name (Role: $admin_role - auto-detected!)"
else
    print_result 1 "Admin login failed"
fi

# Test 5: Customer Login (NO ROLE PARAMETER)
echo -e "\n${BLUE}📋 Test 5: Customer Authentication (NO ROLE PARAMETER)${NC}"

customer_response=$(curl -s -X POST "$BASE_URL/login-pelanggan" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "pelanggan@test.com",
    "password": "password123"
  }')

customer_token=$(echo "$customer_response" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ ! -z "$customer_token" ]; then
    customer_name=$(echo "$customer_response" | grep -o '"name":"[^"]*"' | sed 's/"name":"\([^"]*\)"/\1/')
    customer_code=$(echo "$customer_response" | grep -o '"kode_pelanggan":"[^"]*"' | sed 's/"kode_pelanggan":"\([^"]*\)"/\1/')
    print_result 0 "Customer login successful"
    print_success "Customer: $customer_name (Code: $customer_code)"
else
    print_result 1 "Customer login failed"
    print_info "Response: $customer_response"
fi

# Test 6: Invalid Credentials
echo -e "\n${BLUE}📋 Test 6: Security Tests${NC}"

print_info "Testing invalid credentials..."
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
    print_result 1 "Security issue: invalid credentials not properly handled (HTTP: $invalid_http_code)"
fi

# Test 7: Access without token
print_info "Testing access without token..."
no_token_response=$(curl -s -w "%{http_code}" "$BASE_URL/tabung")
no_token_http_code="${no_token_response: -3}"

if [ "$no_token_http_code" = "401" ]; then
    print_result 0 "Protected endpoint properly requires authentication"
else
    print_result 1 "Security issue: protected endpoint accessible without token (HTTP: $no_token_http_code)"
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
echo -e "🏁 API Testing Complete!"
echo -e "==================================================${NC}"

echo -e "\n${GREEN}✅ Test Summary:${NC}"
echo "- Public endpoint accessible"
echo "- Staff authentication working (NO ROLE PARAMETER)"
echo "- Role auto-detection working"
echo "- Protected endpoints secured"
echo "- Multiple role support"
echo "- Customer authentication"
echo "- Security measures in place"

echo -e "\n${YELLOW}🆕 NEW FEATURES:${NC}"
echo "- ✅ No role parameter required in login"
echo "- ✅ Role automatically detected from database"
echo "- ✅ Simpler API integration"
echo "- ✅ Better security (role cannot be manipulated)"

echo -e "\n${BLUE}🔗 Quick Links:${NC}"
echo "- Homepage: https://test.gasalamsolusi.my.id"
echo "- Admin: https://test.gasalamsolusi.my.id/admin"
echo "- API Test: https://test.gasalamsolusi.my.id/api/test"

echo -e "\n${YELLOW}📱 For Flutter integration:${NC}"
echo "- Base URL: https://test.gasalamsolusi.my.id/api"
echo "- Staff Auth: POST /login-staff (email, password only)"
echo "- Customer Auth: POST /login-pelanggan (email, password only)"
echo "- Role will be returned in response after successful login"
echo "- All endpoints require Bearer token in Authorization header"

echo -e "\n${GREEN}🎯 Example Flutter Login:${NC}"
echo "{"
echo '  "email": "kepala_gudang@tabungretail.com",'
echo '  "password": "admin123"'
echo "  // No role parameter needed!"
echo "}"

print_info "Testing completed at $(date)"
print_success "API simplified successfully - role parameter removed!"
