
# Dokumentasi API Management Cluster

## Endpoint: Create New Cluster

### **POST** `/property/cluster`

### Header
```
Content-Type: application/json
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| name     | `mandatory` Nama Kluster    |
| project |  `mandatory` Project id  |
| block_code |  `mandatory` Block Code   |
| notes |  Notes   |

### Contoh Request Body
```json
{
	"name": "A",
	"project": "01978287-3761-70a7-9912-057b90e7318a",
	"block_code": "A"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Cluster created successfully",
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
			"The name field is required."
		]
	}
}
```

---

## Endpoint: Get All Cluster

### **GET** `/property/cluster`

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

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Cluster retrieved successfully",
	"data": [
		{
			"id": "01978c79-3f3e-73c3-92db-3a9b71802a4d",
			"project_id": "01977dd3-de51-7174-b59e-f715ea44d966",
			"name": "Agatha",
			"project_name": "Harmony Land 4",
			"block_code": "A"
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

## Endpoint: Get Cluster By ID

### **GET** `/property/cluster/{cluster_id}`

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
	"message": "Cluster retrieved successfully",
	"data": {
		"id": "01978c6f-b65b-73b9-b8e1-c74aa62a3cd3",
		"project_id": "01977dd3-de51-7174-b59e-f715ea44d966",
		"name": "Agatha",
		"project_name": "Harmony Land 4",
		"block_code": "B",
		"notes": null
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

## Endpoint: Update Cluster

### **POST** `/property/cluster/{cluster_id}`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| project |  `mandatory` Project id  |
| name     | `mandatory` Nama kluster    |
| block_code |  `mandatory` Block Code   |
| notes |  `optional` Notes   |


### Contoh Request Body
```json
{
	"project" : "01977dd3-de51-7174-b59e-f715ea44d966",
	"name" : "Agatha",
	"block_code" : "B",
	"notes" : "indonesia"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Cluster updated successfully",
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

## Endpoint: Delete Cluster

### **DELETE** `/property/cluster/{cluster_id}`

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
	"message": "Cluster deleted successfully",
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