
# Dokumentasi API Management Project

## Endpoint: Create New Project

### **POST** `/property/projects`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| name     | `mandatory` Nama Project    |
| location |  `mandatory`  Lokasi   |
| developer |  `mandatory` Developer   |
| area_total_sqm | `mandatory` Luas Tanah m2  |
| start_date | `mandatory` Tanggal Mulai   |
| site_plan_image | Gambar Site Plan |
| notes |  Catatan   |

### Contoh Request Body
```multipart
{
	"name": "Project A",
	"location": "Jl. Contoh",
	"developer": "PT Contoh",
	"area_total_sqm": 100,
	"start_date": "2023-01-01",
	"site_plan_image": "{file}",
	"notes": "Catatan tambahan"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Project created successfully",
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

## Endpoint: Get All Projects

### **GET** `/property/projects`

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

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Projects retrieved successfully",
	"data": [
		{
			"id": "01977dd3-de51-7174-b59e-f715ea44d966",
			"name": "Harmony Land 4",
			"location": "bandung timur",
			"developer": "landpro",
			"area_total_sqm": "1200",
			"start_date": "2025-07-27",
			"status": "active"
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

## Endpoint: Get Project By ID

### **GET** `/property/projects/{project_id}`

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
	"message": "Project retrieved successfully",
	"data": {
		"id": "01978287-3761-70a7-9912-057b90e7318a",
		"name": "Harmony Land 4",
		"location": "bandung timur",
		"developer": "landpro",
		"area_total_sqm": "1200",
		"start_date": "2025-07-27",
		"status": "inactive",
		"created_by": "76d2f337-fd2c-4528-a29c-4c080fa27bdc",
		"site_plan_image": "http://localhost:8000/api/file/property/siteplan/harmony-land-4.png"
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

## Endpoint: Update Project

### **POST** `/property/projects/{project_id}`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| name     | `mandatory` Nama Project    |
| location |  `mandatory`  Lokasi   |
| developer |  `mandatory` Developer   |
| area_total_sqm | `mandatory` Luas Tanah m2  |
| start_date | `mandatory` Tanggal Mulai   |
| site_plan_image | Gambar Site Plan |
| status | `mandatory` Status (active/inactive)   |
| notes |  Catatan   |


### Contoh Request Body
```multipart
{
	"name": "Harmony Land 4",
	"location": "bandung timur",
	"developer": "landpro",
	"area_total_sqm": "1200",
	"start_date": "2025-07-27",
	"site_plan_image": "{image}",
	"status": "active",
	"notes": "harmony land 4"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Project updated successfully",
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

## Endpoint: Delete Project

### **DELETE** `/property/projects/{project_id}`

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
	"message": "Project deleted successfully",
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