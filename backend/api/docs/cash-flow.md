
# Dokumentasi API Cash Flow

## Endpoint: Get All Cash flow

### **GET** `/finance/cashflow`

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
| start_date | Tanggal awal |
| end_date | Tanggal akhir |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": [
		{
			"id": "0198fa97-4af1-7248-8a2b-a22613489790",
			"date": "2025-08-30 17:47:55",
			"category": "Bank Transfer",
			"sub_category": "Bank Transfer",
			"description": "Bank Transfer",
			"debit": "10000.00",
			"credit": "-",
			"bank_name": "bank BJB",
			"saldo": 821000
		},
		{
			"id": "0198fa97-4aec-73a2-a87d-1ea0faab531e",
			"date": "2025-08-30 17:47:55",
			"category": null,
			"sub_category": "Bank Transfer",
			"description": null,
			"debit": "-",
			"credit": "10000.00",
			"bank_name": "bank Indo",
			"saldo": 890000
		},
		{
			"id": "0198f9c3-720f-71dd-9570-2d0bbf0c500d",
			"date": "2025-08-30 13:56:32",
			"category": null,
			"sub_category": "Administrasi bank",
			"description": "koala",
			"debit": "-",
			"credit": "90000.00",
			"bank_name": "bank BJB",
			"saldo": 811000
		},
		{
			"id": "0198f9c1-2a2f-71a3-8e8d-773a50ffe7d2",
			"date": "2025-08-30 13:54:02",
			"category": "Pemodalan",
			"sub_category": "Pemodalan",
			"description": "Pemodalan",
			"debit": "1000.00",
			"credit": "-",
			"bank_name": "bank BJB",
			"saldo": 901000
		}
	],
	"pagination": {
		"total": 4,
		"per_page": 20,
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

## Endpoint: Export All Cash flow

### **GET** `/finance/cashflow/export`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| start_date | `mandatory` Tanggal awal |
| end_date | `mandatory` Tanggal akhir |

### Contoh Response Berhasil
> code : 200

file will be downloaded

### Contoh Response Gagal (jika token tidak valid)
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---