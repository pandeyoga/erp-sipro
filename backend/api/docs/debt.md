
# Dokumentasi API Management Piutang

## Endpoint: Get All Categories

### **GET** `/finance/loan/categories`

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
	"message": "Categories retrieved successfully",
	"data": [
		{
			"id": "ad26bd82-8ba9-4a52-ac25-d245ac52656f",
			"name": "Pinjaman BPRS Mentari"
		},
		{
			"id": "413bfaf1-623b-4a78-968e-0e3618bbaec5",
			"name": "Pinjaman Bank KYG-PPL"
		},
		{
			"id": "f84a4f5e-80a5-4a8d-9642-d72a970ea1b2",
			"name": "Pinjaman lainnya"
		}
	]
}
```

### Contoh Response Gagal

**Jika Token Tidak Valid**
```json
{
  "success": false,
  "message": "Unauthorized",
  "errors": null
}
```

---

## Endpoint: Create Piutang

### **POST** `/finance/loan`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                  |
|-----------|-----------------------------|
| category_id    | `mandatory` Kategori Asset                   |
| name    | `mandatory` Nama Asset                   |
| bank_account_id    | `mandatory` Rekening Bank                   |
| amount    | `mandatory` Jumlah Asset                   |
| description    | `optional` Deskripsi Asset                   |

### Contoh Request Body
```json
{
	"category_id" : "{category_id}",
	"name" : "pinjaman bank bca",
	"bank_account_id" : "{bank_account_id}",
	"amount" : 900000,
	"description" : "pinjaman bank bca"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Loan created successfully",
	"data": {
		"name": "pinjaman bank bca",
		"description": null,
		"cash_in_sub_sub_category_id": "413bfaf1-623b-4a78-968e-0e3618bbaec5",
		"bank_account_id": "0199adcd-54b2-7389-8c57-17b679c3a472",
		"payment_bank_account_id": null,
		"total_amount": 900000,
		"paid_amount": 0,
		"cash_in_id": "0199b4ac-0ddb-73b1-bcfa-23ce6c4ecf8a",
		"cash_out_id": null,
		"created_by": "df50e717-4638-4ab2-ae80-1de5aa57e5d6",
		"id": "0199b4ac-0de3-7336-89e3-656d60df0281",
		"updated_at": "2025-10-05T13:59:58.000000Z",
		"created_at": "2025-10-05T13:59:58.000000Z"
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
		"name": "The name field is required."
	}
}
```

---

## Endpoint: Get All Piutang

### **GET** `/finance/loan`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Parameter
| Parameter | Keterangan                  |
|-----------|-----------------------------|
| page    | `optional` Halaman Asset                   |
| per_page    | `optional` Jumlah Asset per halaman                   |
| search    | `optional` cari Nama Asset                   |
| status    | `optional` lunas/belum_lunas                   |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Loan retrieved successfully",
	"data": [
		{
			"id": "0199b4ac-0de3-7336-89e3-656d60df0281",
			"name": "pinjaman bank bca",
			"description": null,
			"total_amount": "900000",
			"paid_amount": "0",
			"status": "belum-lunas",
			"created_at": "2025-10-05T13:59:58.000000Z",
			"created_by_name": "Admin",
			"category": "Pinjaman Bank KYG-PPL"
		},
		{
			"id": "0199b3dd-c487-7164-bc5a-e4ab1e4699fa",
			"name": "pinjaman bank aa",
			"description": null,
			"total_amount": "1900000",
			"paid_amount": "1900000",
			"status": "lunas",
			"created_at": "2025-10-05T10:14:39.000000Z",
			"created_by_name": "Admin",
			"category": "Pinjaman Bank KYG-PPL"
		}
	],
	"pagination": {
		"total": 2,
		"per_page": 10,
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

## Endpoint: Get Piutang By ID

### **GET** `/finance/loan/{id}`

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
	"message": "Loan retrieved successfully",
	"data": {
		"id": "0199b3dd-c487-7164-bc5a-e4ab1e4699fa",
		"name": "pinjaman bank bca",
		"description": null,
		"total_amount": "900000",
		"paid_amount": "0",
		"status": "belum-lunas",
		"created_at": "2025-10-05T10:14:39.000000Z",
		"created_by_name": "Admin",
		"category": "Pinjaman Bank KYG-PPL",
		"cash_in_id": "0199b3dd-c47f-7124-b48d-f5c60779b63f"
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

**Jika Role Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: Update Piutang

### **PUT** `/finance/loan/{id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                  |
|-----------|-----------------------------|
| name    | `required` Nama Piutang                   |
| category_id    | `required` Kategori Piutang                   |
| bank_account_id    | `required` Bank Piutang                   |
| amount    | `required` Jumlah Piutang                   |


### Contoh Request Body
```json
{
	"category_id" : "{id}",
	"name" : "pinjaman bank awam",
	"bank_account_id" : "{id}",
	"amount" : 1900000
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Loan updated successfully",
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
		"name": ["required"]
	}
}
```

---

## Endpoint: Delete Piutang

### **DELETE** `/finance/loan/{asset_id}`

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
	"message": "Loan deleted successfully",
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

## Endpoint: Bayar Piutang

### **PUT** `/finance/loan/{id}/payment`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                  |
|-----------|-----------------------------|
| bank_account_id    | `required` Bank Piutang                   |
| amount    | `required` Jumlah Piutang                   |

### Contoh Request Body
```json
{
	"bank_account_id" : "{id}",
	"amount" : 1900000
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Loan deleted successfully",
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

