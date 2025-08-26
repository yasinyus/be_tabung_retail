# 🚀 PANDUAN TESTING API TERIMA TABUNG ARMADA

## ⚙️ Setup Awal

### 1. Server Status
✅ Server Laravel berjalan di: `http://192.168.1.7:8000`
✅ Database: MySQL (tabung_activity table sudah dibuat)
✅ Authentication: Laravel Sanctum

### 2. User Testing
- **Email**: `gudang@gmail.com`
- **Password**: `gudang`
- **Role**: `kepala_gudang`

---

## 🎯 TESTING DI POSTMAN

### Metode 1: Import Collection
1. Download file: `Postman_Collection_TabungRetail.json`
2. Buka Postman → Import → Upload file tersebut
3. Collection "API Tabung Retail" akan muncul
4. Jalankan request sesuai urutan (1-6)

### Metode 2: Manual Setup

#### Step 1: Test API Dasar
```
GET http://192.168.1.7:8000/api/v1/test
Headers:
- Accept: application/json
```
**Expected Response:**
```json
{
  "status": "success",
  "message": "API V1 is working!",
  "endpoints": [...]
}
```

#### Step 2: Login
```
POST http://192.168.1.7:8000/api/v1/auth/login
Headers:
- Content-Type: application/json
- Accept: application/json

Body (JSON):
{
  "email": "gudang@gmail.com",
  "password": "gudang"
}
```
**Expected Response:**
```json
{
  "status": "success",
  "message": "Login successful",
  "user_type": "admin",
  "user": {
    "id": 7,
    "name": "Kepala Gudang Mobile",
    "email": "gudang@gmail.com",
    "roles": "kepala_gudang"
  },
  "token": "77|VS9NI5esslv9CGndcqUZEFjHexoAIQ0vOGSU4wHG6b6856a6"
}
```
**⚠️ PENTING: Salin token untuk request selanjutnya!**

#### Step 3: Test Profile
```
GET http://192.168.1.7:8000/api/v1/auth/profile
Headers:
- Authorization: Bearer [TOKEN_DARI_LOGIN]
- Accept: application/json
```

#### Step 4: Test Dashboard
```
GET http://192.168.1.7:8000/api/v1/mobile/dashboard
Headers:
- Authorization: Bearer [TOKEN_DARI_LOGIN]
- Accept: application/json
```

#### Step 5: Terima Tabung Armada (SUCCESS)
```
POST http://192.168.1.7:8000/api/v1/mobile/terima-tabung
Headers:
- Authorization: Bearer [TOKEN_DARI_LOGIN]
- Content-Type: application/json
- Accept: application/json

Body (JSON):
{
  "lokasi_qr": "GDG-001",
  "armada_qr": "ARM-001",
  "tabung_qr": ["TBG-001", "TBG-002", "TBG-003"],
  "keterangan": "Test penerimaan tabung dari Postman"
}
```
**Expected Response:**
```json
{
  "status": "success",
  "message": "Data berhasil disimpan! 3 tabung telah diterima.",
  "data": {
    "transaksi_id": "TRX-20250825034512",
    "tanggal": "25-08-2025",
    "lokasi_qr": "GDG-001",
    "armada_qr": "ARM-001",
    "total_tabung": 3,
    "nama_user": "Kepala Gudang Mobile",
    "keterangan": "Test penerimaan tabung dari Postman",
    "status_transaksi": "berhasil",
    "id_aktivitas": 1
  },
  "notification": {
    "title": "Tabung Berhasil Diterima",
    "message": "Sejumlah 3 tabung telah berhasil diterima dari armada.",
    "type": "success"
  }
}
```

#### Step 6: Test Validasi QR Invalid
```
POST http://192.168.1.7:8000/api/v1/mobile/terima-tabung
Headers:
- Authorization: Bearer [TOKEN_DARI_LOGIN]
- Content-Type: application/json
- Accept: application/json

Body (JSON):
{
  "lokasi_qr": "INVALID-QR",
  "armada_qr": "ARM-001",
  "tabung_qr": ["TBG-001"],
  "keterangan": "Test dengan QR invalid"
}
```
**Expected Response:**
```json
{
  "status": "error",
  "message": "QR Code Gudang tidak valid"
}
```

---

## 📋 FORMAT QR CODE YANG VALID

| Type | Format | Contoh Valid | Contoh Invalid |
|------|--------|-------------|----------------|
| **Gudang** | `GDG-XXX` | GDG-001, GDG-999 | GDG-1, GUDANG-001 |
| **Armada** | `ARM-XXX` | ARM-001, ARM-999 | ARM-1, ARMADA-001 |
| **Tabung** | `TBG-XXX` | TBG-001, TBG-999 | TBG-1, TABUNG-001 |

*XXX = 3 digit angka (001-999)*

---

## 🔧 TROUBLESHOOTING

### Error 500 Internal Server Error
1. Cek server masih berjalan: `http://192.168.1.7:8000/api/v1/test`
2. Cek token masih valid (login ulang jika perlu)
3. Pastikan Headers benar:
   - `Authorization: Bearer [TOKEN]`
   - `Content-Type: application/json`
   - `Accept: application/json`

### Token Expired/Invalid
- Login ulang untuk mendapatkan token baru
- Token berubah setiap kali server restart

### QR Code Validation Failed
- Pastikan format QR Code sesuai dengan pattern:
  - Gudang: `GDG-001` ✅, `gudang-001` ❌
  - Armada: `ARM-001` ✅, `armada-001` ❌
  - Tabung: `TBG-001` ✅, `tabung-001` ❌

---

## 📊 DATABASE

Data yang berhasil disimpan akan masuk ke table `tabung_activity` dengan struktur:
- `id` - Auto increment ID
- `activity` - "Terima Tabung"
- `nama_user` - Nama user yang login
- `qr_tabung` - JSON array QR code tabung
- `lokasi_gudang` - QR code gudang
- `armada` - QR code armada  
- `keterangan` - Keterangan dari user
- `status` - "berhasil"
- `user_id` - ID user yang login
- `transaksi_id` - Unique transaction ID
- `tanggal_aktivitas` - Tanggal aktivitas
- `created_at` & `updated_at` - Timestamps

---

## ✅ CHECKLIST TESTING

- [ ] API basic test berhasil
- [ ] Login berhasil & dapat token
- [ ] Profile endpoint berfungsi
- [ ] Dashboard endpoint berfungsi
- [ ] Terima tabung dengan QR valid berhasil
- [ ] Validasi QR invalid berfungsi
- [ ] Data tersimpan di database
- [ ] Response JSON sesuai format

---

## 🎉 STATUS IMPLEMENTASI

✅ **Universal Login API** - Login otomatis detect user type  
✅ **QR Code Validation** - Validasi format GDG/ARM/TBG  
✅ **Database Integration** - Data tersimpan ke tabung_activity  
✅ **API Response** - JSON response lengkap dengan notification  
✅ **Error Handling** - Proper error response untuk invalid input  
✅ **Authentication** - Laravel Sanctum token-based auth  

**API SIAP UNTUK PRODUCTION! 🚀**
