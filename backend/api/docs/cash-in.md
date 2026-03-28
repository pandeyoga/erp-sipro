
# Dokumentasi API Cash Flow In

## Endpoint: Create New Cash flow In

### **POST** `/finance/cash-in`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| property_id | `mandatory if category is related property` ID Property  |
| category_id | `mandatory` ID Category  |
| sub_category_id | `mandatory` ID Sub Category  |
| bank_account_id | `mandatory` ID Bank Account  |
| total_amount | `mandatory` Total Amount  |
| description | `mandatory` Description  |
| notes |  Notes  |

### Contoh Request Body
```json
{
	"property_id": "PROPERTY_ID",
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
	"message": "Cash In created successfully",
	"data": {
		"id": "01984540-dc50-7224-ad64-f3d96dcf0b06",
		"property_id": "01983603-d115-711a-9696-3547c4913962",
		"category_id": "7dfc0440-0ca2-489f-a535-9ded73682918",
		"sub_category_id": "df815b38-52cb-4b40-8ed7-c976760f9533",
		"total_amount": 900000,
		"description": "cash bartahap",
		"notes": "testing",
		"updated_at": "2025-07-26T05:42:15.000000Z",
		"created_at": "2025-07-26T05:42:15.000000Z"
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

## Endpoint: Get All Cash flow In

### **GET** `/finance/cash-in`

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
| status    | status (lunas, belum-lunas) |
| category_id | ID Category |
| sub_category_id | ID Sub Category |


### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Cash In retrieved successfully",
	"data": [
		{
			"id": "01984532-0640-7292-8ffc-5ad8c9f5c502",
			"property_id": "01983603-d115-711a-9696-3547c4913962",
			"category_id": "7dfc0440-0ca2-489f-a535-9ded73682918",
			"category": "Penjualan Rumah",
			"sub_category_id": "df815b38-52cb-4b40-8ed7-c976760f9533",
			"sub_category": "Cash Bertahap",
			"total_amount": "1000000.00",
			"paid_amount": null,
			"description": "cash bartahap",
			"type": "cash-bertahap",
			"status": "belum lunas",
			"created_at": "2025-07-26",
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

## Endpoint: Get Cash flow In By ID

### **GET** `/finance/cash-in/{cash_in_id}`

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
		"id": "01984532-0640-7292-8ffc-5ad8c9f5c502",
		"property_id": "01983603-d115-711a-9696-3547c4913962",
		"property_name": "12A - Agatha - Harmony Land 4 - subsidi 30/60",
		"category_id": "7dfc0440-0ca2-489f-a535-9ded73682918",
		"sub_category_id": "df815b38-52cb-4b40-8ed7-c976760f9533",
		"bank_account": "bank BJB",
		"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
		"category": "Penjualan Rumah",
		"sub_category": "Cash Bertahap",
		"total_amount": "1000000.00",
		"paid_amount": "120000.00",
		"description": "cash bartahap",
		"notes": "testing",
		"created_at": "2025-07-26T05:26:03.000000Z",
		"child": {
			"general": [
				{
					"id": "01984532-0642-7282-b12e-e2e55289115a",
					"name": "cash bartahap",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
					"bank_account": "bank BJB"
				}
			],
			"pembayaran bertahap": [
				{
					"id": "01984532-0645-7332-9661-147b2ab4dfde",
					"name": "cash bartahap",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
					"bank_account": "bank BJB"
				},
				{
					"id": "01984532-0646-70ca-be02-ce05e34d64f2",
					"name": "cash bartahap",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
					"bank_account": "bank BJB"
				}
			],
			"penambahan spek": [
				{
					"id": "01984532-0643-7358-baf5-b5d685d56c9d",
					"name": "cash bartahap",
					"total_amount": "120000.00",
					"paid_amount": "120000.00",
					"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
					"bank_account": "bank BJB"
				},
				{
					"id": "01984532-0644-7344-91fa-e350c0e32b2a",
					"name": "cash bartahap",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
					"bank_account": "bank BJB"
				},
				{
					"id": "01984532-0644-7344-91fa-e350c1a67ab9",
					"name": "cash bartahap",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
					"bank_account": "bank BJB"
				}
			]
		}
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

## Endpoint: Update Cash In

### **PUT** `/finance/cash-in/{cash_in_id}`

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
| notes        | Notes |

### Contoh Request Body
```json
{
	"total_amount" : 1000000,
	"description" : "cash bartahap",
	"notes" : "testing"
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
		"total_amount": [
			"The total amount field is required"
		]
	}
}
```

---

## Endpoint: Delete Cash In

### **DELETE** `/finance/cash-in/{cash_in_id}`

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
	"message": "Cash in deleted successfully",
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

**Jika Cash In Id Tidak Valid**
> code : 400
```json
{
	"success": false,
	"message": "Invalid cash in id",
	"errors": null
}
```

**Jika Cash In Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: GET List Category Cash In

### **GET** `/finance/cash-in/categories`

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
			"id": "d700adcf-243d-4a3b-8a44-7c50ae85bbd6",
			"name": "Booking"
		},
		{
			"id": "25a137fb-042f-4d11-b8d0-6d2e8cdef809",
			"name": "Pemasukan Lainya"
		},
		{
			"id": "c39beef7-ef50-44e9-8249-03e39e97f704",
			"name": "Pendapatan bunga bank"
		},
		{
			"id": "7dfc0440-0ca2-489f-a535-9ded73682918",
			"name": "Penjualan Rumah"
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

## Endpoint: GET List Sub Category Cash In

### **GET** `/finance/cash-in/sub-categories/{category_id}`

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
			"id": "df815b38-52cb-4b40-8ed7-c976760f9533",
			"name": "Cash Bertahap"
		},
		{
			"id": "2001576e-b626-4759-8f80-dbf7a7678f4b",
			"name": "Cash Keras"
		},
		{
			"id": "70c09ac7-1c7c-4691-95f1-abb5b7a95e03",
			"name": "KPR"
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

## Endpoint: GET List Sub Sub Category Cash In (Optional)

### **GET** `/finance/cash-in/sub-sub-categories/{sub_category_id}`

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
	"message": "Sub Sub Categories retrieved successfully",
	"data": {
		"general": [
			{
				"id": "f6dbb0bc-84c5-41bd-b91d-0b26b9c920ae",
				"name": "All in",
				"custom_input_description": false
			}
		],
		"pembayaran bertahap": [
			{
				"id": "ec20d848-06a1-4367-9213-0d6b27030ff7",
				"name": "Cicilan Pelunasan",
				"custom_input_description": false
			},
			{
				"id": "23c81641-efee-458b-a479-882248902097",
				"name": "DP (70%)",
				"custom_input_description": false
			}
		],
		"penambahan spek": [
			{
				"id": "b40a7027-9ac8-447d-b72f-0576296918da",
				"name": "Hook",
				"custom_input_description": false
			},
			{
				"id": "aeaedb66-5b4a-4189-a5d3-79359cb36ff8",
				"name": "Penambahan Spek bangunan",
				"custom_input_description": false
			},
			{
				"id": "9a127677-28a5-43d4-b757-a43eed609aa5",
				"name": "Penambahan tanah",
				"custom_input_description": false
			}
		]
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

## Endpoint: GET List Property

### **GET** `/finance/cash-in/property-list`

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
	"message": "Property list retrieved successfully",
	"data": [
		{
			"id": "0198f9bf-d698-700c-a3ac-7d0026a17057",
			"unit_number": "12A",
			"cluster": "Agatha",
			"project": "Harmony Land 4",
			"unit_type": "subsidi 30/60"
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

## Endpoint: Update and Create Transaction (on child cash in)

### **PUT** `/finance/cash-in/{cash_in_id}/transaction`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| cash_in_id | `mandatory` ID Cash In (bisa di lihat id pada child cash in di api get cash in by id)  |
| total_amount | `mandatory` Total Amount (akan merubah total amount dari child cash in)  |
| amount | `mandatory` Amount  |
| bank_account_id | `mandatory` Bank Account ID  |
| date | Date (YYYY-MM-DD)  custom tanggal|
| notes |  Notes  |

### Contoh Request Body
```json
{
	"cash_in_id" : "CASH_IN_ID",
	"total_amount" : 120000,
	"amount" : 10000,
	"bank_account_id" : "BANK_ACCOUNT_ID",
	"date" : "2022-01-01",
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

## Endpoint: GET All Transaction Per Cash In

### **GET** `/finance/cash-in/{cash_in_id}/transaction`

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
			"transaction_id": "01984533-3a80-703c-9c4d-0744bc937526",
			"property_id": "01983603-d115-711a-9696-3547c4913962",
			"category": "Penjualan Rumah",
			"sub_category": "Cash Bertahap",
			"description": "Hook",
			"amount": "9000.00",
			"notes": "koala",
			"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
			"bank_account": "bank BJB",
			"created_at": "2025-07-26 12:27:21"
		},
		{
			"transaction_id": "01984533-325e-70c2-b134-cdcb9997975c",
			"property_id": "01983603-d115-711a-9696-3547c4913962",
			"category": "Penjualan Rumah",
			"sub_category": "Cash Bertahap",
			"description": "Hook",
			"amount": "9000.00",
			"notes": "koala",
			"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
			"bank_account": "bank BJB",
			"created_at": "2025-07-26 12:27:19"
		},
		{
			"transaction_id": "01984533-3237-7286-8847-05724f88099f",
			"property_id": "01983603-d115-711a-9696-3547c4913962",
			"category": "Penjualan Rumah",
			"sub_category": "Cash Bertahap",
			"description": "Hook",
			"amount": "9000.00",
			"notes": "koala",
			"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
			"bank_account": "bank BJB",
			"created_at": "2025-07-26 12:27:19"
		},
		{
			"transaction_id": "01984533-3074-728a-9b32-24f20050f380",
			"property_id": "01983603-d115-711a-9696-3547c4913962",
			"category": "Penjualan Rumah",
			"sub_category": "Cash Bertahap",
			"description": "Hook",
			"amount": "9000.00",
			"notes": "koala",
			"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
			"bank_account": "bank BJB",
			"created_at": "2025-07-26 12:27:19"
		},
		{
			"transaction_id": "01984533-2a70-72d1-826a-35c63b731d45",
			"property_id": "01983603-d115-711a-9696-3547c4913962",
			"category": "Penjualan Rumah",
			"sub_category": "Cash Bertahap",
			"description": "Hook",
			"amount": "9000.00",
			"notes": "koala",
			"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
			"bank_account": "bank BJB",
			"created_at": "2025-07-26 12:27:17"
		},
		{
			"transaction_id": "01984533-252e-7208-b28b-098cb0ebf846",
			"property_id": "01983603-d115-711a-9696-3547c4913962",
			"category": "Penjualan Rumah",
			"sub_category": "Cash Bertahap",
			"description": "Hook",
			"amount": "9000.00",
			"notes": "koala",
			"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
			"bank_account": "bank BJB",
			"created_at": "2025-07-26 12:27:16"
		},
		{
			"transaction_id": "01984533-2001-7235-9467-83c6d051a95c",
			"property_id": "01983603-d115-711a-9696-3547c4913962",
			"category": "Penjualan Rumah",
			"sub_category": "Cash Bertahap",
			"description": "Hook",
			"amount": "9000.00",
			"notes": "koala",
			"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
			"bank_account": "bank BJB",
			"created_at": "2025-07-26 12:27:15"
		},
		{
			"transaction_id": "01984533-1a45-72a8-914c-a5f0b5c1155e",
			"property_id": "01983603-d115-711a-9696-3547c4913962",
			"category": "Penjualan Rumah",
			"sub_category": "Cash Bertahap",
			"description": "Hook",
			"amount": "9000.00",
			"notes": "koala",
			"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
			"bank_account": "bank BJB",
			"created_at": "2025-07-26 12:27:13"
		},
		{
			"transaction_id": "01984533-13aa-70e7-81c5-7b0e21a0470f",
			"property_id": "01983603-d115-711a-9696-3547c4913962",
			"category": "Penjualan Rumah",
			"sub_category": "Cash Bertahap",
			"description": "Hook",
			"amount": "9000.00",
			"notes": "koala",
			"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
			"bank_account": "bank BJB",
			"created_at": "2025-07-26 12:27:12"
		},
		{
			"transaction_id": "01984533-0eae-72ec-8cf3-2e72ea4796bd",
			"property_id": "01983603-d115-711a-9696-3547c4913962",
			"category": "Penjualan Rumah",
			"sub_category": "Cash Bertahap",
			"description": "Hook",
			"amount": "9000.00",
			"notes": "koala",
			"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
			"bank_account": "bank BJB",
			"created_at": "2025-07-26 12:27:10"
		},
		{
			"transaction_id": "01984532-ec83-72d0-b5c1-1774c3be4642",
			"property_id": "01983603-d115-711a-9696-3547c4913962",
			"category": "Penjualan Rumah",
			"sub_category": "Cash Bertahap",
			"description": "Hook",
			"amount": "9000.00",
			"notes": "koala",
			"bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
			"bank_account": "bank BJB",
			"created_at": "2025-07-26 12:27:02"
		}
	],
	"pagination": {
		"total": 11,
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

### **DELETE** `/finance/cash-in/transaction/{transaction_id}`

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
