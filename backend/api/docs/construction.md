
# Dokumentasi API Management Konstruksi

## Endpoint: Get Sub Contractor

### **GET** `/property/construction/sub-contractors`

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
			"id": "0197b5ba-44e0-7292-a664-1ef308cf10fa",
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

## Endpoint: Get Project List (for Select)

### **GET** `/property/construction/project-lists`

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
	"message": "List Project retrieved successfully",
	"data": [
		{
			"id": "0197fdc5-c329-7065-a473-c7ac4c5b401c",
			"name": "Harmony Land 4"
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

## Endpoint: Get Cluster List (for Select)

### **GET** `/property/construction/cluster-lists/{project_id}`

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
	"message": "List Cluster retrieved successfully",
	"data": [
		{
			"id": "0197fdc6-15ec-7030-acd3-d9579e6b0f7c",
			"name": "Agatha"
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

## Endpoint: Get Unit Type List (for Select)

### **GET** `/property/construction/unit-type-lists`

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
	"message": "List Unit Type retrieved successfully",
	"data": [
		{
			"id": "0197fdc6-294f-7113-bad1-c9cf5fea84f3",
			"type": "subsidi 30/60"
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

## Endpoint: Get Unit Property List (for Select)

### **GET** `/property/construction/property-lists/{project_id}/{cluster_id}/{unit_type_id}`

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
			"id": "0197fdc6-df79-73af-a0a4-8a09a73ec68d",
			"unit_number": "12A"
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

## Endpoint: Create New Construction

### **POST** `/property/construction`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| project_id     | `mandatory` ID Project    |
| cluster_id     | `mandatory` ID Cluster    |
| unit_type_id     | `mandatory` ID Unit Type    |
| property_unit_id     | `mandatory` ID Unit Property    |
| start_date | `mandatory` Tanggal Mulai Konstruksi |
| estimated_end_date | `mandatory` Tanggal Selesai Konstruksi |
| sub_contractor_id | `mandatory` ID Kontraktor |
| spk | `mandatory` File SPK Konstruksi |

### Contoh Request Body
```json
{
	"project_id" : "01978c97-44c9-7092-82bf-9fdbd63db0a9",
	"cluster_id" : "01978c97-44c9-7092-82bf-9fdbd63db0a9",
	"unit_type_id" : "01978c97-44c9-7092-82bf-9fdbd63db0a9",
	"property_unit_id" : "01978c97-44c9-7092-82bf-9fdbd63db0a9",
	"start_date" : "2023-01-01",
	"estimated_end_date" : "2023-01-01",
	"sub_contractor_id" : "01978c97-44c9-7092-82bf-9fdbd63db0a9",
	"spk" : "file.pdf"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Construction created successfully",
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
			"The type field is required."
		]
	}
}
```

---

## Endpoint: Get Summary Construction

### **GET** `/property/construction/summary`

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
			"status": "pondasi",
			"total": 0
		},
		{
			"status": "naik_bata",
			"total": 0
		},
		{
			"status": "naik_atap",
			"total": 0
		},
		{
			"status": "plester_aci",
			"total": 0
		},
		{
			"status": "keramik_cat",
			"total": 1
		},
		{
			"status": "finishing",
			"total": 0
		},
		{
			"status": "done",
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

## Endpoint: Get All Construction

### **GET** `/property/construction`

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
| cluster_id | ID Cluster |
| sub_contractor_id | ID Kontraktor |
| sortKey   | Kolom yang akan diurutkan (name, duration) |
| sortDir   | Urutan (asc, desc) |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Constructions retrieved successfully",
	"data": [
		{
			"id": "0197b6a3-be71-7075-a975-4bfb516d94e9",
			"lead_name": "bocil jajang",
			"status": "pondasi",
			"start_date": "2025-06-28",
			"estimated_end_date": "2025-07-28",
			"project_name": "Harmony Land 4",
			"cluster_name": "Agatha",
			"unit_type": "subsidi 30/60",
			"sub_contractor_name": "mang jajang konelo",
			"sub_contractor_id": "0197b5ba-44e0-7292-a664-1ef308cf10fa",
			"unit_number": "12A",
			"duration": "0"
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

## Endpoint: Get Construction By ID

### **GET** `/property/construction/{construction_id}`

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
	"message": "Unit retrieved successfully",
	"data": {
		"id": "0197b5ba-7837-70ef-a3ac-aedc13f8adfa",
		"project_id": "01977dd3-de51-7174-b59e-f715ea44d966",
		"unit_property_id": "0197b0f6-6c4d-73a3-9f87-0bc434616e14",
		"start_date": "2025-06-28",
		"estimated_end_date": "2025-07-28",
		"actual_end_date": null,
		"status": "pondasi",
		"notes": null,
		"project_name": "Harmony Land 4",
		"cluster_name": "Agatha",
		"unit_type": "subsidi 30/60",
		"unit_number": "12A",
		"unit_price": null,
		"sub_contractor_id": "0197b5ba-44e0-7292-a664-1ef308cf10fa",
		"sub_contractor_name": "mang jajang konelo",
		"construction_notes": "wc duduk nya pake yang merek a",
		"construction_phases": [
			{
				"construction_phase": "pondasi",
				"status": "not_started",
				"documentation": null
			},
			{
				"construction_phase": "naik_bata",
				"status": "not_started",
				"documentation": null
			},
			{
				"construction_phase": "naik_atap",
				"status": "not_started",
				"documentation": null
			},
			{
				"construction_phase": "plester_aci_keramik_cat",
				"status": "not_started",
				"documentation": null
			},
			{
				"construction_phase": "finishing",
				"status": "not_started",
				"documentation": null
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

**Jika Contact Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: Update Construction

### **POST** `/property/construction/{construction_id}`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| status_pondasi | Status pondasi (not_started,in_progress,completed) |
| dokumentasi_pondasi | Dokumentasi pondasi (jpeg,png,jpg,pdf) |
| status_naik_bata | Status naik bata (not_started,in_progress,completed) |
| dokumentasi_naik_bata | Dokumentasi naik bata (jpeg,png,jpg,pdf) |
| status_naik_atap | Status naik atap (not_started,in_progress,completed) |
| dokumentasi_naik_atap | Dokumentasi naik atap (jpeg,png,jpg,pdf) |
| status_plester_aci | Status plester aci (not_started,in_progress,completed) |
| dokumentasi_plester_aci | Dokumentasi plester aci (jpeg,png,jpg,pdf) |
| status_keramik_cat | Status keramik cat (not_started,in_progress,completed) |
| dokumentasi_keramik_cat | Dokumentasi keramik cat (jpeg,png,jpg,pdf) |
| status_finishing | Status finishing (not_started,in_progress,completed) |
| dokumentasi_finishing | Dokumentasi finishing (jpeg,png,jpg,pdf) |
| notes | Catatan (optional) |

### Contoh Request Body
```multipart
{
	"status_pondasi" : "in_progress",
	"dokumentasi_pondasi" : "{file}",
	"status_naik_bata" : "in_progress",
	"dokumentasi_naik_bata" : "{file}",
	"status_naik_atap" : "in_progress",
	"dokumentasi_naik_atap" : "{file}",
	"status_plester_aci" : "in_progress",
	"dokumentasi_plester_aci" : "{file}",
	"status_keramik_cat" : "in_progress",
	"dokumentasi_keramik_cat" : "{file}",
	"status_finishing" : "in_progress",
	"dokumentasi_finishing" : "{file}",
	"notes" : "Catatan"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Construction updated successfully",
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
		"name": "The status pondasi field is required.",
	}
}
```

---

## Endpoint: Delete Construction

### **DELETE** `/property/construction/{construction_id}`

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
	"message": "Construction deleted successfully",
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