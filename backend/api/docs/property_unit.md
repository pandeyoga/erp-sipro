
# Dokumentasi API Management Property Unit

## Endpoint: Create New Property

### **POST** `/property/unit-property`

### Header
```
Content-Type: application/json
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| project_id |  `mandatory` Project id  |
| cluster_id |  `mandatory` Cluster id  |
| unit_type_id |  `mandatory` Unit Type id  |
| unit_number |  `mandatory` Unit Number   |

### Contoh Request Body
```json
{
	"project_id": "01977dd3-de51-7174-b59e-f715ea44d966",
	"cluster_id": "01978c79-3f3e-73c3-92db-3a9b71802a4d",
	"unit_type_id": "01978c79-3f3e-73c3-92db-3a9b71802a4d",
	"unit_number": "10A"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Property created successfully",
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
			"The unit_number field is required."
		]
	}
}
```

---

## Endpoint: Get All Property

### **GET** `/property/unit-property`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| search    | cari dengan query ilike ke kolom name |
| page      | Halaman |
| per_page  | Jumlah data per halaman |
| project | ID Project |
| cluster | ID Cluster |
| unit_type | ID Unit Type |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Property retrieved successfully",
	"data": [
		{
			"id": "0197b088-8a00-72fd-b81c-fad7382e84eb",
			"project_name": "Harmony Land 4",
			"cluster_name": "Agatha",
			"unit_type": "subsidi 30/60",
			"unit_number": "10A",
			"price": null,
			"status": "belum_dibangun",
			"construction_status": null,
			"notes": null
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

## Endpoint: Get Project List (for Select)

### **GET** `/property/unit-property/projects-list`

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
	"message": "Project lists retrieved successfully",
	"data": [
		{
			"id": "01977dd3-de51-7174-b59e-f715ea44d966",
			"name": "Harmony Land 4"
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

## Endpoint: Get Cluster List (for Select)

### **GET** `/property/unit-property/clusters-list`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| project | ID Project |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Cluster lists retrieved successfully",
	"data": [
		{
			"id": "01978c89-b258-716f-a086-5c322ce8b02e",
			"name": "Agatha"
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

## Endpoint: Get Unit Type List (for Select)

### **GET** `/property/unit-property/unit-types-list`

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
	"message": "Unit type lists retrieved successfully",
	"data": [
		{
			"id": "01978c9c-3b47-72c6-96f9-f68aefd45a74",
			"type": "subsidi 30/60"
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

## Endpoint: Get Property By ID

### **GET** `/property/unit-property/{property_id}`

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
	"message": "Property retrieved successfully",
	"data": {
		"id": "01993d13-036d-71a7-ab53-5543ae008602",
		"project_id": "01993cf4-245a-7357-853b-19bd4af836e1",
		"project_name": "Harmony Land 4",
		"cluster_id": "01993cf5-312e-708c-8039-856bc0af03a3",
		"cluster_name": "maksl",
		"unit_type_id": "01993cf5-6dad-71ed-866f-3417de362f3f",
		"unit_type": "30/78",
		"unit_number": "90",
		"price": "166000000.00",
		"status": "available",
		"construction_status": "done",
		"is_booked": true,
		"customer": "Gia KPR",
		"sub_contractor": "TB Koala Jepang",
		"notes": null,
		"construction_progress": "100%",
		"construction_documentation": [
			{
				"construction_phase": "pondasi",
				"documentation": "/api/file/property/construction/documentation-pondasi/69a55016-acc1-4eeb-8ae3-4f5d6d4d6102.jpeg"
			},
			{
				"construction_phase": "naik_bata",
				"documentation": "/api/file/property/construction/documentation-naik_bata/e81ab28b-171a-4f95-b1dc-febeecdcf8e4.jpeg"
			},
			{
				"construction_phase": "naik_atap",
				"documentation": "/api/file/property/construction/documentation-naik_atap/ec3b5ad3-2c10-4f61-831c-342683703a9b.jpeg"
			},
			{
				"construction_phase": "finishing",
				"documentation": "/api/file/property/construction/documentation-finishing/2e10466f-95d0-4b5f-8e27-762d273e2623.jpeg"
			}
		],
		"retention_cases": [
			{
				"opened_at": "2024-10-15",
				"description": "koala",
				"status": "open",
				"resolved_at": null,
				"estimated_resolved_at": "2024-10-25",
				"case_pictures": [
					"/api/file/property/retention-case/bukti-case/fe70a21e-1f05-4f7a-b9c1-43fbb1a08db7.png"
				],
				"case_documentations": [],
				"sub_contractor_name": "TB Koala Jepang",
				"notes": null
			}
		],
		"payment": {
			"total_amount": "171000000.00",
			"paid_amount": "0.00",
			"remaining_amount": 171000000,
			"details": [
				{
					"description": "Retensi Bangunan",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"remaining_amount": "0.00"
				},
				{
					"description": "JKK",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"remaining_amount": "0.00"
				},
				{
					"description": "Bestek",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"remaining_amount": "0.00"
				},
				{
					"description": "IMB/PBG",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"remaining_amount": "0.00"
				},
				{
					"description": "Restensi Listrik",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"remaining_amount": "0.00"
				},
				{
					"description": "Retensi Air",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"remaining_amount": "0.00"
				},
				{
					"description": "Retensi Sertifikat",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"remaining_amount": "0.00"
				},
				{
					"description": "Selisih KYG",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"remaining_amount": "0.00"
				},
				{
					"description": "Pencairan AKAD",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"remaining_amount": "0.00"
				},
				{
					"description": "Penambahan Spek bangunan",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"remaining_amount": "0.00"
				},
				{
					"description": "Penambahan tanah",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"remaining_amount": "0.00"
				},
				{
					"description": "Hook",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"remaining_amount": "0.00"
				},
				{
					"description": "All In",
					"total_amount": "0.00",
					"paid_amount": "0.00",
					"remaining_amount": "0.00"
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

**Jika Contact Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: Update Property

### **PUT** `/property/unit-property/{property_id}`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| project_id |  `mandatory` Project id  |
| cluster_id |  `mandatory` Cluster id  |
| unit_type_id |  `mandatory` Unit Type id  |
| unit_number |  `mandatory` Unit Number   |


### Contoh Request Body
```json
{
	"project_id": "01977dd3-de51-7174-b59e-f715ea44d966",
	"cluster_id": "01978c89-b258-716f-a086-5c322ce8b02e",
	"unit_type_id": "01978c9c-3b47-72c6-96f9-f68aefd45a74",
	"unit_number": "10A"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Property updated successfully",
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
		"name": "The unit_number field is required.",
	}
}
```

---

## Endpoint: Delete Property

### **DELETE** `/property/unit-property/{property_id}`

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
	"message": "Property deleted successfully",
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

**Jika Contact Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```
---

## Endpoint: Create Item Qc Property

### **POST** `/property/unit-property/{property_id}/quality-control-item`

### Header
```
Content-Type: multipart/form-data  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| name |  `mandatory` nama item  |
| is_passed |  `mandatory` bolean  |
| evidence |  bukti item  image jpg/png/jpeg  |
| comment |   keterangan  |

### Contoh Request Body
```json
{
	"name": "Item 1",
	"is_passed": true,
	"evidence": "{File Evidence}",
	"comment": "Item 1 passed."
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Quality control items created successfully",
	"data": {
		"id": "019948cf-c29f-730f-8a0b-fe5641ebb4fa"
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

## Endpoint: Update Item Qc Property

### **POST** `/property/unit-property/{property_id}/quality-control-item/{item_id}`

### Header
```
Content-Type: multipart/form-data 
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| name |  `mandatory` nama item  |
| is_passed |  `mandatory` bolean  |
| evidence |  bukti item  image jpg/png/jpeg  |
| comment |   keterangan  |

### Contoh Request Body
```json
{
	"name": "Item 1",
	"is_passed": true,
	"evidence": "{File Evidence}",
	"comment": "Item 1 passed."
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Quality control items updated successfully",
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

## Endpoint: Get Item Qc Property

### **GET** `/property/unit-property/{property_id}/quality-control-item`

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
	"message": "Quality control items retrieved successfully",
	"data": [
		{
			"id": "019948cf-c29f-730f-8a0b-fe5641ebb4fa",
			"name": "Bata aman",
			"is_passed": true,
			"comment": "gatau",
			"evidence": "/api/file/property/qc-evidence/67f051c7-d4ed-4a6b-9118-f54a0196c2f2.jpeg",
			"created_at": "2025-09-14T15:19:59.000000Z"
		},
		{
			"id": "019948cf-4cd9-7190-9df2-d14c791c9427",
			"name": "Bata aman",
			"is_passed": true,
			"comment": "gatau",
			"evidence": "/api/file/property/qc-evidence/4817aee5-ec66-4b91-9cf7-f30a4f1bf5fd.jpeg",
			"created_at": "2025-09-14T15:19:29.000000Z"
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

**Jika Property Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

___

## Endpoint: Get Item Qc Property By Id

### **GET** `/property/unit-property/{property_id}/quality-control-item/{item_id}`

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
	"message": "Quality control item retrieved successfully",
	"data": {
		"id": "019948cf-c29f-730f-8a0b-fe5641ebb4fa",
		"name": "Bata aman",
		"is_passed": true,
		"comment": "gatau",
		"evidence": "/api/file/property/qc-evidence/67f051c7-d4ed-4a6b-9118-f54a0196c2f2.jpeg",
		"created_at": "2025-09-14T15:19:59.000000Z"
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

**Jika Property Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

___

## Endpoint: Delete Item Qc Property By Id

### **DELETE** `/property/unit-property/{property_id}/quality-control-item/{item_id}`

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
	"message": "Quality control item deleted successfully",
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

**Jika Property Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

___

## Endpoint: Import Item Qc Property

### **POST** `/property/unit-property/{property_id}/quality-control-item/import`

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| file     | File Excel Quality Control Item |

> template ada di {base_url}/files/static/importable-qc-template.xlsx

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
	"message": "Quality control item import successfully",
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

**Jika Property Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

___
