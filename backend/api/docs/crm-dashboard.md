
# Dokumentasi API Dashboard Crm

## Endpoint: Get Marketing Performance

### **GET** `/crm/dashboard/marketing-performance`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
Tidak ada param yang diperlukan.

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success Get Marketing Performance",
	"data": [
		{
			"user_id": "1b135ab9-702c-4e8a-bf8a-39aa7526c99b",
			"user_name": "Damian Kurniawan",
			"total_tasks": 4,
			"on_time": 4,
			"late": 0,
			"ontime_percentage": 100
		}
	]
}
```
> is_duplicate menunjukkan apakah contact tersebut adalah duplikat dari contact lain (berdasarkan phone)

### Contoh Response Gagal (jika token tidak valid)
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Endpoint: Get New Lead

### **GET** `/crm/dashboard/new-lead`

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
	"message": "Success Get All New Lead",
	"data": [
		{
			"lead_id": "0197fe7d-3862-71b4-99bd-04a9c7e2a55d",
			"lead_name": "Naja",
			"source": "instagram",
			"status": "new",
			"phone": "89763526636",
			"due_date": "2025-07-13",
			"agent": null,
			"created_at": "2025-07-12T11:55:08.000000Z"
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

## Endpoint: Get Pending Task

### **GET** `/crm/dashboard/pending-tasks`

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
	"message": "Success Get All Pending Task User",
	"data": [
		{
			"lead_id": "0197fe7d-3862-71b4-99bd-04a9c7e2a55d",
			"lead_name": "Naja",
			"task": "Follow up lead agar statusnya menjadi Prospect",
			"source": "instagram",
			"status": "new",
			"phone": "89763526636",
			"due_date": "2025-07-13",
			"is_late": false,
			"remaining_days": 1,
			"agent": null,
			"created_at": "2025-07-12T11:55:08.000000Z"
		},
		{
			"lead_id": "0197fdc4-d7e7-7288-ad12-eeb021464c72",
			"lead_name": "Galih",
			"task": "Follow up lead agar status paymentnya menjadi Legalitas akhir",
			"source": "agent",
			"status": "akad_kredit",
			"phone": "86352675623",
			"due_date": null,
			"is_late": false,
			"remaining_days": null,
			"agent": null,
			"created_at": "2025-07-12T08:33:45.000000Z"
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

## Endpoint: Get Summary by Status

### **GET** `/crm/dashboard/summary-status`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Tipe Data | Deskripsi |
| --- | --- | --- |
| when | String | today,last_week,last_month,last_year |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success Get Summary Status",
	"data": {
		"document_and_legal_process": 1,
		"new": 0,
		"prospect": 1,
		"reserve": 1,
		"survey": 1
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

## Endpoint: Get Summary by source

### **GET** `/crm/dashboard/summary-source`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Tipe Data | Deskripsi |
| --- | --- | --- |
| when | String | today,last_week,last_month,last_year |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success Get Summary Source",
	"data": {
		"instagram": 1
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

## Endpoint: Get Summary by changed status

### **GET** `/crm/dashboard/summary-changed`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Tipe Data | Deskripsi |
| --- | --- | --- |
| when | String | today,last_week,last_month,last_year |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success Get Summary Changed Status",
	"data": {
		"2025-10-11": 1
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

## Endpoint: Get lead-funnel

### **GET** `/crm/dashboard/lead-funnel`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Tipe Data | Deskripsi |
| --- | --- | --- |
| month | String | 1-12 |
| year | String | tahun |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success Get Lead Funnel",
	"data": {
		"new": {
			"total": 1,
			"percentage": 33.33
		},
		"survey": {
			"total": 1,
			"percentage": 100
		},
		"reservation": {
			"total": 0,
			"percentage": 0
		},
		"payment": {
			"total": 0,
			"percentage": 0
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

---

## Endpoint: Get task-performance

### **GET** `/crm/dashboard/task-performance`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Tipe Data | Deskripsi |
| --- | --- | --- |
| year | String | tahun |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success Get Task Performance",
	"data": {
		"Wina": {
			"late": 0,
			"ontime": 4
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

---