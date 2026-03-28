
# Dokumentasi API Sub Contractor

## Endpoint: Create Sub Contractor

### **POST** `/property/sub-contractor`

### Header
```
Content-Type: application/json
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| name     | `mandatory` Nama Sub Contractor    |
| added_at | `mandatory` Tanggal Ditambahkan "YYYY-MM-DD" |

### Contoh Request Body
```json
{
	"name" : "Sub Contractor 1",
	"added_at" : "2023-01-01"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Sub Contractor created successfully",
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

## Endpoint: Get All Sub Contractor

### **GET** `/property/sub-contractor`

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
	"message": "Sub Contractors retrieved successfully",
	"data": [
		{
			"id": "0197b238-16db-739c-beb9-b6fd18d72cd7",
			"sub_contractor_name": "jawa Jawa",
			"total_in_progress_constructions": 0,
			"total_done_constructions": 0,
			"on_time_constructions": 0,
			"added_at": "2025-06-27 00:00:00"
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

## Endpoint: Get Sub Contractor By ID

### **GET** `/property/sub-contractor/{sub_contractor_id}`

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
		"id": "0197b238-16db-739c-beb9-b6fd18d72cd7",
		"sub_contractor_name": "mang jajang konelo",
		"total_in_progress_constructions": 0,
		"total_done_constructions": 0,
		"on_time_constructions": 0,
		"added_at": "2025-10-20 00:00:00"
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

**Jika Sub Contractor Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: Update Sub Contractor

### **PUT** `/property/sub-contractor/{sub_contractor_id}`

### Header
```
Content-Type: application/json
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| name     | `mandatory` Nama Sub Contractor    |
| added_at | `mandatory` Tanggal Ditambahkan "YYYY-MM-DD" |

### Contoh Request Body
```json
{
	"name" : "Sub Contractor 1",
	"added_at" : "2023-01-01"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Sub Contractor updated successfully",
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

## Endpoint: Delete Sub Contractor

### **DELETE** `/property/sub-contractor/{sub_contractor_id}`

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
	"message": "Sub Contractor deleted successfully",
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