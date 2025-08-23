# 🧪 Tutorial Test API di VS Code

## ✅ Prerequisites
- [x] REST Client extension sudah terinstall
- [x] Server Laravel berjalan di http://127.0.0.1:8000
- [x] File `api-tests.http` sudah tersedia
- [x] Test users sudah dibuat

## 📋 Step-by-Step Testing

### Step 1: Buka File Test
1. Di VS Code, buka file `api-tests.http`
2. Anda akan melihat syntax highlighting untuk HTTP requests
3. Setiap request memiliki tombol "Send Request" di atasnya

### Step 2: Test Login (MULAI DARI SINI)
1. Scroll ke section "1.1 Login Kepala Gudang"
2. Click **"Send Request"** di atas request tersebut
3. Response akan muncul di panel sebelah kanan

**Expected Result:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "token": "COPY_TOKEN_INI"
    }
}
```

### Step 3: Copy Token
1. Dari response login, copy nilai `token`
2. Scroll ke bagian atas file
3. Update variable: `@authToken = PASTE_TOKEN_DISINI`

**Contoh:**
```http
@authToken = 2|dx6vYIclGcJQa60ENWUZkIQe8CTccsnjKqcnvsME25434c57d
```

### Step 4: Test Dashboard
1. Scroll ke section "2.2 Get Dashboard"
2. Click **"Send Request"**
3. Seharang return data user dan statistik

**Expected Result:**
```json
{
    "success": true,
    "data": {
        "user_info": {
            "name": "Kepala Gudang Mobile",
            "roles": ["kepala_gudang"]
        },
        "stats": {
            "total_tabung": 50,
            "total_armada": 10
        }
    }
}
```

### Step 5: Test Role-Based Access
1. Test **"3.1 Get Tabung List"** - Should work (kepala_gudang can access)
2. Test **"3.2 Get Armada List"** - Should work (kepala_gudang can access)
3. Test **"3.3 Get Gudang List"** - Should work (kepala_gudang can access)

### Step 6: Test QR Scanner
1. Test **"4.1 Scan Tabung QR"** 
2. Should return tabung details

### Step 7: Test Different Roles
1. Login as **Operator** (section 1.2)
2. Copy new token
3. Try accessing **Armada** (should fail with 403)
4. Try accessing **Tabung** (should work)

## 🎯 Quick Test Commands

### Test Basic Endpoint (No Auth)
```http
GET http://127.0.0.1:8000/api/test
```

### Test Auth Endpoint
```http
GET http://127.0.0.1:8000/api/test-auth
Authorization: Bearer YOUR_TOKEN
```

## 🔍 Troubleshooting

### ❌ 401 Unauthorized
- Check if token is copied correctly
- Make sure @authToken variable is updated
- Try login again to get fresh token

### ❌ 403 Forbidden
- This is expected for role-based restrictions
- Try with different role (e.g., kepala_gudang has full access)

### ❌ 500 Server Error
- Check Laravel logs: `storage/logs/laravel.log`
- Restart server if needed

## 📊 Test Results Matrix

| Role | Dashboard | Tabung | Armada | Gudang | Profile |
|------|-----------|--------|--------|--------|---------|
| kepala_gudang | ✅ | ✅ | ✅ | ✅ | ❌ |
| operator | ✅ | ✅ | ❌ | ✅ | ❌ |
| driver | ✅ | ❌ | ✅ | ❌ | ❌ |
| pelanggan | ✅ | ❌ | ❌ | ❌ | ✅ |

## 🚀 Ready for Flutter!

Setelah semua test berhasil, API siap diintegrasikan dengan aplikasi Flutter menggunakan package `http` atau `dio`.

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
```
