
# Dokumentasi API Management Reservation

## Endpoint: Get Summary Reservation By Status

### **GET** `/crm/reservation/summary`

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
	"message": "Summary fetched successfully",
	"data": {
		"pending": 1,
		"confirmed": 3,
		"canceled": 0,
		"expired": 0
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

## Endpoint: Get Property List (For Create Reservation)

### **GET** `/crm/reservation/get-properties

Menampilkan list property untuk select saat membuat reservation / update reservation

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Query Params
| Parameter | Deskripsi |
| search | cari nama user |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Properties fetched successfully",
	"data": [
		{
			"id": "0197b0f6-6c4d-73a3-9f87-0bc434616e14",
			"name": "Harmony Land 4 - 12A [Agatha - subsidi 30/60]"
		},
		{
			"id": "0197b0f6-550a-735d-a022-9a629494ea91",
			"name": "Harmony Land 4 - 11A [Agatha - subsidi 30/60]"
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

## Endpoint: Get Property List (For Edit Reservation)

### **GET** `/crm/reservation/{reservation_id}/properties

Menampilkan semua list property untuk select saat update reservation

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Query Params
| Parameter | Deskripsi |
| search | cari nama user |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Properties fetched successfully",
	"data": [
		{
			"id": "0197b0f6-6c4d-73a3-9f87-0bc434616e14",
			"name": "Harmony Land 4 - 12A [Agatha - subsidi 30/60]"
		},
		{
			"id": "0197b0f6-550a-735d-a022-9a629494ea91",
			"name": "Harmony Land 4 - 11A [Agatha - subsidi 30/60]"
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

## Endpoint: Get Lead Prospect (For Reservation)

### **GET** `/crm/reservation/get-prospects`

Menampilkan list lead yang memiliki status prospect

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Query Params
| Parameter | Deskripsi |
| search | cari nama lead |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Prospect fetched successfully",
	"data": [
		{
			"id": "0196d9cc-785d-70dc-a8b9-e6319474d0f1",
			"name": "jajang",
			"phone": "4247324628",
			"email": "jajang@yopmail.com",
			"survey_location_id": null,
			"survey_date": "2025-05-10",
			"marketing_agent_id": "ce422123-4951-44b8-80b2-9e22930dc7bb",
			"marketing_agent": "Carter Sauer"
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

## Endpoint: Create Reservation

### **POST** `/crm/reservation`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| lead_id | uuid lead               |
| property_id | uuid property       |
| unit_price | int             |
| reservation_date | YYYY-MM-DD      |
| reservation_fee | int             |
| all_in_fee | int             |
| hook_additional_fee | int             |
| additional_land_area_fee | int             |
| additional_building_specifications_fee | int             |
| construction_notes | string                  |
| notes | string                  |

### Contoh Request Body
```json
{
	"lead_id" : "0196b5c2-ed3b-73ef-8b99-b57a48fe32a4",
	"reservation_date" : "2025-04-20",
	"unit_price" : 200000000,
	"property_id" :  "6d20a605-d856-4102-bd8d-461a35e23990",
	"reservation_fee" : 15000000,
	"all_in_fee" : 600000,
	"notes" : "testing",
	"hook_additional_fee" : 0,
	"additional_land_area_fee" : 0,
	"additional_building_specifications_fee" : 0
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Reservation created successfully",
	"data": {
		"lead_id": "0196b5c2-ed3b-73ef-8b99-b57a48fe32a4",
		"reservation_date": "2025-04-20",
		"status": "pending",
		"property_unit_id": "6d20a605-d856-4102-bd8d-461a35e23990",
		"unit_price": 200000000,
		"dp_amount": 3400000,
		"notes": "testing",
		"id": "0196b5c5-cc6a-7238-af42-6267737af9d7",
		"updated_at": "2025-05-09T15:59:21.000000Z",
		"created_at": "2025-05-09T15:59:21.000000Z"
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
		"lead_id": [
			"required"
		],
	}
}
```

---


## Endpoint: Get All Reservation

### **GET** `/crm/reservation`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| search    | cari dengan query ilike ke kolom name, email, phone dan location |
| page      | Halaman |
| per_page  | Jumlah data per halaman |
| sortKey  | Urutkan berdasarkan kolom (name, duration) |
| order     | Urutkan berdasarkan (asc,desc) |
| status | 'pending','confirmed','canceled','expired' |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Reservations fetched successfully",
	"data": [
		{
			"id": "01972a80-40b1-7054-b7ca-7f96261bd64a",
			"lead_id": "01972a35-39e4-7151-b2a5-c739986370ba",
			"property_id": "6d20a605-d856-4102-bd8d-461a35e23990",
			"name": "Naja",
			"phone": "89763526636",
			"notes": "testing",
			"reservation_status": "pending",
			"property_name": "Dummy Property 1",
			"reservation_date": "2025-05-10",
			"duration": "22 days"
		},
		{
			"id": "01972998-412f-7012-9a14-c305a550f841",
			"lead_id": "0197298f-88ac-7248-bcb1-7e6405d0eef5",
			"property_id": "6d20a605-d856-4102-bd8d-461a35e23990",
			"name": "bocil jajang",
			"phone": "4247324628",
			"notes": "ganteng ora",
			"reservation_status": "confirmed",
			"property_name": "Dummy Property 1",
			"reservation_date": "2025-03-20",
			"duration": "73 days"
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
> duration = berapa hari dari reservation date sampai sekarang

### Contoh Response Gagal (jika token tidak valid)
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Endpoint: Get Reservation By ID

### **GET** `/crm/reservation/{id}`

Bisa di gunakan untuk autofill di form tambah reservation / edit reservation

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
	"message": "Reservation fetched successfully",
	"data": {
		"id": "0196b5c5-cc6a-7238-af42-6267737af9d7",
		"name": "Naja",
		"status": "pending",
		"survey_date": null,
		"marketing_agent": "Carter Sauer",
		"property": "Dummy Property 1",
		"property_id": "6d20a605-d856-4102-bd8d-461a35e23990",
		"unit_price": "3400000.00",
		"reservation_fee": "3400000.00",
		"hook_additional_fee" : "0.00",
        "additional_land_area_fee" : "0.00",
        "additional_building_specifications_fee" : "0.00",
		"all_in_fee": "3400000.00",
		"reservation_proof": null,
		"reservation_letter": null,
		"reservation_date": "2025-04-20",
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

**Jika Reservation Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: Update Reservation

### **POST** `/crm/reservation/{id}`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                 |
|-----------|----------------------------|
| reservation_date | Tanggal Reservasi |
| property_id | ID Properti |
| unit_price | Harga Properti |
| status | Status Reservasi 'pending','confirmed','canceled','expired'|
| reservation_fee | Biaya Reservasi |
| all_in_fee | Biaya All In |
| hook_additional_fee | Biaya Hook Tambahan |
| additional_land_area_fee | Biaya Tambahan Luas Tanah |
| additional_building_specifications_fee | Biaya Tambahan Spesifikasi Bangunan |
| reservation_proof | Bukti Reservasi (file) image|
| reservation_letter | Surat Reservasi (file) pdf|
| construction_notes | Catatan Konstruksi |
| notes | Catatan Reservasi |


### Contoh Request Body
```json
{
	"reservation_date": "2025-05-10",
	"property_id": "6d20a605-d856-4102-bd8d-461a35e23990",
	"unit_price": "3400000",
	"status": "confirmed",
	"reservation_fee": "3400000.00",
	"all_in_fee": "3400000.00",
	"hook_additional_fee" : "0.00",
	"additional_land_area_fee" : "0.00",
	"additional_building_specifications_fee" : "0.00",
	"reservation_proof": "(file)",
	"reservation_letter": "(file)",
	"notes": null
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Reservation updated successfully",
	"data": {
		"id": "0196af28-b565-73d8-9568-b4edb4546657",
		"lead_id": "0196af22-d1f0-7077-9869-3d70ccc7576f",
		"property_unit_id": "6d20a605-d856-4102-bd8d-461a35e23990",
		"unit_price": "3400000",
		"status": "confirmed",
		"reservation_date": "2025-03-20",
		"booking_document_url": "http://localhost:8000/api/file/crm/reservation/surat_pemesanan/ce254144-8739-4dcf-9f62-6f4f3f3e25f4.pdf",
		"dp_proof_url": "http://localhost:8000/api/file/crm/reservation/bukti_pembayaran/310f6b28-a313-4fe3-bbe5-d9ac20efccec.png",
		"dp_amount": "99999",
		"notes": null,
		"created_at": "2025-05-08T09:10:02.000000Z",
		"updated_at": "2025-05-09T09:22:41.000000Z"
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
		"reservation_date": ["required"]
	}
}
```

---


## Endpoint: Delete Lead And Reservation

### **DELETE** `/crm/reservation/{id}`

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
	"message": "Lead deleted successfully",
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

**Jika Lead Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---
