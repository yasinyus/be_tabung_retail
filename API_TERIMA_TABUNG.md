# 📋 API Terima Tabung Armada Documentation

## 🎯 Endpoint
```
POST /api/v1/mobile/terima-tabung
```

## 🔐 Authentication
- Requires Bearer Token (dari endpoint login)
- Header: `Authorization: Bearer {token}`

## 📥 Request Body
```json
{
    "lokasi_qr": "GDG-001",
    "armada_qr": "ARM-001", 
    "tabung_qr": [
        "TBG-001",
        "TBG-002", 
        "TBG-003"
    ],
    "keterangan": "Tabung dalam kondisi baik" // Optional
}
```

## 📤 Response Success (200)
```json
{
    "status": "success",
    "message": "Data berhasil disimpan! 3 tabung telah diterima.",
    "data": {
        "transaksi_id": "TRX-20250825024813",
        "tanggal": "25-08-2025",
        "lokasi_qr": "GDG-001",
        "armada_qr": "ARM-001",
        "total_tabung": 3,
        "nama_user": "Driver Mobile",
        "keterangan": "Tabung dalam kondisi baik",
        "status_transaksi": "berhasil"
    },
    "notification": {
        "title": "Tabung Berhasil Diterima",
        "message": "Sejumlah 3 tabung telah berhasil diterima dari armada.",
        "type": "success"
    }
}
```

## ❌ Response Error (400)
```json
{
    "status": "error",
    "message": "QR Code Gudang tidak valid"
}
```

```json
{
    "status": "error",
    "message": "QR Code Tabung tidak valid",
    "invalid_tabung": [2, 3] // Index tabung yang tidak valid
}
```

## 📝 Field Descriptions

### Input Fields:
- **lokasi_qr** (required): QR Code Gudang (format: GDG-XXX)
- **armada_qr** (required): QR Code Armada (format: ARM-XXX)
- **tabung_qr** (required): Array QR Code Tabung (format: TBG-XXX)
- **keterangan** (optional): Catatan tambahan (max 500 karakter)

### Auto-Generated Fields:
- **tanggal**: Format DD-MM-YYYY (otomatis dari sistem)
- **nama_user**: Diambil dari user yang login
- **total_tabung**: Count dari array tabung_qr

## 🔍 QR Code Format Validation

| Type | Format | Example |
|------|--------|---------|
| Gudang | GDG-XXX | GDG-001 |
| Armada | ARM-XXX | ARM-001 |
| Tabung | TBG-XXX | TBG-001 |

## 📱 Mobile App Flow

1. **Scan QR Gudang** → Isi `lokasi_qr`
2. **Scan QR Armada** → Isi `armada_qr`  
3. **Scan Multiple QR Tabung** → Isi array `tabung_qr`
4. **Input Keterangan** (opsional)
5. **Submit** → Kirim ke API
6. **Show Notification** → Tampilkan response notification

## 🧪 Test dengan PowerShell
```bash
# Jalankan test script
powershell -ExecutionPolicy Bypass -File "test-terima-tabung.ps1"
```

## 🧪 Test dengan cURL
```bash
# 1. Login dulu
curl -X POST http://192.168.1.7:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"driver@gmail.com","password":"password"}'

# 2. Gunakan token untuk terima tabung
curl -X POST http://192.168.1.7:8000/api/v1/mobile/terima-tabung \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "lokasi_qr": "GDG-001",
    "armada_qr": "ARM-001",
    "tabung_qr": ["TBG-001", "TBG-002"],
    "keterangan": "Test terima tabung"
  }'
```

## 📊 Business Logic

1. **Validasi Authentication** - User harus login
2. **Validasi QR Code** - Semua QR harus format yang benar
3. **Auto Generate Data**:
   - Tanggal otomatis (DD-MM-YYYY)
   - Nama user dari login
   - Total tabung dari count array
   - Transaksi ID unik
4. **Response Notification** - Untuk ditampilkan di mobile app

## 🔄 Future Enhancements

- [ ] Integrasi dengan database real (Model TerimaTabung)
- [ ] Validasi QR dengan database (cek apakah QR exist)
- [ ] Update stok tabung di gudang
- [ ] History transaksi
- [ ] Push notification
- [ ] Export report
