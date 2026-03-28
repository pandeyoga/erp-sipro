
# Dokumentasi API Rekening Bank

## Endpoint: Create Bank

### **POST** `/finance/bank-account`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| code     |`mandatory` Kode Bank unik            |
| name     | `mandatory` Nama Bank                |
| account_number | Nomor Rekening |
| opening_balance | Saldo Awal |

### Contoh Request Body
```json
{
	"code": "1-232",
	"name": "bank BJB",
	"account_number": null,
	"opening_balance" : 900000
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Bank account created successfully",
	"data": {
		"id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
		"code": "1-232",
		"name": "bank BJB",
		"account_number": null,
		"opening_balance": 900000,
		"updated_at": "2025-08-30T06:47:32.000000Z",
		"created_at": "2025-08-30T06:47:32.000000Z"
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
		"code": [
			"The code field is required."
		]
	}
}
```

---

## Endpoint: Transfer Bank

### **POST** `/finance/bank-account/transfer`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| from_bank_account_id     |`mandatory` id Bank            |
| to_bank_account_id     |`mandatory` id Bank            |
| amount     |`mandatory` Jumlah Transfer            |
| transfer_fee     |`optional` Biaya Transfer            |
| note     |`optional` Catatan Transfer            |

### Contoh Request Body
```json
{
	"from_bank_account_id" : "{id}",
	"to_bank_account_id" : "{id}",
	"amount" : 10000,
	"transfer_fee" : 1000,
	"note" : "koala
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Bank account transfer saldo successfully",
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
		"amount": [
			"The amount field is required."
		]
	}
}
```

---

## Endpoint: Get All Bank

### **GET** `/finance/bank-account`

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

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Bank accounts fetched successfully",
	"data": [
		{
			"id": "0198c7e9-6475-7093-87bd-a340334d0489",
			"code": "1-232",
			"name": "bank BJB",
			"account_number": null,
			"created_at": "2025-08-20T14:36:58.000000Z",
			"updated_at": "2025-08-20T14:36:58.000000Z"
		},
		{
			"id": "0198c7e8-777b-7027-97ed-dd9ff4e8770a",
			"code": "1-231",
			"name": "bank BJB",
			"account_number": null,
			"created_at": "2025-08-20T14:35:57.000000Z",
			"updated_at": "2025-08-20T14:35:57.000000Z"
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

## Endpoint: List Transfer All Bank

### **GET** `/finance/bank-account/transfer`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| start_date | Tanggal awal |
| end_date | Tanggal akhir |
| page      | Halaman |
| per_page  | Jumlah data per halaman |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Bank account list transfer fetched successfully",
	"data": [
		{
			"id": "0198fa97-4af2-71df-901a-caeda0102469",
			"from_bank_account_name": "bank Indo",
			"to_bank_account_name": "bank BJB",
			"amount": 10000,
			"notes": null,
			"date": "2025-08-30T10:47:55.000000Z"
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

### Contoh Response Gagal (jika token tidak valid)
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Endpoint: Get Transaction Bank

### **GET** `/finance/bank-account/{id}/transaction`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| start_date    | Tanggal Awal |
| end_date    | Tanggal Akhir |
| page      | Halaman |
| per_page  | Jumlah data per halaman |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Bank account detail transaction fetched successfully",
	"data": [
		{
			"id": "0198f9c3-720f-71dd-9570-2d0bbf0c500d",
			"name": "Administrasi bank",
			"type": "out",
			"amount": "90000.00",
			"notes": "koala",
			"date": "2025-08-30T06:56:32.000000Z"
		},
		{
			"id": "0198f9c1-2a2f-71a3-8e8d-773a50ffe7d2",
			"name": "Pemodalan",
			"type": "in",
			"amount": "1000.00",
			"notes": "koala",
			"date": "2025-08-30T06:54:02.000000Z"
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

## Endpoint: Get Bank By ID

### **GET** `/finance/bank-account/{id}`

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
	"message": "Bank account fetched successfully",
	"data": {
		"id": "0198c7e9-6475-7093-87bd-a340334d0489",
		"code": "1-232",
		"name": "bank BJB",
		"account_number": null,
		"created_at": "2025-08-20T14:36:58.000000Z",
		"updated_at": "2025-08-20T14:36:58.000000Z"
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

**Jika User Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: Update Bank

### **PUT** `/finance/bank-account/{id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                 |
|-----------|----------------------------|
| code      | `required` bank code        |
| name      | `required` bank name        |
| account_number | bank account number |
| opening_balance | opening balance |

### Contoh Request Body
```json
{
	"code": "1-232",
	"name": "bank BJB",
	"account_number": null,
	"opening_balance": 9000
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Bank account updated successfully",
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
		"name": "The name field is required.",
	}
}
```

---

## Endpoint: Delete Bank

### **DELETE** `/finance/bank-account/{id}`

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
	"message": "Bank account deleted successfully",
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

**Jika User Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---