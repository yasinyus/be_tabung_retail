# Debug API dengan VS Code REST Client

## Test in VS Code

Buka file `api-tests.http` di VS Code dan jalankan request berikut:

### 1. Login (Copy token dari response)
```http
POST http://127.0.0.1:8000/api/v1/auth/login
Content-Type: application/json

{
    "email": "kepala.gudang@mobile.test",
    "password": "password123",
    "user_type": "staff"
}
```

### 2. Test Basic Endpoint
```http
GET http://127.0.0.1:8000/api/test
```

### 3. Test Auth Endpoint  
```http
GET http://127.0.0.1:8000/api/test-auth
Authorization: Bearer YOUR_TOKEN_HERE
```

## ✅ API Status:
- ✅ Server running di http://127.0.0.1:8000
- ✅ Login endpoint bekerja
- ✅ Token generation bekerja  
- ❌ Sanctum authentication middleware bermasalah

## 🔍 Debugging Steps:

1. Gunakan VS Code REST Client extension
2. Buka file `api-tests.http`
3. Update @authToken dengan token real dari login
4. Test semua endpoint satu per satu
5. Lihat response code dan message

## 🎯 Expected Results:

### Login Success:
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "token": "2|dx6vYIclGcJQa60ENWUZkIQe8CTccsnjKqcnvsME25434c57d"
    }
}
```

### Dashboard Success (setelah login):
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

## 📱 Flutter Integration Ready:
API sudah siap untuk Flutter dengan format response yang konsisten!
