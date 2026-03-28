
# Dokumentasi API Management Unit

## Endpoint: Create New Unit

### **POST** `/property/unit`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| type     | `mandatory` Tipe Unit    |
| building_area | `mandatory` Luas Bangunan |
| land_area | `mandatory` Luas Tanah |
| notes |  Catatan Tambahan |

### Contoh Request Body
```json
{
	"type" : "subsidi 30/60",
	"building_area" : 30,
	"land_area" : 60,
	"notes" : ""
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Unit created successfully",
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

## Endpoint: Get All Unit

### **GET** `/property/unit`

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

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Unit retrieved successfully",
	"data": [
		{
			"id": "01978c97-44c9-7092-82bf-9fdbd63db0a9",
			"type": "subsidi simpang siod",
			"land_area": "60",
			"building_area": "30"
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

## Endpoint: Get Unit By ID

### **GET** `/property/unit/{unit_id}`

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
		"id": "01978c97-44c9-7092-82bf-9fdbd63db0a9",
		"type": "subsidi simpang siod",
		"land_area": "60",
		"building_area": "30",
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

## Endpoint: Update Unit

### **POST** `/property/unit/{unit_id}`

### Header
```
Content-Type: application/json
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| type     | `mandatory` Tipe Unit    |
| building_area | `mandatory` Luas Bangunan |
| land_area | `mandatory` Luas Tanah |
| notes |  Catatan Tambahan |

### Contoh Request Body
```json
{
	"type" : "subsidi 30/60",
	"building_area" : 30,
	"land_area" : 60,
	"notes" : ""
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
		"name": "The type field is required.",
	}
}
```

---

## Endpoint: Delete Unit

### **DELETE** `/property/unit/{unit_id}`

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

**Jika Contact Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```
---