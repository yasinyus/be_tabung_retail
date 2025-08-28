# API Tabung Datang - Kepala Gudang

## 📋 Deskripsi

API `tabung-datang` adalah endpoint khusus untuk **Kepala Gudang** yang digunakan untuk mencatat kedatangan tabung gas di gudang. Hanya user dengan role `kepala_gudang` yang dapat mengakses endpoint ini.

## 🔧 API Endpoint

```
POST /api/v1/mobile/tabung-datang
```

## 🔐 Authentication & Authorization

- **Authentication**: Required (Bearer Token)
- **Authorization**: Role `kepala_gudang` only
- **Access Control**: Strict role validation

## 📋 Form Fields

| Field | Type | Required | Description | Format |
|-------|------|----------|-------------|---------|
| `lokasi` | string | ✅ | Scan QR Gudang | QR Code |
| `armada` | string | ✅ | Scan QR Armada | QR Code |
| `tabung_qr` | array | ✅ | Scan QR Tabung (multiple) | Array of QR Codes |
| `keterangan` | string | ❌ | Opsional (bila ada hal tidak wajar) | Text |

## 📤 Request Format

### Headers
```
Content-Type: application/json
Authorization: Bearer {token}
Accept: application/json
```

### Request Body (JSON)
```json
{
    "lokasi": "GDG-001",                    // Scan QR Gudang
    "armada": "ARM-001",                    // Scan QR Armada
    "tabung_qr": ["T-001", "T-002", "T-003"], // Scan QR Tabung (multiple)
    "keterangan": "Tabung dalam kondisi baik"  // Opsional
}
```

## 📥 Response Format

### Success Response (200)
```json
{
    "status": "success",
    "message": "Data tabung datang berhasil disimpan! 3 tabung telah tiba.",
    "data": {
        "transaksi_id": "TDG-20250128123456",
        "tanggal": "28-01-2025",
        "lokasi": "GDG-001",
        "armada": "ARM-001",
        "total": 3,
        "nama": "John Doe",
        "keterangan": "Tabung dalam kondisi baik",
        "status_transaksi": "Datang",
        "id_aktivitas": 123
    },
    "notification": {
        "title": "Tabung Datang Berhasil",
        "message": "Sejumlah 3 tabung telah berhasil tiba di gudang.",
        "type": "success"
    }
}
```

### Error Response (401 - Unauthorized)
```json
{
    "status": "error",
    "message": "Unauthorized"
}
```

### Error Response (403 - Access Denied)
```json
{
    "status": "error",
    "message": "Access denied. Only kepala_gudang can access this endpoint."
}
```

### Error Response (400 - Validation Error)
```json
{
    "status": "error",
    "message": "QR Code Lokasi Gudang tidak valid"
}
```

### Error Response (400 - Invalid Tabung)
```json
{
    "status": "error",
    "message": "QR Code Tabung tidak valid",
    "invalid_tabung": [1, 3]
}
```

### Error Response (500 - Server Error)
```json
{
    "status": "error",
    "message": "Terjadi kesalahan saat menyimpan data",
    "error": "Database connection failed",
    "file": "/path/to/file.php",
    "line": 123
}
```

## 🧪 Testing Examples

### Test 1: Without Authentication
```bash
curl -X POST http://localhost:8000/api/v1/mobile/tabung-datang \
  -H "Content-Type: application/json" \
  -d '{
    "lokasi": "GDG-001",
    "armada": "ARM-001",
    "tabung_qr": ["T-001"],
    "keterangan": "Test data"
  }'
```

**Expected Response:**
```json
{
    "status": "error",
    "message": "Unauthorized"
}
```

### Test 2: With Wrong Role
```bash
# Login as admin (not kepala_gudang)
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'

# Use token to access tabung-datang
curl -X POST http://localhost:8000/api/v1/mobile/tabung-datang \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "lokasi": "GDG-001",
    "armada": "ARM-001",
    "tabung_qr": ["T-001"],
    "keterangan": "Test data"
  }'
```

**Expected Response:**
```json
{
    "status": "error",
    "message": "Access denied. Only kepala_gudang can access this endpoint."
}
```

### Test 3: With Correct Role (kepala_gudang)
```bash
# Login as kepala_gudang
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "kepala_gudang@example.com",
    "password": "password"
  }'

# Use token to access tabung-datang
curl -X POST http://localhost:8000/api/v1/mobile/tabung-datang \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "lokasi": "GDG-001",
    "armada": "ARM-001",
    "tabung_qr": ["T-001", "T-002", "T-003"],
    "keterangan": "Tabung dalam kondisi baik"
  }'
```

**Expected Response:**
```json
{
    "status": "success",
    "message": "Data tabung datang berhasil disimpan! 3 tabung telah tiba.",
    "data": {
        "transaksi_id": "TDG-20250128123456",
        "tanggal": "28-01-2025",
        "lokasi": "GDG-001",
        "armada": "ARM-001",
        "total": 3,
        "nama": "Kepala Gudang",
        "keterangan": "Tabung dalam kondisi baik",
        "status_transaksi": "Datang",
        "id_aktivitas": 123
    },
    "notification": {
        "title": "Tabung Datang Berhasil",
        "message": "Sejumlah 3 tabung telah berhasil tiba di gudang.",
        "type": "success"
    }
}
```

## 📋 Validation Rules

| Field | Type | Required | Max Length | Format/Values |
|-------|------|----------|------------|---------------|
| `lokasi` | string | ✅ | 100 | QR code format |
| `armada` | string | ✅ | 100 | QR code format |
| `tabung_qr` | array | ✅ | - | Array of QR codes |
| `tabung_qr.*` | string | ✅ | 50 | Individual QR code |
| `keterangan` | string | ❌ | 500 | Optional text |

## 🔍 QR Code Validation

### Gudang (Warehouse)
- **Format**: `GDG-XXX` (e.g., GDG-001, GDG-002)
- **JSON Format**: `{"type": "gudang", "code": "GDG-001"}`

### Armada (Fleet)
- **Format**: Any string with 3+ characters
- **JSON Format**: `{"id": "1", "nopol": "B1234ABC"}`

### Tabung (Cylinder)
- **Format**: `T-XXX` or `TBG-XXX` (e.g., T-001, TBG-001)
- **JSON Format**: `{"id": "1", "code": "T-001"}`

## 💾 Database Storage

Data disimpan ke tabel `tabung_activities` dengan struktur:

```sql
INSERT INTO tabung_activities (
    activity,
    nama_user,
    qr_tabung,
    lokasi_gudang,
    armada,
    keterangan,
    status,
    user_id,
    transaksi_id,
    tanggal_aktivitas,
    created_at,
    updated_at
) VALUES (
    'Tabung Datang',
    'Kepala Gudang',
    '["T-001", "T-002", "T-003"]',
    'GDG-001',
    'ARM-001',
    'Tabung dalam kondisi baik',
    'Datang',
    1,
    'TDG-20250128123456',
    '2025-01-28',
    NOW(),
    NOW()
);
```

## 🎯 Key Features

- ✅ **Role-based access control** - Hanya `kepala_gudang`
- ✅ **Automatic date generation** - Format DD-MM-YYYY
- ✅ **QR code validation** - Validasi semua QR codes
- ✅ **Multiple tabung support** - Array of cylinder QR codes
- ✅ **Transaction ID generation** - Format TDG-YYYYMMDDHHMMSS
- ✅ **Database storage** - Menggunakan TabungActivity model
- ✅ **Comprehensive error handling** - Detailed error messages
- ✅ **Notification system** - Success/error notifications

## 🔐 Security Features

- **Authentication Required**: All requests must include valid token
- **Role Validation**: Strict check for `kepala_gudang` role
- **Input Validation**: All fields validated on server side
- **SQL Injection Protection**: Using Laravel Eloquent ORM
- **XSS Protection**: JSON responses properly encoded

## 📱 Mobile App Integration

### Form Flow:
1. **User opens form** → Check if user is kepala_gudang
2. **Scan QR Lokasi** → Scan QR code gudang
3. **Scan QR Armada** → Scan QR code armada
4. **Scan QR Tabung** → Scan multiple QR codes tabung
5. **Add Keterangan** → Optional notes (if needed)
6. **Submit** → Send data to API
7. **Response** → Show success/error message

### Error Handling:
- **401 Unauthorized**: User not logged in
- **403 Forbidden**: User not kepala_gudang
- **400 Validation Error**: Invalid input data
- **400 QR Error**: Invalid QR codes
- **500 Server Error**: Database or server issues

## 🧪 Testing Checklist

- [ ] Test without authentication (should return 401)
- [ ] Test with wrong role (should return 403)
- [ ] Test with correct role (should return 200)
- [ ] Test with invalid QR codes (should return 400)
- [ ] Test with empty tabung_qr array (should return 400)
- [ ] Test with valid data (should return success)
- [ ] Test database storage (check tabung_activities table)
- [ ] Test transaction ID format (TDG-YYYYMMDDHHMMSS)
- [ ] Test automatic date generation (DD-MM-YYYY format)

## 🚀 Implementation Notes

### Required User Setup:
```sql
-- Create kepala_gudang user
INSERT INTO users (name, email, password, role) VALUES (
    'Kepala Gudang',
    'kepala_gudang@example.com',
    '$2y$10$...', -- Hashed password
    'kepala_gudang'
);
```

### QR Code Generation:
- Generate QR codes for gudang, armada, and tabung
- QR codes should contain JSON data for validation
- Use consistent format across all QR codes

### Mobile App Requirements:
- Camera access for QR scanning
- Role validation before showing form
- Error handling for all API responses
- Success notification display

## 📊 Expected Workflow

1. **Kepala Gudang** opens mobile app
2. **App validates** user role is `kepala_gudang`
3. **User scans** QR codes for lokasi, armada, and tabung
4. **App sends** data to API endpoint
5. **API validates** all QR codes and user role
6. **API saves** data to database
7. **API returns** success response with transaction details
8. **App shows** success notification to user

## 🎉 Success Criteria

✅ **Only kepala_gudang can access**  
✅ **QR code validation works**  
✅ **Multiple tabung support**  
✅ **Automatic date generation**  
✅ **Database storage functional**  
✅ **Transaction ID generation**  
✅ **Comprehensive error handling**  
✅ **Role-based security enforced**
