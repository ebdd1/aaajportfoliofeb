# 🔐 Auth API Documentation

**Base URL:** `https://greyhound-quicken-schedule.ngrok-free.dev/api`

---

## Endpoints

### 1. Register

**`POST /api/auth/register`**

Register new user account.

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePass123",
  "password_confirmation": "SecurePass123"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 5,
      "name": "John Doe",
      "email": "john@example.com",
      "is_admin": false,
      "email_verified_at": null
    },
    "token": "1|njp3I2fbA0Q7GYGJfURhg47kmBhJlOCAyU3gNWyVd68c7d61",
    "token_type": "Bearer"
  }
}
```

**Validation Rules:**
- `name`: required, string, max 255
- `email`: required, email, unique in users table
- `password`: required, min 8 chars, mixed case, numbers, confirmed

---

### 2. Login

**`POST /api/auth/login`**

Authenticate user and receive token.

**Request:**
```json
{
  "email": "john@example.com",
  "password": "SecurePass123",
  "device_name": "iPhone 15 Pro"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 5,
      "name": "John Doe",
      "email": "john@example.com",
      "is_admin": false,
      "email_verified_at": null
    },
    "token": "2|a4MaVNrCAUO1LJGNFYgo10SBocygxcFAwwfo0LWI61daba8c",
    "token_type": "Bearer"
  }
}
```

**Rate Limiting:** 5 attempts per minute per IP + email combination.

**Error (401):**
```json
{
  "success": false,
  "message": "Invalid credentials",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

---

### 3. Get Current User

**`GET /api/auth/me`**

Get authenticated user information.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 5,
    "name": "John Doe",
    "email": "john@example.com",
    "is_admin": false,
    "email_verified_at": null,
    "created_at": "2026-06-29T03:07:51.000000Z",
    "profile": {
      "avatar": "/storage/avatars/default.png",
      "phone": null,
      "bio": null
    }
  }
}
```

---

### 4. Logout

**`POST /api/auth/logout`**

Revoke current access token.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## Authentication Flow

```
1. Register/Login → Get Bearer Token
2. Use token in Authorization header for protected endpoints
3. Logout → Revoke token
```

### Example Usage

```bash
# Register
curl -X POST /api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John","email":"john@test.com","password":"Test1234","password_confirmation":"Test1234"}'

# Login
curl -X POST /api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@test.com","password":"Test1234"}'

# Get current user
curl -X GET /api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Logout
curl -X POST /api/auth/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Security Features

| Feature | Implementation |
|---------|----------------|
| Rate Limiting | 5 attempts/min per IP+email |
| Password Rules | Min 8 chars, mixed case, numbers |
| Token Security | Sanctum with hashed tokens |
| CORS | Configured for SPA |
| HTTPS | Enforced (ngrok) |

---

## Error Responses

### Validation Error (422)
```json
{
  "message": "The email field is required.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

### Rate Limited (429)
```json
{
  "message": "Too many login attempts. Please try again in 1 minutes.",
  "errors": {
    "email": ["Too many login attempts. Please try again in 1 minutes."]
  }
}
```

### Unauthenticated (401)
```json
{
  "message": "Unauthenticated."
}
```