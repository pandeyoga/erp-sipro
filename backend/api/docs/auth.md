
# Dokumentasi API Autentikasi & Autorisasi

## Endpoint: Login

### **POST** `/login`

### Header
```
Content-Type: application/json
Accept: application/json
```

### Body Request
| Parameter | Keterangan                  |
|-----------|-----------------------------|
| email     | Wajib, format email valid   |
| password  | Wajib                        |

### Contoh Request Body
```json
{
    "email": "user@example.com",
    "password": "password"
}
```

### Contoh Response Berhasil
```json
{
	"success": true,
	"message": "Successfully logged in",
	"data": {
		"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzU0NzI4OTUwLCJleHAiOjE3NTQ3NzIxNTAsIm5iZiI6MTc1NDcyODk1MCwianRpIjoibWhMUnR0RmRxUFMyU2pyQiIsInN1YiI6ImM2ODVhNjEyLWRhOGYtNDUxYi05MTE5LWU1MTYzM2JiNDA1MSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjciLCJ1c2VyIjp7ImlkIjoiYzY4NWE2MTItZGE4Zi00NTFiLTkxMTktZTUxNjMzYmI0MDUxIiwibmFtZSI6IkFkbWluIiwiZW1haWwiOiJhZG1pbkBsYW5kcHJvLmNvbSIsInJvbGVfaWQiOiJlOWY1ZmFiYS00MjBkLTQ4YTktYTY4ZS05YjA1ODc2YjhjYTUiLCJyb2xlX25hbWUiOiJBZG1pbiIsInBlcm1pc3Npb25zIjpbImFsbF9hY2Nlc3MiXX19.6R_rS0Xhlm7t9mqIbyf-aozRK6YkIjmHpOvTXzMLN38",
		"token_type": "bearer",
		"expires_in": 720,
		"permissions": [
			{
				"name": "All access",
				"code": "all_access"
			}
		]
	}
}
```
> `expires_in` menunjukkan masa berlaku token dalam satuan **menit**.

### Contoh Response Gagal

**1. Kesalahan Kredensial**
```json
{
  "success": false,
  "message": "Unauthorized",
  "errors": null
}
```

**2. Kesalahan Validasi**
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "email": [
      "The email field must be a valid email address."
    ]
  }
}
```
> Isi dari `errors` akan menyesuaikan dengan kesalahan yang terjadi.

---

## Endpoint: Refresh Token

### **POST** `/refresh`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
Tidak ada body yang diperlukan.

### Contoh Response Berhasil
```json
{
  "success": true,
  "message": "Successfully refreshed token",
  "data": {
    "access_token": "{Token}",
    "token_type": "bearer",
    "expires_in": 60
  }
}
```

### Contoh Response Gagal (jika token tidak valid)
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Endpoint: Logout

### **DELETE** `/logout`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
Tidak ada body yang diperlukan.

### Contoh Response Berhasil
```json
{
  "success": true,
  "message": "Successfully logged out",
  "data": null
}
```

### Contoh Response Gagal (jika token tidak valid)
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Endpoint: Permission Check

### **POST** `/check-permissions`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request

| Parameter | Keterangan                  |
|-----------|-----------------------------|
| permissions | Wajib, array yang berisi nama permission yang ingin dicek aksesnya dengan user yang sedang login |

### Contoh Request Body
```json
{
	"permissions":[
		"auth.get_all_permission_items"
	]
}
```

> `permissions` adalah array yang berisi nama permission yang ingin dicek aksesnya dengan user yang sedang login.

### Contoh Response Berhasil
```json
{
	"success": true,
	"message": "Successfully checked permissions",
	"data": [
		{
			"permission": "auth.get_all_permission_items",
			"access": true
		}
	]
}
```

### Contoh Response Gagal
**1. Token Tidak Valid**
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**2. Kesalahan Validasi**
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "permissions": [
      "The permissions field is required."
    ]
  }
}
```
> Isi dari `errors` akan menyesuaikan dengan kesalahan yang terjadi.

---