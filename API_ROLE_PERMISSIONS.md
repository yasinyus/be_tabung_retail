# API Role Permissions - Terima Tabung Endpoint

## 📋 Overview

Dokumentasi lengkap tentang role dan permission yang diizinkan untuk mengakses endpoint `api/v1/mobile/terima-tabung`.

## 🔐 Endpoint Details

**URL:** `POST /api/v1/mobile/terima-tabung`  
**Authentication:** Required (Bearer Token)  
**Middleware:** `auth:sanctum`

## 👥 Role yang Diizinkan

### ✅ **SEMUA ROLE DIIZINKAN** (Current Configuration)

Berdasarkan analisis kode, endpoint `api/v1/mobile/terima-tabung` **TIDAK MEMILIKI RESTRIKSI ROLE KHUSUS**. Semua user yang berhasil login dapat mengakses endpoint ini.

### User Types yang Dapat Mengakses:

#### 1. **Admin Users** (User Model)
Semua role admin dapat mengakses:
- `admin_utama` ✅
- `admin_umum` ✅
- `kepala_gudang` ✅
- `operator_retail` ✅
- `driver` ✅
- `user` ✅
- **Role lainnya** ✅

#### 2. **Customer Users** (Pelanggan Model)
Semua pelanggan dapat mengakses:
- `pelanggan` (jenis: `umum`) ✅
- `pelanggan` (jenis: `agen`) ✅

## 🔍 Analisis Kode

### Route Configuration
```php
// routes/api.php
Route::prefix('mobile')->middleware('auth:sanctum')->group(function () {
    Route::post('terima-tabung', [AuthController::class, 'terimaTabung']);
});
```

**Kesimpulan:** Hanya memerlukan autentikasi (`auth:sanctum`), tidak ada middleware role khusus.

### Controller Logic
```php
// app/Http/Controllers/Api/AuthController.php
public function terimaTabung(Request $request)
{
    $user = $request->user();
    
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized'
        ], 401);
    }
    
    // Tidak ada pengecekan role tambahan
    // Lanjut ke proses terima tabung...
}
```

**Kesimpulan:** Controller hanya mengecek apakah user sudah login, tidak ada validasi role.

## 🚨 Security Considerations

### Current State: **PERMISSIVE**
- ✅ Semua user yang login dapat mengakses
- ✅ Tidak ada pembatasan berdasarkan role
- ✅ Admin dan pelanggan memiliki akses yang sama

### Recommended Security: **RESTRICTIVE**
Jika ingin membatasi akses, tambahkan middleware role:

```php
// routes/api.php (Recommended)
Route::prefix('mobile')->middleware(['auth:sanctum', 'api.role:admin_utama,admin_umum,kepala_gudang,operator_retail,driver'])->group(function () {
    Route::post('terima-tabung', [AuthController::class, 'terimaTabung']);
});
```

## 📊 Role Hierarchy (Jika Diterapkan)

### 🔴 **High Priority Roles** (Full Access)
- `admin_utama` - Super admin
- `admin_umum` - General admin
- `kepala_gudang` - Warehouse manager

### 🟡 **Medium Priority Roles** (Limited Access)
- `operator_retail` - Retail operator
- `driver` - Delivery driver

### 🟢 **Low Priority Roles** (Read Only)
- `user` - Basic user
- `pelanggan` - Customer (read only)

## 🧪 Testing Role Access

### Test dengan Admin User
```bash
# Login sebagai admin
curl -X POST https://your-domain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Test terima-tabung dengan token admin
curl -X POST https://your-domain.com/api/v1/mobile/terima-tabung \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "lokasi_qr": "GDG-001",
    "armada_qr": "ARM-001",
    "tabung_qr": ["T-001", "T-002"],
    "keterangan": "Test admin access"
  }'
```

### Test dengan Pelanggan User
```bash
# Login sebagai pelanggan
curl -X POST https://your-domain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"pelanggan@example.com","password":"password"}'

# Test terima-tabung dengan token pelanggan
curl -X POST https://your-domain.com/api/v1/mobile/terima-tabung \
  -H "Authorization: Bearer PELANGGAN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "lokasi_qr": "GDG-001",
    "armada_qr": "ARM-001",
    "tabung_qr": ["T-001", "T-002"],
    "keterangan": "Test pelanggan access"
  }'
```

## 🔧 Implementasi Role Restriction (Optional)

### 1. Tambahkan Middleware Role
```php
// routes/api.php
Route::prefix('mobile')->middleware(['auth:sanctum', 'api.role:admin_utama,admin_umum,kepala_gudang,operator_retail,driver'])->group(function () {
    Route::post('terima-tabung', [AuthController::class, 'terimaTabung']);
});
```

### 2. Tambahkan Role Check di Controller
```php
// app/Http/Controllers/Api/AuthController.php
public function terimaTabung(Request $request)
{
    $user = $request->user();
    
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized'
        ], 401);
    }
    
    // Role check untuk admin users
    if ($user instanceof User) {
        $allowedRoles = ['admin_utama', 'admin_umum', 'kepala_gudang', 'operator_retail', 'driver'];
        if (!in_array($user->role, $allowedRoles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient permissions'
            ], 403);
        }
    }
    
    // Role check untuk pelanggan (optional)
    if ($user instanceof Pelanggan) {
        return response()->json([
            'status' => 'error',
            'message' => 'Customers cannot perform this action'
        ], 403);
    }
    
    // Lanjut ke proses terima tabung...
}
```

## 📋 Summary

### Current State
- **Access Level:** PERMISSIVE
- **Admin Users:** ✅ All roles allowed
- **Customer Users:** ✅ All customers allowed
- **Security:** Basic authentication only

### Recommended State
- **Access Level:** RESTRICTIVE
- **Admin Users:** ✅ Specific roles only
- **Customer Users:** ❌ No access
- **Security:** Role-based access control

## 🎯 Conclusion

**Endpoint `api/v1/mobile/terima-tabung` saat ini dapat diakses oleh SEMUA user yang berhasil login, tanpa pembatasan role.**

Jika ingin meningkatkan keamanan, implementasikan role restriction sesuai rekomendasi di atas.
