# 🚀 Quick Setup Postman - Tabung Retail API

## 📥 **Import ke Postman (Super Easy!)**

### **Method 1: Import Collection & Environment (Recommended)**

1. **Buka Postman**
2. **Import Collection:**
   - File > Import
   - Pilih: `Tabung_Retail_API_Collection.postman_collection.json`
   - Klik "Import"

3. **Import Environment:**
   - File > Import  
   - Pilih: `Tabung_Retail_Live.postman_environment.json`
   - Klik "Import"

4. **Set Active Environment:**
   - Pilih "Tabung Retail Live Environment" dari dropdown di kanan atas

---

## ⚡ **Quick Test Flow**

### **1. Test Public Endpoint**
- Run: `🔓 Authentication > Test Public Endpoint`
- Expected: ✅ 200 OK

### **2. Login & Auto Token Save**
- Run: `🔓 Authentication > 🔐 Login Staff - Kepala Gudang (No Role)`
- Expected: ✅ Token auto-saved, Role auto-detected

### **3. Test Protected Endpoints**
- Run: `📊 Protected Endpoints > Get Tabung Data`
- Expected: ✅ Data loaded with token

### **4. Security Tests**
- Run: `🔒 Security Tests > ❌ Invalid Credentials`
- Expected: ✅ Properly rejected

---

## 🎯 **Key Testing Points**

### **✅ New API Features (No Role Parameter):**
```json
// ✅ Request format (simplified)
{
  "email": "kepala_gudang@tabungretail.com",
  "password": "admin123"
  // No role parameter needed!
}

// ✅ Response includes auto-detected role
{
  "status": "success",
  "user": {
    "role": "kepala_gudang"  // Auto-detected!
  },
  "token": "1|abc123..."
}
```

### **🔧 Auto Features:**
- ✅ **Token Auto-Save:** Script otomatis save token ke environment
- ✅ **Role Auto-Detection:** Server otomatis detect role dari database
- ✅ **Auto Tests:** Setiap request include validation tests
- ✅ **Auto Cleanup:** Logout otomatis clear token

---

## 📋 **Collection Structure**

```
📁 Tabung Retail API - Simplified (No Role)
├── 🔓 Authentication
│   ├── Test Public Endpoint
│   ├── 🔐 Login Staff - Kepala Gudang (No Role)
│   ├── 🔐 Login Staff - Operator (No Role)
│   ├── 🔐 Login Staff - Admin (No Role)
│   ├── 👤 Login Customer (No Role)
│   └── 🚪 Logout
├── 📊 Protected Endpoints  
│   ├── Get Tabung Data
│   ├── Get Armada Data
│   ├── Get Gudang Data
│   └── Get Pelanggan Data
└── 🔒 Security Tests
    ├── ❌ Invalid Credentials
    ├── ❌ Access Without Token
    └── ❌ Invalid Token
```

---

## 🧪 **Test Credentials**

### **Staff Accounts:**
```
Email: kepala_gudang@tabungretail.com
Password: admin123
Expected Role: kepala_gudang (auto-detected)

Email: operator@tabungretail.com  
Password: admin123
Expected Role: operator (auto-detected)

Email: admin@tabungretail.com
Password: admin123
Expected Role: admin (auto-detected)
```

### **Customer Account:**
```
Email: pelanggan@test.com
Password: password123
Expected: Customer data with kode_pelanggan
```

---

## 🎯 **Environment Variables**

| Variable | Value | Usage |
|----------|-------|-------|
| `base_url` | `https://test.gasalamsolusi.my.id/api` | API Base URL |
| `token` | (auto-filled) | Authentication Token |
| `admin_url` | `https://test.gasalamsolusi.my.id/admin` | Admin Panel |
| `homepage_url` | `https://test.gasalamsolusi.my.id` | Homepage |

---

## 🚀 **Advanced Testing**

### **Run Collection Tests:**
1. Klik Collection "Tabung Retail API"
2. Klik "Run" 
3. Select semua requests
4. Klik "Run Tabung Retail API"
5. Watch automated testing! 🔥

### **Expected Results:**
- ✅ 14/14 tests pass
- ✅ All endpoints working
- ✅ Security measures active
- ✅ Auto role detection working

---

## 🆕 **What's New (API Simplified)**

### **Before vs After:**

| Feature | OLD (with role) | NEW (no role) |
|---------|----------------|---------------|
| **Request** | `email + password + role` | `email + password` |
| **Security** | Role can be manipulated | Role auto-detected |
| **Integration** | Complex for Flutter | Simple for Flutter |
| **Validation** | Manual role checking | Database role checking |

### **Benefits:**
- ✅ **Simpler Integration:** Flutter development easier
- ✅ **Better Security:** No role manipulation possible  
- ✅ **Auto Detection:** Role determined by database
- ✅ **Modern Standard:** Follows industry best practices
- ✅ **Error Reduction:** Less chance of wrong role errors

---

## 🔗 **Quick Links**

- **🌐 Homepage:** https://test.gasalamsolusi.my.id
- **⚙️ Admin Panel:** https://test.gasalamsolusi.my.id/admin  
- **🧪 API Test:** https://test.gasalamsolusi.my.id/api/test
- **📚 Documentation:** See `POSTMAN_TESTING_GUIDE.md`

---

## 🎉 **Ready to Test!**

1. **Import files ke Postman** ✅
2. **Set environment active** ✅  
3. **Run login test** ✅
4. **Verify role auto-detection** ✅
5. **Test protected endpoints** ✅
6. **Run security tests** ✅

**🎯 Happy Testing! API sekarang super simple dan secure!**
