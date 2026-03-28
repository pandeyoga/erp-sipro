
# Dokumentasi API Management Survey

## Endpoint: Get Summary Lead by status

### **GET** `/crm/survey/summary`

Menampilkan list user dengan role group marketing agent

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Query Params
tidak ada

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": [
		{
			"status": "not_scheduled",
			"total": 0
		},
		{
			"status": "scheduled",
			"total": 0
		},
		{
			"status": "done",
			"total": 1
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

## Endpoint: Get All Non Survey Lead (for select)

### **GET** `/crm/survey/get-non-survey-lead`

Menampilkan list contact yang bukan lead

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| search    | cari dengan query ilike ke kolom name, email |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": [
		{
			"id": "8c795663-c8a4-461a-99b0-a2d0d933f618",
			"name": "Galih",
			"phone": "86352675623",
		},
		{
			"id": "0d6dd499-93be-44d2-837b-2b62015925c1",
			"name": "Naja",
			"phone": "89763526636",
		}
	]
}
```
> assign_to adalah id user marketing agent yang di assign ke lead tersebut

### Contoh Response Gagal (jika token tidak valid)
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Endpoint: Get All Property Units (for select)

### **GET** `/crm/survey/get-property-units`

Menampilkan list unit untuk select unit preference

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
	"message": "Success",
	"data": [
		{
			"id": "0197fdc6-294f-7113-bad1-c9cf5fea84f3",
			"type": "subsidi 30/60",
			"land_area": "60",
			"building_area": "30"
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

## Endpoint: Get All Survey Location (for select)

### **GET** `/crm/survey/get-survey-location`

Menampilkan list projek untuk select survey location

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
	"message": "Success",
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

## Endpoint: Create New Survey

### **POST** `/crm/survey`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| lead_id | id lead yang akan di survey |
| survey_date | tanggal survey |
| survey_location_id | id survey location |
| actual_survey_date | tanggal survey sebenarnya |
| survey_documentation | dokumentasi survey |
| unit_preference_id | id unit preference |

### Contoh Request Body
```json
{
	"lead_id": "{}",
	"survey_date": "2025-07-10",
	"survey_location_id": "{}",
	"actual_survey_date" : "2025-07-10",
	"survey_documentation" : null,
	"unit_preference_id" : "{}",
	"pic":"Mas Jajang",
	"notes":"aaa"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Survey created successfully",
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
		"lead_id": [
			"The lead id field is required."
		]
	}
}
```

---


## Endpoint: Get All Surveys lead

### **GET** `/crm/survey`

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
| marketing_id | cari dengan query where marketing_id |
| source | cari dengan query where source (facebook,instagram,tiktok,ots,agent) |
| status | cari dengan query where status (new, prospect, reserve, document_and_legal_process, complete, cancel) |
| sortKey | urutkan berdasarkan (name, duration, order_number) |
| order | urutan dari sort (asc, desc) |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Leads fetched successfully",
	"data": [
		{
			"id": "0199d3f9-e8f8-7198-b4cf-b0c1b9ba7cc6",
			"status": "Done",
			"name": "dwada",
			"phone": "0873762346232",
			"scheduled_date": "2025-07-10",
			"actual_survey_date": "2025-07-10",
			"duration": "94 days",
			"notes": "anjay"
		}
	],
	"pagination": {
		"total": 1,
		"per_page": 20,
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

## Endpoint: Get Survey By ID

### **GET** `/crm/survey/{survey_id}`

Bisa di gunakan untuk autofill data form leads update

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
	"message": "Lead fetched successfully",
	"data": {
		"id": "0199d3f9-e8f8-7198-b4cf-b0c1b9ba7cc6",
		"contact_id": "0199cf1a-e77a-7046-88e6-cf54bd25d488",
		"unit_preference_id": "0199945d-2d32-730f-a61a-3465bf91f197",
		"survey_location_id": "0199945b-3d51-7187-ad44-91a600627172",
		"pic": "Mas Jajang",
		"notes": "anjay",
		"status": "done",
		"survey_date": "2025-07-10",
		"actual_survey_date": "2025-07-10",
		"name": "dwada",
		"phone": "0873762346232",
		"email": "jajang@gmail.com",
		"survey_documentation": "https:example.com/survey.png",
	}
}
```
> survey documentation bisa di pake jadi profile picture lead

### Contoh Response Gagal
**Jika Token Tidak Valid**
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**Jika Lead Id Tidak Valid**
> code : 400
```json
{
	"success": false,
	"message": "Invalid lead id",
	"errors": null
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

## Endpoint: Update Survey

### **POST** `/crm/survey/{survey_id}`

### Header
```
Content-Type: Multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                 |
|-----------|----------------------------|
| source | Source (facebook,instagram,tiktok,ots,agent,event,ads_facebook,ads_instagram,ads_tiktok) |
| marketing_id | Marketing agent yang di assign ke lead |
| status | Status lead (hanya new, dan prospect) |
| survey_date | Tanggal survey |
| survey_location_id | Lokasi survey |
| actual_survey_date | Tanggal survey sebenarnya |
| survey_documentation | Dokumentasi survey (pdf, jpg, png) |
| unit_preference_id | Unit preferensi |
| notes | Catatan tambahan |


### Contoh Request Body
```multipart
{
	"lead_id": "0199d3f9-e8f8-7198-b4cf-b0c1b9ba7cc6",
	"survey_date": "2025-07-10",
	"survey_location_id": "0199945b-3d51-7187-ad44-91a600627172",
	"actual_survey_date" : "2025-07-10",
	"survey_documentation" : null,
	"unit_preference_id" : "0199945d-2d32-730f-a61a-3465bf91f197",
	"pic":"Mas Ilham",
	"notes":"anjay"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Survey updated successfully",
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