# API Terima Tabung - Working Documentation

## Status: ✅ BERFUNGSI DENGAN BAIK

API endpoint `api/v1/mobile/terima-tabung` sudah berfungsi dengan baik dan dapat diakses.

## Endpoint Details

**URL:** `POST /api/v1/mobile/terima-tabung`  
**Authentication:** Required (Bearer Token)  
**Content-Type:** `application/json`

## Authentication

Sebelum mengakses endpoint ini, Anda harus login terlebih dahulu:

```bash
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password"
}
```

Response login akan memberikan token yang harus digunakan untuk request terima-tabung:

```json
{
    "status": "success",
    "message": "Login successful",
    "user_type": "admin",
    "user": {
        "id": 19,
        "name": "Admin User",
        "email": "admin@example.com",
        "roles": "admin"
    },
    "token": "120|ZPRWPigsdafnzr0s7yUB4EpYrIJMIg6lW2Q9WDDP205cde90"
}
```

## Request Format

```bash
POST /api/v1/mobile/terima-tabung
Authorization: Bearer {token}
Content-Type: application/json

{
    "lokasi_qr": "GDG-001",
    "armada_qr": "ARM-001", 
    "tabung_qr": ["T-001", "T-002"],
    "keterangan": "Test terima tabung"
}
```

### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `lokasi_qr` | string | ✅ | QR Code Gudang |
| `armada_qr` | string | ✅ | QR Code Armada |
| `tabung_qr` | array | ✅ | Array QR Code Tabung (min 1) |
| `keterangan` | string | ❌ | Keterangan tambahan (max 500 chars) |

## Response Format

### Success Response (200)

```json
{
    "status": "success",
    "message": "Data berhasil disimpan! 2 tabung telah diterima.",
    "data": {
        "transaksi_id": "TRX-20250828084644",
        "tanggal": "28-08-2025",
        "lokasi_qr": "GDG-001",
        "armada_qr": "ARM-001",
        "total_tabung": 2,
        "nama_user": "Admin User",
        "keterangan": "Test terima tabung",
        "status_transaksi": "Isi",
        "id_aktivitas": 8
    },
    "notification": {
        "title": "Tabung Berhasil Diterima",
        "message": "Sejumlah 2 tabung telah berhasil diterima dari armada.",
        "type": "success"
    }
}
```

### Error Responses

#### Unauthorized (401)
```json
{
    "status": "error",
    "message": "Unauthenticated."
}
```

#### Validation Error (400)
```json
{
    "status": "error",
    "message": "QR Code Gudang tidak valid"
}
```

#### Server Error (500)
```json
{
    "status": "error",
    "message": "Terjadi kesalahan saat menyimpan data",
    "error": "Error details",
    "file": "File path",
    "line": "Line number"
}
```

## Testing

### Test Users Available

**Admin User:**
- Email: `admin@example.com`
- Password: `password`

**Pelanggan User:**
- Email: `pelanggan@example.com` 
- Password: `password`

### Test Commands

1. **Create test users:**
```bash
php artisan user:create-test
```

2. **Test API with authentication:**
```bash
php test_api_with_auth.php
```

3. **Test API without authentication:**
```bash
php test_api_terima_tabung.php
```

## Implementation Details

### Controller
- **File:** `app/Http/Controllers/Api/AuthController.php`
- **Method:** `terimaTabung()`

### Database
- **Table:** `tabung_activities`
- **Model:** `App\Models\TabungActivity`

### Validation
- QR Code validation untuk Gudang, Armada, dan Tabung
- Input validation untuk semua required fields
- Array validation untuk tabung_qr

### Features
- ✅ Authentication required
- ✅ QR Code validation
- ✅ Database storage
- ✅ Transaction ID generation
- ✅ User tracking
- ✅ Error handling
- ✅ Success notifications

## Troubleshooting

### Common Issues

1. **"Route [login] not defined"** - ✅ FIXED
   - Exception handler sudah diperbaiki di `bootstrap/app.php`

2. **"Unauthenticated"** - ✅ EXPECTED
   - Endpoint memerlukan token autentikasi

3. **Database connection issues** - ✅ FIXED
   - Test users sudah dibuat dengan command `user:create-test`

### Next Steps

1. ✅ API endpoint berfungsi
2. ✅ Authentication working
3. ✅ Database storage working
4. ✅ Error handling implemented
5. 🔄 Ready for mobile app integration

## Summary

API endpoint `api/v1/mobile/terima-tabung` sudah **BERFUNGSI DENGAN SEMPURNA** dan siap untuk digunakan oleh aplikasi mobile.
