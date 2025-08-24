# 🔄 API Changes: Removed Role Parameter from Login

## ⚡ **What Changed**

### Before (OLD):
```json
POST /api/login-staff
{
  "email": "kepala_gudang@tabungretail.com",
  "password": "admin123",
  "role": "kepala_gudang"  // ❌ Required role parameter
}
```

### After (NEW):
```json
POST /api/login-staff
{
  "email": "kepala_gudang@tabungretail.com",
  "password": "admin123"
  // ✅ No role parameter needed - auto-detected from database
}
```

---

## 🔧 **Updated API Endpoints**

### Authentication (No Role Required):
- `POST /api/login-staff` - Login untuk semua staff (admin, kepala_gudang, operator)
- `POST /api/login-pelanggan` - Login untuk customer
- `POST /api/logout` - Logout (requires token)

### Protected Data Endpoints:
- `GET /api/tabung` - List tabung
- `GET /api/armada` - List armada  
- `GET /api/gudang` - List gudang
- `GET /api/pelanggan` - List pelanggan

---

## 📋 **New Response Format**

### Staff Login Response:
```json
{
  "status": "success",
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Kepala Gudang",
    "email": "kepala_gudang@tabungretail.com",
    "role": "kepala_gudang"  // ✅ Role automatically detected
  },
  "token": "1|abc123..."
}
```

### Customer Login Response:
```json
{
  "status": "success", 
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Customer Name",
    "email": "pelanggan@test.com",
    "kode_pelanggan": "PEL001",
    "lokasi_pelanggan": "Jakarta",
    "jenis_pelanggan": "Retail"
  },
  "token": "1|abc123..."
}
```

### Error Response:
```json
{
  "status": "error",
  "message": "Invalid credentials"
}
```

---

## 🚀 **Benefits of Changes**

1. **Simpler Integration**: No need to specify role in login request
2. **Auto Role Detection**: System automatically determines user role from database
3. **Better Security**: Role cannot be manipulated in request
4. **Cleaner Code**: Simplified authentication flow
5. **Future Proof**: Easier to add new roles without changing API

---

## 🧪 **Testing the New API**

### Using curl:
```bash
# Staff login (no role parameter)
curl -X POST https://test.gasalamsolusi.my.id/api/login-staff \
  -H "Content-Type: application/json" \
  -d '{"email":"kepala_gudang@tabungretail.com","password":"admin123"}'

# Customer login
curl -X POST https://test.gasalamsolusi.my.id/api/login-pelanggan \
  -H "Content-Type: application/json" \
  -d '{"email":"pelanggan@test.com","password":"password123"}'

# Use token for protected endpoints
curl -H "Authorization: Bearer YOUR_TOKEN" \
  https://test.gasalamsolusi.my.id/api/tabung
```

### Using Flutter:
```dart
// Staff login - no role parameter needed
final response = await http.post(
  Uri.parse('https://test.gasalamsolusi.my.id/api/login-staff'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({
    'email': 'kepala_gudang@tabungretail.com',
    'password': 'admin123',
    // No role parameter needed!
  }),
);

final data = jsonDecode(response.body);
if (data['status'] == 'success') {
  String token = data['token'];
  String userRole = data['user']['role']; // Auto-detected role
  String userName = data['user']['name'];
}
```

---

## 📱 **Updated Test Scripts**

All testing scripts have been updated:
- ✅ `test-api-live.sh` - Auto-detects role from response
- ✅ `test-api-live.ps1` - Updated response parsing
- ✅ `api-tests-live.http` - Simplified request format
- ✅ `Tabung_Retail_API_Live.postman_collection.json` - No role parameter

---

## 🔄 **Migration Guide**

### For existing Flutter apps:

1. **Remove role parameter** from login requests
2. **Update response parsing** to new format
3. **Get role from response** instead of storing separately

### Old code:
```dart
body: jsonEncode({
  'email': email,
  'password': password,
  'role': selectedRole,  // ❌ Remove this
}),
```

### New code:
```dart
body: jsonEncode({
  'email': email,
  'password': password,  // ✅ Only email and password
}),

// Get role from response
final role = jsonDecode(response.body)['user']['role'];
```

---

## ✅ **Backward Compatibility**

- Old `/api/v1/auth/login` endpoint still works with role parameter
- New direct endpoints (`/api/login-staff`, `/api/login-pelanggan`) don't require role
- Choose the endpoint that fits your needs

**🎉 Simplified API ready for testing!**
