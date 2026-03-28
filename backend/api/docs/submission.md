
# Dokumentasi API Submission

## Endpoint: Create New Submission

### **POST** `/finance/submission`

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
| amount | `mandatory` Total Amount  |
| description | `mandatory` Description  |
| notes |  Notes  |
| file_proof |  File Proof  |

### Contoh Request Body
```json
{
	"category_id": "CATEGORY_ID",
	"sub_category_id": "SUB_CATEGORY_ID",
	"amount": 990000,
	"description": "DESCRIPTION",
	"notes": "NOTES"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Submission created successfully",
	"data": {
		"type": "submission",
		"category_id": "3bc226f1-2395-4f64-8c4a-26b8a50c16f5",
		"sub_category_id": "4e095cf7-7d40-429a-9af0-779b69c5e5b8",
		"amount": 900001,
		"description": "Koala",
		"notes": "testing",
		"submitted_by": "c685a612-da8f-451b-9119-e51633bb4051",
		"id": "0198a6e0-759c-73e5-95f7-7d3b47598607",
		"updated_at": "2025-08-14T04:39:44.000000Z",
		"created_at": "2025-08-14T04:39:44.000000Z"
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
		"amount": [
			"The amount field is required"
		]
	}
}
```

---

## Endpoint: Get All Submission

### **GET** `/finance/submission`

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

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Submission retrieved successfully",
	"data": [
		{
			"id": "0198a6e0-759c-73e5-95f7-7d3b47598607",
			"status": "pending",
			"category_id": "3bc226f1-2395-4f64-8c4a-26b8a50c16f5",
			"category": "Biaya akad notaris",
			"sub_category": "AJB BN",
			"amount": "900001.00",
			"description": "Koala",
			"file_proof": null,
			"created_at": "2025-08-14",
			"created_by": "c685a612-da8f-451b-9119-e51633bb4051",
			"approved_by": null,
			"approved_at": "1970-01-01",
			"notes": "testing"
		},
		{
			"id": "0198a6d4-b415-7281-8455-64269e864832",
			"status": "approved",
			"category_id": "3955dc25-5aac-4084-8eec-9e0c72f3cf79",
			"category": "Biaya lainnya",
			"sub_category": "Administrasi bank",
			"amount": "900001.00",
			"file_proof": null,
			"description": "Koala",
			"created_at": "2025-08-14",
			"created_by": "c685a612-da8f-451b-9119-e51633bb4051",
			"approved_by": "c685a612-da8f-451b-9119-e51633bb4051",
			"approved_at": "2025-08-14",
			"notes": "testing"
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

## Endpoint: Get Cash Submission By ID

### **GET** `/finance/submission/{cash_out_id}`

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
	"message": "Submission retrieved successfully",
	"data": {
		"id": "0198a6e0-759c-73e5-95f7-7d3b47598607",
		"category_id": "3bc226f1-2395-4f64-8c4a-26b8a50c16f5",
		"sub_category_id": "4e095cf7-7d40-429a-9af0-779b69c5e5b8",
		"category": "Biaya akad notaris",
		"sub_category": "AJB BN",
		"status": "approved",
		"feedback": null,
		"file_proof": null,
		"submitted_by": "c685a612-da8f-451b-9119-e51633bb4051",
		"approved_by": "c685a612-da8f-451b-9119-e51633bb4051",
		"approved_at": "2025-08-14 11:40:02",
		"amount": "900001.00",
		"description": "Koala",
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

## Endpoint: Update Submission

### **PUT** `/finance/submission/{cash_out_id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                 |
|-----------|----------------------------|
| amount | `mandatory` Total Amount |
| description  | `mandatory` Description |
| notes        | Notes |

### Contoh Request Body
```json
{
	"amount" : 1000000,
	"description" : "koala",
	"notes" : "testing"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Submission updated successfully",
	"data": {
		"id": "0198934d-f1af-7048-a169-d58806342e76",
		"category_id": "3955dc25-5aac-4084-8eec-9e0c72f3cf79",
		"sub_category_id": "aed0d661-20db-429f-a894-6bc135658d6c",
		"type": "submission",
		"description": "gaung",
		"amount": 1000000,
		"notes": "testing",
		"status": "pending",
		"submitted_by": "c685a612-da8f-451b-9119-e51633bb4051",
		"approved_by": null,
		"approved_at": null,
		"created_at": "2025-08-10T09:26:55.000000Z",
		"updated_at": "2025-08-14T04:20:56.000000Z"
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

## Endpoint: Delete Submission

### **DELETE** `/finance/submission/{cash_out_id}`

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
	"message": "Submission deleted successfully",
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

## Endpoint: GET List Category Submission

### **GET** `/finance/submission/categories`

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

## Endpoint: GET List Sub Category Submission

### **GET** `/finance/submission/sub-categories/{category_id}`

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

## Endpoint: Approve Submission

### **PUT** `/finance/submission/{cash_out_id}/approve`

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
	"message": "Transaction approved successfully",
	"data": {
		"id": "0198a6e0-759c-73e5-95f7-7d3b47598607",
		"category_id": "3bc226f1-2395-4f64-8c4a-26b8a50c16f5",
		"sub_category_id": "4e095cf7-7d40-429a-9af0-779b69c5e5b8",
		"type": "submission",
		"description": "Koala",
		"amount": "900001.00",
		"notes": "testing",
		"status": "approved",
		"feedback": null,
		"submitted_by": "c685a612-da8f-451b-9119-e51633bb4051",
		"approved_by": "c685a612-da8f-451b-9119-e51633bb4051",
		"approved_at": "2025-08-14T04:40:02.678522Z",
		"created_at": "2025-08-14T04:39:44.000000Z",
		"updated_at": "2025-08-14T04:40:02.000000Z"
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

## Endpoint: Reject Submission

### **PUT** `/finance/submission/{cash_out_id}/reject`

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
	"message": "Transaction rejected successfully",
	"data": {
		"id": "0198a6ec-e1f5-7067-81b1-03fdacc39aa0",
		"category_id": "3bc226f1-2395-4f64-8c4a-26b8a50c16f5",
		"sub_category_id": "4e095cf7-7d40-429a-9af0-779b69c5e5b8",
		"type": "submission",
		"description": "Koala",
		"amount": "900001.00",
		"notes": "testing",
		"status": "rejected",
		"feedback": null,
		"submitted_by": "c685a612-da8f-451b-9119-e51633bb4051",
		"approved_by": null,
		"approved_at": null,
		"created_at": "2025-08-14T04:53:18.000000Z",
		"updated_at": "2025-08-14T04:53:26.000000Z"
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
