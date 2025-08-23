# 🎯 API Testing di VS Code - READY TO USE!

## ✅ Status API:
- **Server**: Running di http://127.0.0.1:8000 ✅
- **Basic Endpoint**: Working ✅  
- **REST Client**: Installed ✅
- **Test File**: `api-tests.http` Ready ✅

## 🚀 QUICK START - Langsung Test!

### 1. Buka File Test
Di VS Code, buka file: `api-tests.http`

### 2. Test Basic Endpoint (MULAI DARI SINI)
- Scroll ke "0.1 Test Basic Endpoint"
- Click **"Send Request"** 
- Should return: `{"success":true,"message":"API Test endpoint working"}`

### 3. Test Login
- Scroll ke "1.1 Login Kepala Gudang"  
- Click **"Send Request"**
- Copy `token` dari response
- Update `@authToken = PASTE_TOKEN_HERE`

### 4. Test Authenticated Endpoints
- Test Dashboard: "2.2 Get Dashboard"
- Test Role Access: "3.1 Get Tabung List"
- Test QR Scanner: "4.1 Scan Tabung QR"

## 📋 Test Credentials Ready:
```
kepala.gudang@mobile.test / password123
operator@mobile.test / password123  
driver@mobile.test / password123
agen@mobile.test / password123
umum@mobile.test / password123
```

## 🎯 Expected Results:

### ✅ Basic Test:
```json
{
    "success": true,
    "message": "API Test endpoint working",
    "timestamp": "2025-08-22T09:45:52.330129Z",
    "server": "Laravel 12.25.0"
}
```

### ✅ Login Success:
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user_type": "staff",
        "user": {
            "name": "Kepala Gudang Mobile",
            "roles": ["kepala_gudang"]
        },
        "token": "TOKEN_AKAN_MUNCUL_DISINI"
    }
}
```

## 🔧 Jika Ada Masalah:

### Login Error (401):
- User mungkin belum dibuat
- Run: `php artisan db:seed --class=MobileUsersSeeder`

### Server Error (500):
- Check: `storage/logs/laravel.log`
- Restart server: Ctrl+C, then `php artisan serve`

## 📱 Ready for Flutter Integration!

API format sudah sesuai untuk Flutter dengan:
- Consistent JSON response
- Token-based auth
- Role-based access control
- Error handling

### Flutter HTTP Example:
```dart
final response = await http.post(
  Uri.parse('http://127.0.0.1:8000/api/v1/auth/login'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({
    'email': 'kepala.gudang@mobile.test',
    'password': 'password123', 
    'user_type': 'staff'
  })
);

if (response.statusCode == 200) {
  final data = jsonDecode(response.body);
  final token = data['data']['token'];
  // Save token for future requests
}
```

**🎉 API siap digunakan untuk development Flutter!**
