# Tabung Retail Mobile API Documentation

## Base URL
```
http://your-domain.com/api
```

## Authentication
All protected endpoints require Bearer token authentication.

### Headers Required
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

---

## Authentication Endpoints

### 1. Login
**POST** `/v1/auth/login`

Login for staff (kepala_gudang, operator, driver) and pelanggan.

#### Request Body
```json
{
    "email": "user@example.com",
    "password": "password123",
    "user_type": "staff" // or "pelanggan"
}
```

#### Success Response (200)
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user_type": "staff", // or "pelanggan"
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "roles": ["kepala_gudang"], // only for staff
            "primary_role": "kepala_gudang", // only for staff
            "created_at": "2024-01-01T00:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

#### Error Response (401)
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### 2. Logout
**POST** `/v1/auth/logout`

Requires authentication.

#### Success Response (200)
```json
{
    "success": true,
    "message": "Logout successful"
}
```

### 3. Get Profile
**GET** `/v1/auth/profile`

Get current user profile information.

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "user_type": "staff",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "roles": ["kepala_gudang"],
            "created_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

### 4. Refresh Token
**POST** `/v1/auth/refresh`

Refresh the current access token.

#### Success Response (200)
```json
{
    "success": true,
    "message": "Token refreshed successfully",
    "data": {
        "token": "2|xyz789...",
        "token_type": "Bearer"
    }
}
```

---

## Mobile App Endpoints

### 1. Dashboard
**GET** `/v1/mobile/dashboard`

Get dashboard data for the authenticated user.

**Accessible by:** All authenticated users

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "user_info": {
            "name": "John Doe",
            "email": "john@example.com",
            "user_type": "staff",
            "roles": ["kepala_gudang"]
        },
        "stats": {
            "total_tabung": 150,
            "total_armada": 25,
            "total_gudang": 5,
            "total_pelanggan": 300
        }
    }
}
```

### 2. Get Tabung List
**GET** `/v1/mobile/tabung`

Get paginated list of tabung.

**Accessible by:** kepala_gudang, operator

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "kode_tabung": "TBG001",
                "seri_tabung": "SER001",
                "tahun": 2024,
                "keterangan": "Tabung Gas 12kg",
                "qr_code": "http://domain.com/tabung/1"
            }
        ],
        "last_page": 5,
        "per_page": 20,
        "total": 100
    }
}
```

### 3. Get Armada List
**GET** `/v1/mobile/armada`

Get paginated list of armada.

**Accessible by:** kepala_gudang, driver

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "nopol": "B1234ABC",
                "kapasitas": 50,
                "tahun": 2022,
                "qr_code": "http://domain.com/armada/1"
            }
        ],
        "last_page": 2,
        "per_page": 20,
        "total": 25
    }
}
```

### 4. Get Gudang List
**GET** `/v1/mobile/gudang`

Get paginated list of gudang.

**Accessible by:** kepala_gudang, operator

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "kode_gudang": "GDG001",
                "nama_gudang": "Gudang Pusat",
                "tahun_gudang": 2023,
                "qr_code": "http://domain.com/gudang/1"
            }
        ],
        "last_page": 1,
        "per_page": 20,
        "total": 5
    }
}
```

### 5. Get Pelanggan Profile
**GET** `/v1/mobile/profile`

Get pelanggan profile information.

**Accessible by:** pelanggan only

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "id": 1,
        "kode_pelanggan": "PLG001",
        "nama_pelanggan": "Jane Doe",
        "email": "jane@example.com",
        "lokasi_pelanggan": "Jakarta Selatan",
        "jenis_pelanggan": "agen",
        "harga_tabung": 150000,
        "penanggung_jawab": "Admin Jakarta",
        "qr_code": "http://domain.com/pelanggan/1"
    }
}
```

### 6. QR Code Scanner
**POST** `/v1/mobile/scan-qr`

Scan QR code and get item details.

**Accessible by:** All authenticated users

#### Request Body
```json
{
    "type": "tabung", // tabung, armada, gudang, pelanggan
    "id": 1
}
```

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "type": "tabung",
        "kode_tabung": "TBG001",
        "seri_tabung": "SER001",
        "tahun": 2024,
        "keterangan": "Tabung Gas 12kg"
    }
}
```

---

## Role-Based Access Control

### Roles and Permissions

1. **kepala_gudang**
   - Access to dashboard with stats
   - View tabung list
   - View armada list
   - View gudang list
   - QR code scanning

2. **operator**
   - Access to dashboard with stats
   - View tabung list
   - View gudang list
   - QR code scanning

3. **driver**
   - Access to dashboard with stats
   - View armada list
   - QR code scanning

4. **pelanggan**
   - Access to basic dashboard
   - View own profile
   - QR code scanning

---

## Error Responses

### 401 Unauthorized
```json
{
    "success": false,
    "message": "Unauthorized - No user found"
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
        "email": ["The email field is required."]
    }
}
```

### 500 Server Error
```json
{
    "success": false,
    "message": "Server error occurred",
    "error": "Detailed error message"
}
```

---

## Flutter Integration Example

### 1. HTTP Client Setup
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class ApiService {
  static const String baseUrl = 'http://your-domain.com/api';
  static String? token;
  
  static Map<String, String> get headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    if (token != null) 'Authorization': 'Bearer $token',
  };
}
```

### 2. Login Function
```dart
Future<Map<String, dynamic>> login(String email, String password, String userType) async {
  final response = await http.post(
    Uri.parse('$baseUrl/v1/auth/login'),
    headers: ApiService.headers,
    body: jsonEncode({
      'email': email,
      'password': password,
      'user_type': userType,
    }),
  );
  
  final data = jsonDecode(response.body);
  
  if (response.statusCode == 200 && data['success']) {
    ApiService.token = data['data']['token'];
    return data;
  } else {
    throw Exception(data['message']);
  }
}
```

### 3. Get Dashboard
```dart
Future<Map<String, dynamic>> getDashboard() async {
  final response = await http.get(
    Uri.parse('$baseUrl/v1/mobile/dashboard'),
    headers: ApiService.headers,
  );
  
  final data = jsonDecode(response.body);
  
  if (response.statusCode == 200 && data['success']) {
    return data['data'];
  } else {
    throw Exception(data['message']);
  }
}
```
