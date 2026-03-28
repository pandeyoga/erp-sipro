
# Dokumentasi API Management Site Plan

## Endpoint: Create New Unit in Site Plan

### **POST** `/property/siteplan/{project_id}`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| property_id | `mandatory` ID Property    |
| top |  `mandatory` Top   |
| left |  `mandatory` Left   |
| width | `mandatory` Width   |
| height | `mandatory` Height   |
| rotate |  `mandatory` Rotate   |

### Contoh Request Body
```json
{
	"property_id": "01977dd3-de51-7174-b59e-f715ea44d966",
	"top": "30",
	"left": "30",
	"width": "100",
	"height": "100",
	"rotate": "0"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Unit created successfully",
	"data": {
		"unit_id": "0197b0f6-6c4d-73a3-9f87-0bc434616e14"
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
		"name": [
			"The property_id field is required."
		]
	}
}
```

---

## Endpoint: Get All Available Property (for dropdown)

### **GET** `/property/siteplan/{project_id}/list-option-property`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
tidak ada

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Unit property list retrieved successfully",
	"data": {
		"Agatha": {
			"subsidi 30/60": [
				{
					"id": "0197b0f6-550a-735d-a022-9a629494ea91",
					"unit_number": "11A",
					"status": "belum_dibangun"
				},
				{
					"id": "0197b0f6-6c4d-73a3-9f87-0bc434616e14",
					"unit_number": "12A",
					"status": "belum_dibangun"
				}
			]
		}
	}
}
```
> "Agatha" adalah nama cluster dan "subsidi 30/60" adalah unit type

### Contoh Response Gagal (jika token tidak valid)
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Endpoint: Get All Unit in Site Plan

### **GET** `/property/siteplan/{project_id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
tidak ada

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Siteplan retrieved successfully",
	"data": {
		"project_id": "01977dd3-de51-7174-b59e-f715ea44d966",
		"site_plan_image": "http://localhost:8000/api/file/property/siteplan/c3c549df-12d5-4748-8b6d-0ff9f2d93a5e.png",
		"units": [
			{
				"id": "0197b088-8a00-72fd-b81c-fad7382e84eb",
				"cluster_name": "Agatha",
				"unit_type": "subsidi 30/60",
				"unit_number": "10A",
				"status": "belum_dibangun",
				"top": "10.30",
				"left": "10.30",
				"width": "10.30",
				"height": "10.30",
				"rotate": "10.00"
			}
		]
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

## Endpoint: Get Unit in Site Plan By ID

### **GET** `/property/siteplan/{project_id}/{property_id}`

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
		"id": "0197b088-8a00-72fd-b81c-fad7382e84eb",
		"cluster_name": "Agatha",
		"unit_type": "subsidi 30/60",
		"unit_number": "10A",
		"status": "belum_dibangun",
		"construction_status": null,
		"notes": null,
		"top": "10.00",
		"left": "10.00",
		"width": "10.00",
		"height": "1103.00",
		"rotate": "10.00"
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

## Endpoint: Update Unit in Site Plan

### **PUT** `/property/siteplan/{project_id}/{property_id}`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| top     | `mandatory` Top   |
| left |  `mandatory` Left   |
| width |  `mandatory` Width   |
| height | `mandatory` Height   |
| rotate | `mandatory` Rotate   |


### Contoh Request Body
```json
{
	"top": "10.00",
	"left": "10.00",
	"width": "10.00",
	"height": "1103.00",
	"rotate": "10.00"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Unit updated successfully",
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
		"name": "The top field is required.",
	}
}
```

---

## Endpoint: Update image Site Plan

### **POST** `/property/siteplan/{project_id}/change-image`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| image     | `mandatory` Image   |


### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Siteplan image changed successfully",
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
		"name": "The image field is required.",
	}
}
```

---

## Endpoint: Delete Unit in Site Plan

### **DELETE** `/property/siteplan/{project_id}/{property_id}`

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
	"message": "Unit deleted successfully",
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