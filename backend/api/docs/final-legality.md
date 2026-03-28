
# Dokumentasi API Management Legalitas Akhir

## Endpoint: Get Summary Legalitas Akhir By Status

### **GET** `/crm/final-legality/summary`

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
	"message": "Success Get Final Legalities",
	"data": [
		{
			"status": "pending",
			"total": 0
		},
		{
			"status": "bast",
			"total": 0
		},
		{
			"status": "retention",
			"total": 1
		},
		{
			"status": "complete",
			"total": 0
		}
	]
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

---



### Contoh Response Gagal
**Jika Token Tidak Valid**
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Endpoint: Get Completed Payment Lead

### **GET** `/crm/final-legality/get-completed-payment-lead`

Menampilkan list lead yang documentnya sudah selesai

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Query Params
| Parameter | Deskripsi |
| search | cari nama lead |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success Get Final Legality",
	"data": [
		{
			"id": "01972b7e-3886-7170-a936-c0691ec68435",
			"payment_id": "01973501-ec19-7193-bb9c-28d68a3c5b05",
			"name": "Galih"
		}
	]
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

---

## Endpoint: Create Legalitas Akhir Lead

### **POST** `/crm/final-legality`

### Header
```
Content-Type: application/json
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| lead_id | uuid lead `required`     |
| bast_document | bast document (pdf) |
| bast_hanover_photo | bast hanover photo (png, jpg, jpeg) |
| bast_date | bast date |
| retention_document | retention document (pdf) |
| retention_hanover_photo | retention hanover photo (png, jpg, jpeg) |
| retention_start_date | retention date |
| notes | notes |

### Contoh Request Body
```multipart/form-data
{
	"lead_id": "uuid",
	"bast_document": "file",
	"bast_hanover_photo": "file",
	"bast_date": "2022-01-01",
	"retention_document": "file",
	"retention_hanover_photo": "file",
	"retention_start_date": "2022-01-01",
	"notes": "Ajukan KPR melalui dua bank"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Success Create Final Legality",
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

**Jika Validasi Gagal**
> code : 400
```json
{
	"success": false,
	"message": "Validation error",
	"errors": {
		"lead_id": [
			"required"
		],
	}
}
```

---


## Endpoint: Get All Legalitas Akhir

### **GET** `/crm/final-legality`

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
| sortKey  | Urutkan berdasarkan kolom (status,duration) |
| sortDir   | Urutkan berdasarkan (asc,desc) |
| status | ('proses_bank', 'sp3k', 'akad_kredit', 'cash') |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success Get Final Legalities",
	"data": [
		{
			"id": "019736c4-954b-7373-a2e5-743dd5e0c59d",
			"status": "Retention",
			"name": "Galih",
			"phone": "86352675623",
			"notes": "koala",
			"property_unit_id": "6d20a605-d856-4102-bd8d-461a35e23990",
			"property_unit": "-",
			"duration": "0 days",
			"created_at": "2025-06-04"
		}
	],
	"pagination": {
		"total": 1,
		"per_page": 10,
		"current_page": 1,
		"last_page": 1
	}
}
```
> duration = berapa hari dari reservation date sampai sekarang

### Contoh Response Gagal (jika token tidak valid)
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Endpoint: Get Legalitas Akhir By Id

### **GET** `/crm/final-legality/{id}`

Bisa di gunakan untuk autofill di form edit legalitas akhir

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
	"message": "Success Get Final Legality",
	"data": {
		"id": "019736c4-954b-7373-a2e5-743dd5e0c59d",
		"lead_id": "01972b7e-3886-7170-a936-c0691ec68435",
		"name": "Galih",
		"phone": "86352675623",
		"email": null,
		"bast_document": "http://localhost:8000/api/file/crm/lead_legalitas_akhir/bast_file/7f1fac4b-cdb3-4d82-9f58-2fd6c5445392.pdf",
		"bast_hanover_photo": "http://localhost:8000/api/file/crm/lead_legalitas_akhir/bast_hanover_photo/74e2347a-0143-490e-bcc0-3e74a76dd7ac.png",
		"bast_date": "2025-10-13",
		"retention_document": "http://localhost:8000/api/file/crm/lead_legalitas_akhir/retention_document/b6799f78-470c-44bd-849d-60e7c187158a.pdf",
		"retention_hanover_photo": "http://localhost:8000/api/file/crm/lead_legalitas_akhir/retention_hanover_photo/e3a0ac1c-35ac-432b-aabc-c42370189b3c.png",
		"retention_start_date": "2025-10-15",
		"retention_end_date": "2026-01-15",
		"notes": "koala",
		"created_at": "1970-01-01"
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

**Jika Reservation Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: Update Legalitas Akhir

### **POST** `/crm/final-legality/{id}`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| bast_document | bast document (pdf) |
| bast_hanover_photo | bast hanover photo (png, jpg, jpeg) |
| bast_date | bast date |
| retention_document | retention document (pdf) |
| retention_hanover_photo | retention hanover photo (png, jpg, jpeg) |
| retention_start_date | retention date |
| notes | notes |


### Contoh Request Body
```json
// multipart/form-data
{
	"bast_document": "7f1fac4b-cdb3-4d82-9f58-2fd6c5445392.pdf",
	"bast_hanover_photo": "74e2347a-0143-490e-bcc0-3e74a76dd7ac.png",
	"bast_date": "2025-10-13",
	"retention_document": "b6799f78-470c-44bd-849d-60e7c187158a.pdf",
	"retention_hanover_photo": "e3a0ac1c-35ac-432b-aabc-c42370189b3c.png",
	"retention_start_date": "2025-10-15",
	"retention_end_date": "2026-01-15",
	"notes": "koala"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success Update Final Legality",
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

---

## Endpoint: Delete Lead Legalitas Akhir

### **DELETE** `/crm/final-legality/{id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Lead deleted successfully",
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

**Jika Lead Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---