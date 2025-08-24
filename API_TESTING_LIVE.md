# 🚀 API Testing Guide - Live Server test.gasalamsolusi.my.id

## 📋 Available API Endpoints

### Base URL: `https://test.gasalamsolusi.my.id/api`

### 1. **Authentication Endpoints**
```
POST /api/login-staff      - Login untuk staff (kepala_gudang, operator, driver)
POST /api/login-pelanggan  - Login untuk pelanggan  
POST /api/logout          - Logout (memerlukan token)
```

### 2. **Data Endpoints** (Memerlukan Authentication)
```
GET /api/tabung           - List semua tabung
GET /api/armada           - List semua armada
GET /api/gudang           - List semua gudang
GET /api/pelanggan        - List semua pelanggan
GET /api/test             - Test endpoint (public)
```

---

## 🧪 Testing Methods

### Method 1: **cURL Commands (Terminal/CMD)**

#### A. Test Public Endpoint:
```bash
curl -X GET https://test.gasalamsolusi.my.id/api/test
```

#### B. Login Staff:
```bash
curl -X POST https://test.gasalamsolusi.my.id/api/login-staff \
  -H "Content-Type: application/json" \
  -d '{
    "email": "kepala_gudang@tabungretail.com",
    "password": "admin123"
  }'
```

#### C. Login Pelanggan:
```bash
curl -X POST https://test.gasalamsolusi.my.id/api/login-pelanggan \
  -H "Content-Type: application/json" \
  -d '{
    "email": "pelanggan@test.com", 
    "password": "password123"
  }'
```

#### D. Get Data dengan Token:
```bash
# Ganti YOUR_TOKEN dengan token dari response login
curl -X GET https://test.gasalamsolusi.my.id/api/tabung \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Method 2: **Postman Collection**

#### Setup Postman:
1. **Create New Collection:** "Tabung Retail API - Live"
2. **Set Environment Variables:**
   - `base_url`: `https://test.gasalamsolusi.my.id/api`
   - `token`: (akan diset otomatis setelah login)

#### Request Examples:

**1. Test Endpoint (GET)**
```
URL: {{base_url}}/test
Method: GET
Headers: (none)
```

**2. Login Staff (POST)**
```
URL: {{base_url}}/login-staff
Method: POST
Headers: 
  Content-Type: application/json
Body (raw JSON):
{
  "email": "kepala_gudang@tabungretail.com",
  "password": "admin123"
}
```

**3. Get Tabung Data (GET)**
```
URL: {{base_url}}/tabung
Method: GET
Headers:
  Authorization: Bearer {{token}}
```

### Method 3: **VS Code REST Client**

#### Create file: `api-test-live.http`

```http
### Base Configuration
@baseUrl = https://test.gasalamsolusi.my.id/api
@token = YOUR_TOKEN_HERE

### Test Public Endpoint
GET {{baseUrl}}/test

### Login Kepala Gudang
POST {{baseUrl}}/login-staff
Content-Type: application/json

{
  "email": "kepala_gudang@tabungretail.com",
  "password": "admin123"
}

### Login Operator
POST {{baseUrl}}/login-staff
Content-Type: application/json

{
  "email": "operator@tabungretail.com",
  "password": "admin123"
}

### Login Pelanggan
POST {{baseUrl}}/login-pelanggan
Content-Type: application/json

{
  "email": "pelanggan@test.com",
  "password": "password123"
}

### Get Tabung Data (Require Token)
GET {{baseUrl}}/tabung
Authorization: Bearer {{token}}

### Get Armada Data (Require Token)
GET {{baseUrl}}/armada
Authorization: Bearer {{token}}

### Get Gudang Data (Require Token)
GET {{baseUrl}}/gudang
Authorization: Bearer {{token}}

### Get Pelanggan Data (Require Token)
GET {{baseUrl}}/pelanggan
Authorization: Bearer {{token}}

### Logout (Require Token)
POST {{baseUrl}}/logout
Authorization: Bearer {{token}}
```

### Method 4: **Browser JavaScript Console**

#### Test di Browser Developer Tools:
```javascript
// Test public endpoint
fetch('https://test.gasalamsolusi.my.id/api/test')
  .then(response => response.json())
  .then(data => console.log('Test Response:', data));

// Login staff
fetch('https://test.gasalamsolusi.my.id/api/login-staff', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'kepala_gudang@tabungretail.com',
    password: 'admin123'
  })
})
.then(response => response.json())
.then(data => {
  console.log('Login Response:', data);
  // Save token for next requests
  window.apiToken = data.data.token;
});

// Get data with token
fetch('https://test.gasalamsolusi.my.id/api/tabung', {
  headers: {
    'Authorization': 'Bearer ' + window.apiToken
  }
})
.then(response => response.json())
.then(data => console.log('Tabung Data:', data));
```

---

## 🔑 Test Accounts

### Staff Accounts:
```json
{
  "kepala_gudang": {
    "email": "kepala_gudang@tabungretail.com",
    "password": "admin123"
  },
  "operator": {
    "email": "operator@tabungretail.com", 
    "password": "admin123"
  }
}
```

### Customer Account:
```json
{
  "pelanggan": {
    "email": "pelanggan@test.com",
    "password": "password123"
  }
}
```

---

## 📊 Expected API Responses

### 1. Successful Login Response:
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Kepala Gudang",
      "email": "kepala_gudang@tabungretail.com",
      "role": "kepala_gudang"
    },
    "token": "1|abcdefghijk..."
  }
}
```

### 2. Data Response Example:
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [
    {
      "id": 1,
      "nama": "Tabung 12kg",
      "kode_qr": "TBG001",
      "status": "tersedia",
      "qr_code_path": "/storage/qr-codes/tabung/TBG001.png"
    }
  ]
}
```

### 3. Error Response Example:
```json
{
  "success": false,
  "message": "Invalid credentials",
  "data": null
}
```

---

## 🚨 Troubleshooting

### Issue 1: CORS Error
**Solution:** API sudah dikonfigurasi untuk CORS, pastikan menggunakan HTTPS

### Issue 2: 401 Unauthorized
**Solution:** 
- Pastikan token valid dan belum expired
- Format header: `Authorization: Bearer token_value`

### Issue 3: 404 Not Found
**Solution:**
- Periksa URL endpoint benar
- Pastikan deployment berhasil

### Issue 4: 500 Internal Server Error
**Solution:**
- Check error logs di cPanel
- Pastikan database connection working

---

## 🔍 Testing Workflow

### 1. **Basic Test:**
```bash
# Test if API is accessible
curl https://test.gasalamsolusi.my.id/api/test
```

### 2. **Authentication Test:**
```bash
# Login and get token
curl -X POST https://test.gasalamsolusi.my.id/api/login-staff \
  -H "Content-Type: application/json" \
  -d '{"email":"kepala_gudang@tabungretail.com","password":"admin123"}'
```

### 3. **Data Access Test:**
```bash
# Use token from step 2
curl -X GET https://test.gasalamsolusi.my.id/api/tabung \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 4. **Complete Test Flow:**
1. Test public endpoint ✅
2. Login dengan credentials ✅  
3. Simpan token ✅
4. Akses protected endpoints ✅
5. Logout ✅

---

## 📱 Mobile App Testing

### Flutter HTTP Example:
```dart
// Login request
final response = await http.post(
  Uri.parse('https://test.gasalamsolusi.my.id/api/login-staff'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({
    'email': 'kepala_gudang@tabungretail.com',
    'password': 'admin123'
  }),
);

// Data request with token
final dataResponse = await http.get(
  Uri.parse('https://test.gasalamsolusi.my.id/api/tabung'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json'
  },
);
```

---

**✅ Ready to test!** Pilih method yang paling sesuai untuk kebutuhan testing Anda!
