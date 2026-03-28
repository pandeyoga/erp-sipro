
# Dokumentasi API Management Retention Case

## Endpoint: Get Sub Contractor

### **GET** `/property/retention/sub-contractors`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
tidak ada param request

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Sub Contractor retrieved successfully",
	"data": [
		{
			"id": "0197d4b8-99dc-7322-be5a-7082703677c1",
			"name": "mang jajang konelo"
		}
	]
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

## Endpoint: Get Reserved Lead

### **GET** `/property/retention/reserved-lead`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
tidak ada param request

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Lead retrieved successfully",
	"data": [
		{
			"lead_id": "01972b91-8fa1-710a-b2d5-8dddb784bbfe",
			"unit_property_id": "0197b0f6-6c4d-73a3-9f87-0bc434616e14",
			"contact_name": "bocil jajang",
			"project_name": "Harmony Land 4",
			"cluster_name": "Agatha",
			"unit_type": "subsidi 30/60",
			"unit_number": "12A",
			"unit_price": null
		}
	]
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

## Endpoint: Create New Retention Case

### **POST** `/property/retention`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| lead_id | `mandatory` ID Lead    |
| description     | `mandatory` Deskripsi Retention Case    |
| case_pictures[] | `mandatory` array gambar retention case (jpeg,png,jpg) |
| notes | Catatan (optional) |
| case_date | `mandatory` Tanggal Retention |
| estimated_resolved_day | `mandatory` Tanggal Selesai Retention |
| sub_contractor_id | `mandatory` ID Kontraktor |

### Contoh Request Body
```multipart
{
	"lead_id" : "01978c97-44c9-7092-82bf-9fdbd63db0a9",
	"description" : "test retention case",
	"case_pictures": [
		"{file1.img"}",
		"{file2.img"}"
	],
	"notes" : "test notes",
	"case_date" : "2023-01-01",
	"estimated_resolved_day" : "2023-01-01",
	"sub_contractor_id" : "01978c97-44c9-7092-82bf-9fdbd63db0a9"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Retention Case created successfully",
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
		"name": [
			"The lead id field is required."
		]
	}
}
```

---

## Endpoint: Get Summary Retention

### **GET** `/property/retention/summary`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
tidak ada param request

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Construction retrieved successfully",
	"data": [
		{
			"status": "open",
			"total": 1
		},
		{
			"status": "in_progress",
			"total": 0
		},
		{
			"status": "resolved",
			"total": 0
		}
	]
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

## Endpoint: Get All Retention

### **GET** `/property/retention`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| search    | cari dengan query ilike ke kolom type |
| page      | Halaman |
| per_page  | Jumlah data per halaman |
| status    | Status Konstruksi (pondasi,naik_bata,naik_atap,plester_aci_keramik_cat,finishing,done) |
| sub_contractor_id | ID Kontraktor |
| sortKey   | Kolom yang akan diurutkan (name, duration) |
| sortDir   | Urutan (asc, desc) |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Retention Cases retrieved successfully",
	"data": [
		{
			"id": "0197d50d-ccaf-7361-9c87-cc2f6f980fc4",
			"description": "koala",
			"notes": null,
			"lead_name": "naja",
			"status": "open",
			"opened_at": "2024-10-15",
			"estimated_resolved_at": "2024-10-25",
			"project_name": "Harmony Land 4",
			"cluster_name": "Agatha",
			"unit_type": "subsidi 30/60",
			"sub_contractor_name": "mang jajang konelo",
			"sub_contractor_id": "0197d4b8-99dc-7322-be5a-7082703677c1",
			"unit_number": "12A",
			"duration": "263"
		},
		{
			"id": "0197d63b-90e3-70e9-93c2-92a81cb7577d",
			"description": "koala",
			"notes": null,
			"lead_name": "naja",
			"status": "open",
			"opened_at": "2024-10-15",
			"estimated_resolved_at": "2024-10-25",
			"project_name": "Harmony Land 4",
			"cluster_name": "Agatha",
			"unit_type": "subsidi 30/60",
			"sub_contractor_name": "mang jajang konelo",
			"sub_contractor_id": "0197d4b8-99dc-7322-be5a-7082703677c1",
			"unit_number": "12A",
			"duration": "263"
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

## Endpoint: Get Retention By ID

### **GET** `/property/retention/{retention_id}`

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
	"message": "Retention Case retrieved successfully",
	"data": {
		"id": "0197d50d-ccaf-7361-9c87-cc2f6f980fc4",
		"description": "koala",
		"lead_name": "naja",
		"status": "open",
		"opened_at": "2024-10-15",
		"estimated_resolved_at": "2024-10-25",
		"notes": null,
		"project_name": "Harmony Land 4",
		"cluster_name": "Agatha",
		"unit_type": "subsidi 30/60",
		"sub_contractor_name": "mang jajang konelo",
		"sub_contractor_id": "0197d4b8-99dc-7322-be5a-7082703677c1",
		"unit_number": "12A",
		"case_pictures": [
			"http://localhost:8000/api/file/property/retention-case/bukti-case/bd731a77-af42-45c0-938a-1748b2dfc075.png"
		],
		"case_documentations": [],
		"duration": "263"
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

**Jika Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: Update Retention

### **POST** `/property/retention/{retention_id}`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| status | `mandatory` Status Retention Case    |
| case_documentations[] | `mandatory` array dokumentasi retention case (jpeg,png,jpg) |
| notes | Catatan (optional) |

### Contoh Request Body
```multipart
{
	"status" : "in_progress",
	"case_documentations": [
		'{file1.img"}',
		'{file2.img"}'
	]
	"notes" : "Catatan"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Retention Case updated successfully",
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
		"name": "The status field is required.",
	}
}
```

---

## Endpoint: Delete Retention

### **DELETE** `/property/retention/{retention_id}`

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
	"message": "Retention Case deleted successfully",
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

**Jika Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```
---