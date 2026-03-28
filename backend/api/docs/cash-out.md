
# Dokumentasi API Cash Flow Out

## Endpoint: GET List Bank

### **GET** `/finance/cash-in/bank-list`

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
	"message": "Bank list fetched successfully",
	"data": [
		{
			"id": "0198c7e8-777b-7027-97ed-dd9ff4e8770a",
			"name": "bank BJB"
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

## Endpoint: Create New Cash flow Out

### **POST** `/finance/cash-out`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| category_id | `mandatory` ID Category  |
| sub_category_id | `mandatory` ID Sub Category  |
| bank_account_id | `mandatory` ID Bank Account  |
| total_amount | `mandatory` Total Amount  |
| description | `mandatory` Description  |
| notes |  Notes  |

### Contoh Request Body
```json
{
	"category_id": "CATEGORY_ID",
	"sub_category_id": "SUB_CATEGORY_ID",
	"bank_account_id": "BANK_ACCOUNT_ID",
	"total_amount": 990000,
	"description": "DESCRIPTION",
	"notes": "NOTES"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Cash Out created successfully",
	"data": {
		"id": "01986a50-5a67-7180-aec3-64a5a9e19b0f",
		"category_id": "8268318d-e8f9-4360-aff5-ad6d1f7acf09",
		"sub_category_id": "7632ec14-c917-4003-b41f-0eb534cd71dd",
		"total_amount": 900001,
		"description": "Koala",
		"notes": "testing",
		"updated_at": "2025-08-02T10:25:07.000000Z",
		"created_at": "2025-08-02T10:25:07.000000Z"
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
		"total_amount": [
			"The total amount field is required"
		]
	}
}
```

---

## Endpoint: Get All Cash flow Out

### **GET** `/finance/cash-out`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| page      | Halaman |
| per_page  | Jumlah data per halaman |
| status    | Status (lunas/belum-lunas) |
| category_id | ID Category |
| sub_category_id | ID Sub Category |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Cash Out retrieved successfully",
	"data": [
		{
			"id": "01986a50-5a67-7180-aec3-64a5a9e19b0f",
			"category_id": "8268318d-e8f9-4360-aff5-ad6d1f7acf09",
			"category": "Biaya lainnya",
			"sub_category": "Administrasi bank",
			"total_amount": "900001.00",
			"description": "Koala",
			"created_at": "2025-08-02",
			"notes": "testing"
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

## Endpoint: Get Cash flow Out By ID

### **GET** `/finance/cash-out/{cash_out_id}`

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
	"message": "Cash In retrieved successfully",
	"data": {
		"id": "01986a46-0cf6-732a-bb4c-257c31c5c7ba",
		"category_id": "8268318d-e8f9-4360-aff5-ad6d1f7acf09",
		"sub_category_id": "7632ec14-c917-4003-b41f-0eb534cd71dd",
		"category": "Biaya lainnya",
		"sub_category": "Administrasi bank",
		"total_amount": "1000000.00",
		"paid_amount": "0.00",
		"description": "gaung",
		"bank_account": "bank BJB",
		"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
		"notes": "testing"
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

## Endpoint: Update Cash Out

### **PUT** `/finance/cash-out/{cash_out_id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                 |
|-----------|----------------------------|
| total_amount | `mandatory` Total Amount |
| description  | `mandatory` Description |
| bank_account_id    | `mandatory` ID Bank     |
| notes        | Notes |

### Contoh Request Body
```json
{
	"total_amount" : 1000000,
	"description" : "koala",
	"notes" : "testing"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Cash In updated successfully",
	"data": {
		"id": "01986a46-0cf6-732a-bb4c-257c31c5c7ba",
		"category_id": "8268318d-e8f9-4360-aff5-ad6d1f7acf09",
		"sub_category_id": "7632ec14-c917-4003-b41f-0eb534cd71dd",
		"description": "gaung",
		"total_amount": 1000000,
		"paid_amount": "0.00",
		"notes": "testing",
		"created_at": "2025-08-02T10:13:52.000000Z",
		"updated_at": "2025-08-02T10:15:54.000000Z"
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
		"total_amount": [
			"The total amount field is required"
		]
	}
}
```

---

## Endpoint: Delete Cash Out

### **DELETE** `/finance/cash-out/{cash_out_id}`

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
	"message": "Cash In deleted successfully",
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

**Jika Cash Out Id Tidak Valid**
> code : 400
```json
{
	"success": false,
	"message": "Invalid cash out id",
	"errors": null
}
```

**Jika Cash Out Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: GET List Category Cash Out

### **GET** `/finance/cash-out/categories`

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
			"id": "fd9f5d09-0791-4cfb-bc79-f01c7bd532ba",
			"name": "Biaya akad konsumen"
		},
		{
			"id": "85101b50-66f3-4877-9fae-fbc6886b8380",
			"name": "Biaya akad notaris"
		},
		{
			"id": "17f89911-2c99-4485-bc2d-e66beef3ae84",
			"name": "Biaya konsultan & entertain bank"
		},
		{
			"id": "8268318d-e8f9-4360-aff5-ad6d1f7acf09",
			"name": "Biaya lainnya"
		},
		{
			"id": "e783b071-b30a-4bfb-a97f-885ed2411bfb",
			"name": "Biaya perijinan"
		},
		{
			"id": "3e0097f0-7ffb-486e-9297-43b271145d73",
			"name": "Biaya sertifikat"
		},
		{
			"id": "6b92bd4d-a4d9-418d-bcf7-6f304386acce",
			"name": "Gaji & upah"
		},
		{
			"id": "95c9636d-f612-4029-a2a2-6518e6d39b74",
			"name": "Kompensasi warga"
		},
		{
			"id": "8cbaf35c-b244-448e-a5b1-c716d38ae31c",
			"name": "Kontruksi"
		},
		{
			"id": "f48adb95-00b4-4d34-84ff-69d3ca5e67d8",
			"name": "Listrik dan air"
		},
		{
			"id": "77d2e7e8-760e-4f8b-b4e2-00577e58b341",
			"name": "Marketing"
		},
		{
			"id": "74424954-5678-432f-b8e5-d4fd5c0b2a74",
			"name": "Operasional lainnya"
		},
		{
			"id": "e5b17fd6-225b-4c2e-beb5-dc33433ef528",
			"name": "Pematangan lahan"
		},
		{
			"id": "2d188699-3bff-4eba-a150-760afdfa2c78",
			"name": "Pembebasan lahan"
		},
		{
			"id": "585e6c46-36a7-49cc-a150-5b0a60413025",
			"name": "Perencanaan teknis"
		},
		{
			"id": "d41abf6f-d7cf-4c12-a095-49724c102b9c",
			"name": "Sarana prasarana"
		},
		{
			"id": "03272abf-b7ee-4e7f-b607-e8e67118e747",
			"name": "Tarikan"
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

## Endpoint: GET List Sub Category Cash Out

### **GET** `/finance/cash-out/sub-categories/{category_id}`

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
	"message": "Sub Categories retrieved successfully",
	"data": [
		{
			"id": "7632ec14-c917-4003-b41f-0eb534cd71dd",
			"name": "Administrasi bank"
		},
		{
			"id": "38a7088d-0966-4fff-870d-2055f93fb859",
			"name": "Apraisal Kredit Bank"
		},
		{
			"id": "dec9bc6c-7755-4500-8453-dbb40a97166a",
			"name": "Beban Penyusutan"
		},
		{
			"id": "3d6b23b7-559b-443a-8097-3c8d1c8e2e26",
			"name": "Insentif/Jasprod"
		},
		{
			"id": "39ad0b10-23e2-4e3d-b4e9-80f7caf277fb",
			"name": "Margin Pinjaman BPRS Mentari"
		},
		{
			"id": "02f5c7ed-3952-49b6-9e05-a002b6a3a713",
			"name": "Margin Pinjaman Bank KYG-PPL"
		},
		{
			"id": "dbd7feea-64f3-464c-b6f2-6664704c9772",
			"name": "Margin Pinjaman lainnya"
		},
		{
			"id": "7752702f-b57e-4573-af4b-496506e7e8e3",
			"name": "Roya"
		},
		{
			"id": "8c369344-1d80-44be-88cb-b30cc4d39c95",
			"name": "ZIS"
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

## Endpoint: Create Transaction (on cash out)

### **PUT** `/finance/cash-out`/{cash_out_id}/transaction`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| amount | `mandatory` Amount  |
| date | Date (YYYY-MM-DD)  custom tanggal|
| bank_account_id | `mandatory` ID Bank  |
| notes |  Notes  |

### Contoh Request Body
```json
{
	"amount" : 10000,
	"date" : "2020-01-01",
	"notes" : "koala"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Action performed successfully",
	"data": {
		"transaction_id": null
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
---

## Endpoint: GET All Transaction Per Cash Out

### **GET** `/finance/cash-out/{cash_out_id}/transaction`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| page | `optional` Halaman  |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Transactions retrieved successfully",
	"data": [
		{
			"transaction_id": "01986a57-ad3b-7398-b38f-ab09f2d4debe",
			"category": "Biaya lainnya",
			"sub_category": "Administrasi bank",
			"description": "Koala",
			"amount": "90000.00",
			"notes": "koala",
			"created_at": "2025-08-02 17:33:07"
		}
	],
	"pagination": {
		"total": 1,
		"per_page": 20,
		"current_page": 1,
		"last_page": 1
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

---

## Endpoint: Delete Transaction

### **DELETE** `/finance/cash-out/transaction/{transaction_id}`

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
	"message": "Transaction deleted successfully",
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
