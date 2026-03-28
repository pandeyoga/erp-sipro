
# Dokumentasi API Management User

## Endpoint: Create New User

### **POST** `/manage/user`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| name     | `mandatory` Nama User    |
| email    | `mandatory` Email        |
| role_id  | `mandatory` ID Role      |
| password |  Password (jika di kosongkan akan di generate secara otomatis default "landpro123")     |

### Contoh Request Body
```json
{
	"name": "giaTest",
	"email": "gia@yopmail.com",
	"role_id": "ROLE_ID",
	"password": "password"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Success",
	"data": {
		"id": "2df0af09-176a-4190-9a86-12451bc44260",
		"name": "giaTest",
		"email": "gia@yopmail.com",
		"role_id": "01968015-e10f-7095-8511-5ec1f63c6c0a",
		"role": "Marketing Coordinator",
		"is_active": true,
		"created_at": "2025-04-29T05:47:42.000000Z"
	}
}
```

### Contoh Response Gagal

**Jika Token Tidak Valid**
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**Jika Validasi Gagal**
> code : 400
```json
{
	"success": false,
	"message": "Validation error",
	"errors": {
		"role_id": [
			"The role id field must be a valid UUID."
		]
	}
}
```

---

## Endpoint: Get All User

### **GET** `/manage/user`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| search    | Nama User / Email|
| page      | Halaman |
| per_page  | Jumlah data per halaman |
| role_id   | Filter berdasarkan ID Role |
| is_active | Filter berdasarkan Status User |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": [
		{
			"id": "4708b79e-b949-46d8-bc3e-071df1308476",
			"role_id": "0196823a-8511-716a-9b1b-c5614f7279e7",
			"name": "giaTest",
			"email": "gia@yopmail.com",
			"role": "Marketing Coordinator",
			"is_active": true,
			"created_at": "2025-04-29T15:47:06.000000Z"
		}
	],
	"pagination": {
		"total": 1,
		"per_page": 3,
		"current_page": 1,
		"last_page": 1
	}
}
```

### Contoh Response Gagal (jika token tidak valid)
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Endpoint: Get User By ID

### **GET** `/manage/user/{user_id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
Tidak ada body yang diperlukan.

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": {
		"id": "4708b79e-b949-46d8-bc3e-071df1308476",
		"role_id": "0196823a-8511-716a-9b1b-c5614f7279e7",
		"name": "giaTest",
		"email": "gia@yopmail.com",
		"role": "Marketing Coordinator",
		"is_active": true,
		"created_at": "2025-04-29T15:47:06.000000Z"
	}
}
```

### Contoh Response Gagal
**Jika Token Tidak Valid**
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**Jika User Id Tidak Valid**
> code : 400
```json
{
	"success": false,
	"message": "Invalid user id",
	"errors": null
}
```

**Jika User Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: Update User

### **PUT** `/manage/user/{id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                 |
|-----------|----------------------------|
| name    | `mandatory` Nama Role        |
| email   | `mandatory` Email User       |
| role_id | `mandatory` ID Role          |
| password | Password User               |

### Contoh Request Body
```json
{
	"name": "Gia",
	"email": "gia@landpro.com",
	"role_id": "ROLE_ID",
	"password": "password",
	"is_active": 0
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": {
		"id": "26250923-62ff-4f04-bc85-538b181870f1",
		"name": "Gia",
		"email": "gia@landpro.com",
		"role_id": "01967293-32f6-7311-8f79-a00e05856233",
		"role": "Marketing",
		"is_active": false,
		"updated_at": "2025-04-26T15:12:00.000000Z"
	}
}
```

### Contoh Response Gagal

**Jika Token Tidak Valid**
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**Jika Validasi Gagal**
> code : 400
```json
{
	"success": false,
	"message": "Validation error",
	"errors": {
		"name": "The name field is required.",
	}
}
```

---

## Endpoint: Delete User

### **DELETE** `/manage/user/{user_id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
Tidak ada body yang diperlukan.

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "User deleted successfully",
	"data": null
}
```

### Contoh Response Gagal
**Jika Token Tidak Valid**
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**Jika User Id Tidak Valid**
> code : 400
```json
{
	"success": false,
	"message": "Invalid user id",
	"errors": null
}
```

**Jika User Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

**Jika User Sedang Memiliki Task (NOT IMPLEMENTED YET)******
> code : 400
```json
{
	"success": false,
	"message": "Cannot delete user with active tasks",
	"errors": null
}
```

---

## Endpoint: Toggle Status User

### **PUT** `/manage/user/{user_id}/status`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
Tidak ada body yang diperlukan.

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "User status updated successfully",
	"data": {
		"user_id": "26250923-62ff-4f04-bc85-538b181870f1",
		"status": "inactive"
	}
}
```

### Contoh Response Gagal

**Jika Token Tidak Valid**
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**Jika User Id Tidak Valid**
> code : 400
```json
{
	"success": false,
	"message": "Invalid user id",
	"errors": null
}
```

**Jika User Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

