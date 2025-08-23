# API Testing Guide

## Test API Endpoints dengan Postman/Insomnia

### Base URL
```
http://localhost/tabung_retail/public/api
```

## 1. Test Login Staff (Kepala Gudang)

**POST** `/v1/auth/login`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body:**
```json
{
    "email": "kepala.gudang@mobile.test",
    "password": "password123",
    "role": "kepala_gudang"
}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": ...,
            "name": "Kepala Gudang Mobile",
            "email": "kepala.gudang@mobile.test",,
            "role": "kepala_gudang"
        },
        "token": "1|...",
        "token_type": "Bearer"
    }
}
```

## 2. Test Login Pelanggan

**POST** `/v1/auth/login`

**Body:**
```json
{
    "email": "agen@mobile.test",
    "password": "password123",
    "role": "pelanggan"
}
```

## 3. Test Dashboard (dengan token dari login)

**GET** `/v1/mobile/dashboard`

**Headers:**
```
Authorization: Bearer [token dari response login]
Content-Type: application/json
Accept: application/json
```

## 4. Test Role-based Access

### Test Tabung List (kepala_gudang dan operator only)
**GET** `/v1/mobile/tabung`

**Headers:**
```
Authorization: Bearer [token kepala_gudang atau operator]
```

### Test Armada List (kepala_gudang dan driver only)
**GET** `/v1/mobile/armada`

**Headers:**
```
Authorization: Bearer [token kepala_gudang atau driver]
```

### Test Pelanggan Profile (pelanggan only)
**GET** `/v1/mobile/profile`

**Headers:**
```
Authorization: Bearer [token pelanggan]
```

## 5. Test QR Scanner

**POST** `/v1/mobile/scan-qr`

**Headers:**
```
Authorization: Bearer [any valid token]
```

**Body:**
```json
{
    "type": "tabung",
    "id": 1
}
```

## Test Cases

### Valid Scenarios:
1. ✅ Kepala Gudang dapat mengakses semua endpoint (tabung, armada, gudang)
2. ✅ Operator dapat mengakses tabung dan gudang (tidak bisa armada)
3. ✅ Driver dapat mengakses armada (tidak bisa tabung/gudang)
4. ✅ Pelanggan hanya dapat mengakses profile dan dashboard basic

### Invalid Scenarios:
1. ❌ Operator mencoba akses armada → 403 Forbidden
2. ❌ Driver mencoba akses tabung → 403 Forbidden
3. ❌ Pelanggan mencoba akses tabung → 403 Forbidden
4. ❌ Login tanpa user_type → 422 Validation Error
5. ❌ Login dengan credentials salah → 401 Unauthorized

## cURL Examples

### Login Kepala Gudang
```bash
curl -X POST http://localhost/tabung_retail/public/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "kepala.gudang@mobile.test",
    "password": "password123",
    "role": "kepala_gudang"
  }'
```

### Get Dashboard
```bash
curl -X GET http://localhost/tabung_retail/public/api/v1/mobile/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Test Unauthorized Access
```bash
curl -X GET http://localhost/tabung_retail/public/api/v1/mobile/tabung \
  -H "Authorization: Bearer DRIVER_TOKEN_HERE" \
  -H "Accept: application/json"
```

## Expected Error Messages

### 401 Unauthorized
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### 403 Forbidden
```json
{
    "success": false,
    "message": "Unauthorized - Insufficient permissions"
}
```

### 422 Validation Error
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "role": ["The role field is required."]
    }
}
```
