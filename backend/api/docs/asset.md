
# Dokumentasi API Management Asset

## Endpoint: Get All Categories

### **GET** `/asset/categories`

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
	"message": "Success",
	"data": [
		{
			"id": "0198d651-dffe-7138-bc8f-ffea098a9150",
			"name": "Tanah"
		},
		{
			"id": "0198d651-e026-738a-91e9-5f99729357f3",
			"name": "Bangunan"
		},
		{
			"id": "0198d651-e030-70c6-a2b8-d0f1d7899c4a",
			"name": "Kendaraan"
		},
		{
			"id": "0198d651-e036-7184-bd7c-fcc4ebfb2da1",
			"name": "Peralatan & Perlengkapan"
		},
		{
			"id": "0198d651-e039-70e7-806a-0d5f3d0c419b",
			"name": "Surat Berharga"
		}
	]
}
```

### Contoh Response Gagal

**Jika Token Tidak Valid**
```json
{
  "success": false,
  "message": "Unauthorized",
  "errors": null
}
```

## Endpoint: Get Asset Sub Category By Category Id

### **GET** `/asset/sub-categories/{category_id}`

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
	"message": "Success",
	"data": [
		{
			"id": "0198d651-e02c-71b5-a5b3-e5a886e7ec57",
			"name": "Bangunan"
		}
	]
}
```

### Contoh Response Gagal

**Jika Token Tidak Valid**
```json
{
  "success": false,
  "message": "Unauthorized",
  "errors": null
}
```

## Endpoint: Create asset

### **POST** `/asset`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                  |
|-----------|-----------------------------|
| category_id    | `mandatory` Kategori Asset                   |
| sub_category_id    | `mandatory` Sub Kategori Asset                   |
| name    | `mandatory` Nama Asset                   |
| desc    | Deskripsi Asset              |
| quantity    | `mandatory` Jumlah Asset                   |
| price    | `mandatory` Harga Asset                   |
| acquisition_date    | `mandatory` Tanggal Pembelian Asset                   |
| useful_life    | `mandatory` Umur Ekonomis Asset dalam bulan                  |

### Contoh Request Body
```json
{
	"category_id" : "{category_id}",
	"sub_category_id" : "{sub_category_id}",
	"name": "meja",
	"description": "meja kantor",
	"quantity" : 1,
	"price" : 2450000000,
	"acquisition_date" :  "2025-07-10",
	"useful_life" : 90
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Asset created successfully",
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
		"name": "The name field is required."
	}
}
```

---

## Endpoint: Get All Asset

### **GET** `/asset`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Parameter
| Parameter | Keterangan                  |
|-----------|-----------------------------|
| page    | `optional` Halaman Asset                   |
| per_page    | `optional` Jumlah Asset per halaman                   |
| category_id    | `optional` Kategori Asset                   |
| sub_category_id    | `optional` Sub Kategori Asset                   |
| search    | `optional` cari Nama Asset                   |
| start_date    | `optional` Tanggal Mulai Asset                   |
| end_date    | `optional` Tanggal Akhir Asset                   |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Asset list",
	"data": [
		{
			"id": "0198d652-172c-734c-b211-277aa90ffb21",
			"acquisition_date": "2025-07-10",
			"registration_number": "1-210/1/2025",
			"category_name": "Tanah",
			"sub_category_name": "Tanah",
			"name": "Tanah Private",
			"description": "Tanah Private",
			"quantity": 1,
			"price": 2450000000,
			"remaining_price": 2395555555.3599997,
			"useful_life": 90,
			"remaining_useful_life": 88
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

## Endpoint: Get Asset By ID

### **GET** `/asset/{asset_id}`

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
	"message": "Success",
	"data": {
		"id": "0198d652-172c-734c-b211-277aa90ffb21",
		"acquisition_date": "2025-07-10",
		"registration_number": "1-210/1/2025",
		"category_name": "Tanah",
		"sub_category_name": "Tanah",
		"name": "koala",
		"description": "jawa",
		"quantity": 1,
		"price": 2450000000,
		"remaining_price": 2395555555.3599997,
		"useful_life": 90,
		"remaining_useful_life": 88
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

**Jika Role Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: Update Asset

### **PUT** `/asset/{asset_id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                  |
|-----------|-----------------------------|
| name    	| `mandatory` Nama Role       |
| desc    	| Deskripsi Role              |
| group    	| Grup Role                   |
| permissions | Array of permissions. |
| permissions.* | Nama Permission yang ingin diassign ke role. (harus ada di daftar permission) |

### Contoh Request Body
```json
{
	"category_id" : "{category_id}",
	"sub_category_id" : "{sub_category_id}",
	"name": "motor",
	"description": "motor vespa",
	"quantity" : 1,
	"price" : 60000000,
	"acquisition_date" :  "2025-08-10",
	"useful_life" : 90
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Asset updated successfully",
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
		"name": ["required"]
	}
}
```

---

## Endpoint: Delete Asset

### **DELETE** `/asset/{asset_id}`

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
	"message": "Asset deleted successfully",
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

