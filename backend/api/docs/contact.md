
# Dokumentasi API Management Contact

## Endpoint: Create New Contact

### **POST** `/crm/contact`

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
| email    |  Email                   |
| phone    | `mandatory` No Telepon   |
| location |  Alamat                  |
| date     | `mandatory` Tanggal Input|

### Contoh Request Body
```json
{
	"name": "gia fauzan",
	"email": "gia@yopmail.com",
	"phone": "83678264782",
	"location": "sadang",
	"date" : "2025-05-06"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Contact created successfully",
	"data": {
		"id": "0196a46f-15a5-73e2-af6a-774299d1254b",
		"name": "gia fauzan",
		"email": null,
		"phone": "83678264782",
		"location": null,
		"created_at": "2025-05-06T07:11:05.000000Z"
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
		"name": [
			"The name field is required."
		]
	}
}
```

---

## Endpoint: Import Contact

### **POST** `/crm/contact/import`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| file     | `mandatory` File Excel   |

<!-- template import ada di -->
### download excel Template
```
http://localhost:8000/files/static/importable-contact-template.xlsx
```
> ubah 'http://localhost:8000' sesuai dengan url server


### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Contacts imported successfully",
	"data": true
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
		"file": [
			"The file field is required."
		]
	}
}
```

---

## Endpoint: Get All Contact

### **GET** `/crm/contact`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| search    | cari dengan query ilike ke kolom name, email, phone dan location |
| page      | Halaman |
| per_page  | Jumlah data per halaman |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Contacts fetched successfully",
	"data": [
		{
			"id": "0196a46e-e4ba-7385-aecd-629c6538ae16",
			"name": "gia fauzan",
			"email": null,
			"phone": "83678264782",
			"location": "sadang",
			"is_duplicate": true,
			"created_at": "2025-05-06 14:10:53"
		},
		{
			"id": "01969f15-1e0c-72b5-8593-d302a9c3297f",
			"name": "gia fauzan",
			"email": "gia@yopmail.com",
			"phone": "83678264782",
			"location": "sadang",
			"is_duplicate": false,
			"created_at": "2025-05-05 13:14:43"
		}
	],
	"pagination": {
		"total": 2,
		"per_page": 20,
		"current_page": 1,
		"last_page": 1
	}
}
```
> is_duplicate menunjukkan apakah contact tersebut adalah duplikat dari contact lain (berdasarkan phone)

### Contoh Response Gagal (jika token tidak valid)
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Endpoint: Get Contact By ID

### **GET** `/crm/contact/{contact_id}`

Bisa di gunakan untuk autofill data form leads create dan update

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
	"message": "Contact fetched successfully",
	"data": {
		"id": "01967d48-3f02-703f-97b7-163cbb520154",
		"name": "naja",
		"email": "naja@yopmail.com",
		"phone": "0873286247264",
		"location": "jl lebe",
		"created_at": "2025-04-28T16:43:28.000000Z"
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

**Jika Contact Id Tidak Valid**
> code : 400
```json
{
	"success": false,
	"message": "Invalid contact id",
	"errors": null
}
```

**Jika Contact Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: Update Contact

### **PUT** `/crm/contact/{contact_id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                 |
|-----------|----------------------------|
| name    | `mandatory` Nama             |
| email   |  Email                       |
| phone   | `mandatory` No Telepon       |
| location|  Alamat                      |


### Contoh Request Body
```json
{
	"name": "naja",
	"email": "naja@yopmail.com",
	"phone": "0873286247264",
	"location": "jl lebe"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Contact updated successfully",
	"data": {
		"id": "01968794-f7bf-7022-827f-3381cb2b3c8b",
		"name": "naja",
		"email": "naja@yopmail.com",
		"phone": "0873286247264",
		"location": "jl lebe",
		"updated_at": "2025-04-30T16:44:25.000000Z"
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

## Endpoint: Delete Contact

### **DELETE** `/crm/contact/{contact_id}`

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
	"message": "Contact deleted successfully",
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

**Jika Contact Id Tidak Valid**
> code : 400
```json
{
	"success": false,
	"message": "Invalid contact id",
	"errors": null
}
```

**Jika Contact Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

**Jika Contact adalah leads**
> code : 400
```json
{
	"success": false,
	"message": "Can't delete this contact, because it's leads",
	"errors": null
}
```

---