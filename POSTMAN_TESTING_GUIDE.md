# 🚀 Panduan Testing API di Postman
## Tabung Retail API - Simplified (No Role Parameter)

### 📋 **Setup Postman Environment**

1. **Buka Postman** dan buat Environment baru:
   - Name: `Tabung Retail Live`
   - Base URL: `https://test.gasalamsolusi.my.id/api`

2. **Environment Variables:**
   ```
   base_url: https://test.gasalamsolusi.my.id/api
   token: (akan diisi otomatis setelah login)
   ```

---

## 🔥 **Collection Testing Steps**

### **1. Test Public Endpoint**

**Method:** `GET`  
**URL:** `{{base_url}}/test`  
**Headers:** None required  

**Expected Response:**
```json
{
  "message": "API is working!",
  "timestamp": "2024-12-19T10:30:00Z"
}
```

---

### **2. Staff Authentication (Simplified - No Role Parameter)**

#### **A. Login Kepala Gudang**

**Method:** `POST`  
**URL:** `{{base_url}}/login-staff`  
**Headers:**
```
Content-Type: application/json
```

**Body (raw JSON):**
```json
{
  "email": "kepala_gudang@tabungretail.com",
  "password": "admin123"
}
```

**Expected Response:**
```json
{
  "status": "success",
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Kepala Gudang",
    "email": "kepala_gudang@tabungretail.com",
    "role": "kepala_gudang"
  },
  "token": "1|abcd1234567890..."
}
```

**📝 Postman Script (Tests Tab):**
```javascript
// Auto-save token untuk request selanjutnya
if (pm.response.code === 200) {
    const response = pm.response.json();
    if (response.token) {
        pm.environment.set("token", response.token);
        pm.test("Token saved successfully", function () {
            pm.expect(response.token).to.not.be.empty;
        });
        
        pm.test("Role auto-detected", function () {
            pm.expect(response.user.role).to.exist;
            pm.expect(response.user.role).to.equal("kepala_gudang");
        });
    }
}

pm.test("Login successful", function () {
    pm.response.to.have.status(200);
    pm.expect(pm.response.json().status).to.equal("success");
});
```

#### **B. Login Operator**

**Method:** `POST`  
**URL:** `{{base_url}}/login-staff`  
**Body (raw JSON):**
```json
{
  "email": "operator@tabungretail.com",
  "password": "admin123"
}
```

#### **C. Login Admin**

**Method:** `POST`  
**URL:** `{{base_url}}/login-staff`  
**Body (raw JSON):**
```json
{
  "email": "admin@tabungretail.com",
  "password": "admin123"
}
```

---

### **3. Customer Authentication**

**Method:** `POST`  
**URL:** `{{base_url}}/login-pelanggan`  
**Headers:**
```
Content-Type: application/json
```

**Body (raw JSON):**
```json
{
  "email": "pelanggan@test.com",
  "password": "password123"
}
```

**Expected Response:**
```json
{
  "status": "success",
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Pelanggan Test",
    "email": "pelanggan@test.com",
    "kode_pelanggan": "PEL001",
    "lokasi_pelanggan": "Jakarta",
    "jenis_pelanggan": "Retail"
  },
  "token": "2|xyz9876543210..."
}
```

---

### **4. Protected Endpoints (Require Token)**

**📝 Authentication Setup:**  
Untuk semua endpoint protected, tambahkan di **Headers:**
```
Authorization: Bearer {{token}}
```

#### **A. Get Tabung Data**

**Method:** `GET`  
**URL:** `{{base_url}}/tabung`  
**Headers:**
```
Authorization: Bearer {{token}}
```

#### **B. Get Armada Data**

**Method:** `GET`  
**URL:** `{{base_url}}/armada`  
**Headers:**
```
Authorization: Bearer {{token}}
```

#### **C. Get Gudang Data**

**Method:** `GET`  
**URL:** `{{base_url}}/gudang`  
**Headers:**
```
Authorization: Bearer {{token}}
```

#### **D. Get Pelanggan Data**

**Method:** `GET`  
**URL:** `{{base_url}}/pelanggan`  
**Headers:**
```
Authorization: Bearer {{token}}
```

---

### **5. Logout**

**Method:** `POST`  
**URL:** `{{base_url}}/logout`  
**Headers:**
```
Authorization: Bearer {{token}}
```

**Postman Script (Tests Tab):**
```javascript
// Clear token after logout
if (pm.response.code === 200) {
    pm.environment.unset("token");
    pm.test("Token cleared successfully", function () {
        pm.expect(pm.environment.get("token")).to.be.undefined;
    });
}

pm.test("Logout successful", function () {
    pm.response.to.have.status(200);
});
```

---

## 🧪 **Security Testing**

### **1. Test Invalid Credentials**

**Method:** `POST`  
**URL:** `{{base_url}}/login-staff`  
**Body:**
```json
{
  "email": "invalid@email.com",
  "password": "wrongpassword"
}
```

**Expected:** HTTP 401/422 with error message

### **2. Test Access Without Token**

**Method:** `GET`  
**URL:** `{{base_url}}/tabung`  
**Headers:** (No Authorization header)

**Expected:** HTTP 401 Unauthorized

### **3. Test Invalid Token**

**Method:** `GET`  
**URL:** `{{base_url}}/tabung`  
**Headers:**
```
Authorization: Bearer invalid_token_123
```

**Expected:** HTTP 401 Unauthorized

---

## 📋 **Postman Collection Structure**

```
📁 Tabung Retail API
├── 📁 Authentication
│   ├── 🔓 Test Public Endpoint
│   ├── 🔐 Login Staff (Kepala Gudang)
│   ├── 🔐 Login Staff (Operator)
│   ├── 🔐 Login Staff (Admin)
│   ├── 🔐 Login Customer
│   └── 🚪 Logout
├── 📁 Protected Endpoints
│   ├── 📊 Get Tabung
│   ├── 🚛 Get Armada
│   ├── 🏪 Get Gudang
│   └── 👥 Get Pelanggan
└── 📁 Security Tests
    ├── ❌ Invalid Credentials
    ├── ❌ No Token Access
    └── ❌ Invalid Token
```

---

## 🎯 **Testing Workflow**

### **Step 1: Environment Setup**
1. Import environment atau buat manual
2. Set `base_url` = `https://test.gasalamsolusi.my.id/api`

### **Step 2: Test Public Access**
1. Run "Test Public Endpoint"
2. Verify response 200 OK

### **Step 3: Authentication Test**
1. Run "Login Staff (Kepala Gudang)"
2. Verify token auto-saved di environment
3. Verify role auto-detected di response

### **Step 4: Protected Endpoints**
1. Run semua endpoint di folder "Protected Endpoints"
2. Verify semua return data dengan token

### **Step 5: Security Validation**
1. Run semua test di folder "Security Tests"
2. Verify proper error responses

### **Step 6: Cleanup**
1. Run "Logout"
2. Verify token cleared dari environment

---

## 🆕 **Key Differences (New API)**

### **❌ OLD (dengan role parameter):**
```json
{
  "email": "kepala_gudang@tabungretail.com",
  "password": "admin123",
  "role": "kepala_gudang"  // Required
}
```

### **✅ NEW (tanpa role parameter):**
```json
{
  "email": "kepala_gudang@tabungretail.com",
  "password": "admin123"
  // Role auto-detected dari database!
}
```

**Benefits:**
- ✅ Request lebih simple
- ✅ Tidak bisa manipulasi role dari frontend
- ✅ Auto role detection dari database
- ✅ Better security
- ✅ Flutter-friendly format

---

## 📱 **Import ke Postman**

### **Method 1: Manual Setup**
1. Buat Collection baru: "Tabung Retail API"
2. Copy-paste semua request dari guide ini
3. Setup environment variables

### **Method 2: Import Collection File**
1. Save konfigurasi ini sebagai `.json` file
2. Import ke Postman via File > Import

### **Method 3: Generate dari curl**
```bash
# Import dari curl command
curl -X POST https://test.gasalamsolusi.my.id/api/login-staff \
  -H "Content-Type: application/json" \
  -d '{"email":"kepala_gudang@tabungretail.com","password":"admin123"}'
```

---

## 🎉 **Testing Checklist**

- [ ] ✅ Public endpoint accessible
- [ ] ✅ Staff login working (no role parameter)
- [ ] ✅ Role auto-detection working
- [ ] ✅ Customer login working
- [ ] ✅ Token auto-saved in environment
- [ ] ✅ Protected endpoints require authentication
- [ ] ✅ All data endpoints return proper data
- [ ] ✅ Invalid credentials rejected
- [ ] ✅ Security measures working
- [ ] ✅ Logout clears token
- [ ] ✅ Auto role detection from database

---

## 🔗 **Quick Links**

- **API Base URL:** `https://test.gasalamsolusi.my.id/api`
- **Admin Panel:** `https://test.gasalamsolusi.my.id/admin`
- **Test Endpoint:** `https://test.gasalamsolusi.my.id/api/test`

**🎯 Happy Testing! API sekarang lebih simple dan secure dengan auto role detection!**
