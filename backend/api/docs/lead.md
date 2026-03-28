
# Dokumentasi API Management Lead

## Endpoint: Get Available Leads Status

### **GET** `/crm/lead/get-available-status`

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
		"new",
		"prospect",
		"reserve",
		"document_and_legal_process",
		"complete",
		"cancel"
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

## Endpoint: Get Marketing Agent

### **GET** `/crm/lead/get-marketing-agents`

Menampilkan list user dengan role group marketing agent

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
	"message": "Success",
	"data": [
		{
			"id": "029cd951-5c7a-4eb9-8abc-3c3687f77b52",
			"name": "Nathan Klein"
		},
		{
			"id": "cabf95bd-e0ed-4688-95ac-dde9ed4e2035",
			"name": "Pamela Smith V"
		},
		{
			"id": "1818512b-80c7-4b7e-a7ef-2021d5927b36",
			"name": "Mrs. Heaven Thompson"
		},
		{
			"id": "6264c1e4-59af-49d9-9789-b28d72772201",
			"name": "Cordie Pfeffer"
		},
		{
			"id": "28727e5d-e7ba-4b78-a0c5-92de8809a8b4",
			"name": "Omer Zemlak Jr."
		},
		{
			"id": "85645749-139e-4e83-97cb-69c52b9e5fe5",
			"name": "Dr. Alba Bernier"
		},
		{
			"id": "6c3289f9-17c6-4538-a4e9-4abc4ecf82c8",
			"name": "Sienna Ratke III"
		},
		{
			"id": "d7bf6879-bbff-40aa-8a11-eb24bf7d2df1",
			"name": "Dr. Elijah Wintheiser V"
		},
		{
			"id": "742ff237-6c39-48d3-9270-4750112a2aa7",
			"name": "Rebekah Grimes"
		},
		{
			"id": "d6e7a509-1285-46a0-bf96-a0e25fda67a7",
			"name": "Mr. Alexie Abernathy Sr."
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

## Endpoint: Get Summary Lead by status

### **GET** `/crm/lead/summary`

Menampilkan list user dengan role group marketing agent

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
	"message": "Success",
	"data": [
		{
			"status": "new",
			"total": 0
		},
		{
			"status": "prospect",
			"total": 0
		},
		{
			"status": "reserve",
			"total": 0
		},
		{
			"status": "document_and_legal_process",
			"total": 0
		},
		{
			"status": "complete",
			"total": 0
		},
		{
			"status": "cancel",
			"total": 0
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

## Endpoint: Get All Non Leads Contact (for select)

### **GET** `/crm/lead/get-non-lead-contacts`

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
			"is_original": 1
		},
		{
			"id": "0d6dd499-93be-44d2-837b-2b62015925c1",
			"name": "Naja",
			"phone": "89763526636",
			"is_original": 1
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

### **GET** `/crm/lead/get-property-units`

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

### **GET** `/crm/lead/get-survey-location`

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

## Endpoint: Create New Lead

### **POST** `/crm/lead`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| source | `mandatory` Source (facebook,instagram,tiktok,ots,agent,event,ads_facebook,ads_instagram,ads_tiktok)   |
| contact_id | `mandatory` Contact Id   |
| marketing_id | `mandatory if source is agent` Marketing Id   |
| survey_date |  Survey Date   |
| survey_location_id |  `mandatory` Survey Location id uuid   |
| notes |  Note   |

### Contoh Request Body
```json
{
	"source": "instagram",
	"contact_id": "uuid",
	"marketing_id": null,
	"survey_date": "2025-05-06",
	"survey_location_id": "",
	"notes":"lead baru dari instagram oleh bu naja"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Lead created successfully",
	"data": {
		"contact_id": "0cdd00f1-860c-4ede-9ab3-719abc382b05",
		"assign_to": "029cd951-5c7a-4eb9-8abc-3c3687f77b52",
		"status": "new",
		"survey_location_id": null,
		"survey_date": "2025-05-06T08:00:31.000000Z",
		"due_date": "2025-05-07T08:00:31.882098Z",
		"id": "0196a49c-588b-70bd-b73f-791d1a3d6602",
		"source": "instagram",
		"updated_at": "2025-05-06T08:00:31.000000Z",
		"created_at": "2025-05-06T08:00:31.000000Z"
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
		"contact_id": [
			"The contact id field is required."
		]
	}
}
```

---


## Endpoint: Get All Leads

### **GET** `/crm/lead`

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
			"id": "01972a36-a080-720c-ba1d-0e5df57fb85c",
			"status": "new",
			"order_number": "#3",
			"source": "agent",
			"name": "Galih",
			"phone": "86352675623",
			"notes": null,
			"due_date": "2025-06-02 13:38:32",
			"duration": "0 days",
			"created_at": "2025-06-01"
		},
		{
			"id": "01972a35-39e4-7151-b2a5-c739986370ba",
			"status": "prospect",
			"order_number": "#2",
			"source": "agent",
			"name": "Naja",
			"phone": "89763526636",
			"notes": "kecepatan jet",
			"due_date": "2025-06-04 13:53:39",
			"duration": "0 days",
			"created_at": "2025-06-01"
		},
		{
			"id": "0197298f-88ac-7248-bcb1-7e6405d0eef5",
			"status": "document_and_legal_process",
			"order_number": "#1",
			"source": "instagram",
			"name": "bocil jajang",
			"phone": "4247324628",
			"notes": "kecepatan jet",
			"due_date": "2025-06-06 10:53:18",
			"duration": "0 days",
			"created_at": "2025-06-01"
		}
	],
	"pagination": {
		"total": 3,
		"per_page": 20,
		"current_page": 1,
		"last_page": 1
	}
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

## Endpoint: Get Lead By ID

### **GET** `/crm/lead/{lead_id}`

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
		"id": "01983610-42e4-7042-8f91-49e2b5b48bf9",
		"name": "Galih",
		"phone": "86352675623",
		"email": null,
		"marketing_agent_id": "0f0f3c9c-fdf8-4733-b5b3-f28e8107ac8a",
		"marketing_agent_name": null,
		"status": "prospect",
		"survey_location_id": "01983603-2a25-73f2-85b7-1b7107b4f596",
		"survey_date": "2025-07-10",
		"due_date": "2025-07-26 14:00:43",
		"source": "agent",
		"pic": "gia",
		"actual_survey_date": "2025-07-10",
		"survey_documentation": "http://localhost:8000/api/file/crm/survey_documentation/bb486816-a0d3-4845-8c9d-17d5d12ed626.png",
		"unit_preference_id": "01983603-7c8c-717a-bee8-7295a0f9d2d5",
		"unit_preference_type": "subsidi 30/60",
		"history": [
			{
				"action_by": "c685a612-da8f-451b-9119-e51633bb4051",
				"action_by_name": "Admin",
				"old_status": null,
				"new_status": "new",
				"changed_at": "2025-07-23 13:54:52"
			},
			{
				"action_by": "c685a612-da8f-451b-9119-e51633bb4051",
				"action_by_name": "Admin",
				"old_status": "new",
				"new_status": "prospect",
				"changed_at": "2025-07-23 14:00:43"
			}
		],
		"notes": "kecepatan jet",
		"created_at": "2025-07-23T06:54:52.000000Z"
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

## Endpoint: Update Lead

### **POST** `/crm/lead/{lead_id}`

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
	"source": "agent",
	"marketing_id": "uuid",
	"status":"prospect",
	"survey_date": "2023-11-10",
	"survey_location_id": "",
	"actual_survey_date": "2023-11-10",
	"survey_documentation": "{file}",
	"unit_preference_id": "",
	"notes":""
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Lead updated successfully",
	"data": {
		"id": "0197fe7d-3862-71b4-99bd-04a9c7e2a55d",
		"contact_id": "9e23dfa6-b7e2-41e6-88a4-a116617b102d",
		"assign_to": "0f0f3c9c-fdf8-4733-b5b3-f28e8107ac8a",
		"status": "prospect",
		"survey_location_id": null,
		"survey_date": "2025-07-10",
		"due_date": "2025-07-26 00:59:06",
		"source": "agent",
		"actual_survey_date": "2025-07-10",
		"survey_documentation": "http://localhost:8000/api/file/crm/survey_documentation/f7a277d2-1194-4b66-8ec7-6a95f801e603.png",
		"unit_preference_id": "0197fdc6-294f-7113-bad1-c9cf5fea84f3",
		"updated_at": "2025-07-22T17:59:06.000000Z",
		"created_at": "2025-07-12T11:55:08.000000Z"
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
		"name": "The name field is required.",
	}
}
```

---

## Endpoint: Delete Lead

### **DELETE** `/crm/lead/{lead_id}`

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
