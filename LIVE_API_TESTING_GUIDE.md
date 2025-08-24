# 🧪 Live API Testing Guide untuk test.gasalamsolusi.my.id

## 🚀 Cara-cara Test API di Live Server

### 1. **Menggunakan Script Bash (Recommended)**

```bash
# Jalankan script otomatis
chmod +x test-api-live.sh
./test-api-live.sh
```

### 2. **Menggunakan VS Code REST Client**

Install extension **REST Client** di VS Code, lalu gunakan file `api-tests-live.http`:

```http
### Test Public Endpoint
GET https://test.gasalamsolusi.my.id/api/test

### Staff Login
POST https://test.gasalamsolusi.my.id/api/login-staff
Content-Type: application/json

{
  "email": "kepala_gudang@tabungretail.com",
  "password": "admin123"
}

### Get Tabung Data (requires token from login above)
GET https://test.gasalamsolusi.my.id/api/tabung
Authorization: Bearer YOUR_TOKEN_HERE
```

### 3. **Menggunakan PowerShell (Windows)**

```powershell
# Test public endpoint
Invoke-RestMethod -Uri "https://test.gasalamsolusi.my.id/api/test" -Method GET

# Test login
$loginData = @{
    email = "kepala_gudang@tabungretail.com"
    password = "admin123"
} | ConvertTo-Json

$response = Invoke-RestMethod -Uri "https://test.gasalamsolusi.my.id/api/login-staff" -Method POST -Body $loginData -ContentType "application/json"
$token = $response.token

# Test protected endpoint
$headers = @{ Authorization = "Bearer $token" }
Invoke-RestMethod -Uri "https://test.gasalamsolusi.my.id/api/tabung" -Method GET -Headers $headers
```

### 4. **Menggunakan Postman**

1. **Import Collection**: Import file `Tabung_Retail_API_Live.postman_collection.json`
2. **Set Environment**: 
   - `base_url`: `https://test.gasalamsolusi.my.id/api`
   - `token`: (akan diset otomatis setelah login)

### 5. **Menggunakan curl Command**

```bash
# Test public endpoint
curl https://test.gasalamsolusi.my.id/api/test

# Login staff
curl -X POST https://test.gasalamsolusi.my.id/api/login-staff \
  -H "Content-Type: application/json" \
  -d '{"email":"kepala_gudang@tabungretail.com","password":"admin123"}'

# Test with token (replace TOKEN with actual token)
curl -H "Authorization: Bearer TOKEN" \
  https://test.gasalamsolusi.my.id/api/tabung
```

---

## 🔑 Test Accounts

### Staff Accounts:
```
Kepala Gudang:
- Email: kepala_gudang@tabungretail.com
- Password: admin123
- Role: kepala_gudang

Operator:
- Email: operator@tabungretail.com  
- Password: admin123
- Role: operator_retail

Admin:
- Email: admin@tabungretail.com
- Password: admin123
- Role: admin_utama
```

### Customer Account:
```
Pelanggan:
- Email: pelanggan@test.com
- Password: password123
```

---

## 📋 API Endpoints untuk Testing

### Authentication:
- `POST /api/login-staff` - Login untuk staff
- `POST /api/login-pelanggan` - Login untuk customer
- `POST /api/logout` - Logout (requires token)

### Public:
- `GET /api/test` - Test endpoint tanpa auth

### Protected (requires Bearer token):
- `GET /api/tabung` - List tabung
- `GET /api/armada` - List armada
- `GET /api/gudang` - List gudang
- `GET /api/pelanggan` - List pelanggan

---

## ✅ Expected Results

### Successful Login Response:
```json
{
  "status": "success",
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Kepala Gudang",
    "email": "kepala_gudang@tabungretail.com",
    "role": "kepala_gudang"
  },
  "token": "1|abc123..."
}
```

### Protected Endpoint Response:
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "nomor_tabung": "TBG001",
      "qr_code": "data:image/png;base64,..."
    }
  ]
}
```

---

## 🚨 Troubleshooting

### Error 401 Unauthorized:
- Check token validity
- Ensure Bearer prefix in Authorization header
- Login again to get fresh token

### Error 500 Internal Server Error:
- Check live server deployment
- Run database_connection_fix.php
- Check error logs in cPanel

### Error 404 Not Found:
- Verify API routes are deployed correctly
- Check .htaccess configuration
- Run deploy.php script

### CORS Issues (if testing from browser):
- Use server-side testing (curl, Postman)
- Check CORS configuration in Laravel

---

## 📱 Flutter Integration Example

```dart
// Login example
final response = await http.post(
  Uri.parse('https://test.gasalamsolusi.my.id/api/login-staff'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({
    'email': 'kepala_gudang@tabungretail.com',
    'password': 'admin123',
  }),
);

// Use token for protected requests
final token = jsonDecode(response.body)['token'];
final dataResponse = await http.get(
  Uri.parse('https://test.gasalamsolusi.my.id/api/tabung'),
  headers: {'Authorization': 'Bearer $token'},
);
```

---

## 🔧 Quick Testing Commands

```bash
# Quick health check
curl -I https://test.gasalamsolusi.my.id/api/test

# Quick login test
curl -X POST https://test.gasalamsolusi.my.id/api/login-staff \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@tabungretail.com","password":"admin123"}' \
  | jq '.token'

# Test with saved token
TOKEN="your_token_here"
curl -H "Authorization: Bearer $TOKEN" \
  https://test.gasalamsolusi.my.id/api/tabung | jq
```

**🎯 Start with:** `./test-api-live.sh` for automatic comprehensive testing!
