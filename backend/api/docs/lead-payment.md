
# Dokumentasi API Management Lead Payment

## Endpoint: Get Summary Lead Document By Status

### **GET** `/crm/lead-payment/summary`

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
	"message": "Leads Payments summary fetched successfully",
	"data": [
		{
			"status": "proses_bank",
			"total": 0
		},
		{
			"status": "sp3k",
			"total": 0
		},
		{
			"status": "akad_kredit",
			"total": 1
		},
		{
			"status": "cash_keras",
			"total": 0
		},
		{
			"status": "cash_bertahap",
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

## Endpoint: Get Bank List

### **GET** `crm/lead-payment/bank-list`

Menampilkan list bank untuk select atau checkbox

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
	"message": "Success Show Bank List",
	"data": [
		{
			"code": "btn",
			"name": "Bank BTN"
		},
		{
			"code": "bjb",
			"name": "Bank BJB"
		},
		{
			"code": "bni",
			"name": "Bank BNI"
		},
		{
			"code": "bri",
			"name": "Bank BRI"
		},
		{
			"code": "bca",
			"name": "Bank BCA"
		},
		{
			"code": "mandiri",
			"name": "Bank Mandiri"
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

## Endpoint: Get Completed Document Lead

### **GET** `/crm/lead-payment/get-completed-document-lead`

Menampilkan list lead yang documentnya sudah selesai

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
	"message": "Leads Payments document fetched successfully",
	"data": [
		{
			"id": "0197360d-4b6b-7376-a40c-485ba5205687",
			"collection_document_id": "5cd22713-ae5f-4e6a-8c74-6f68340472ef",
			"name": "bocil jajang",
			"phone": "4247324628",
			"email": "jajang@yopmail.com"
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

## Endpoint: Create Payment Lead

### **POST** `/crm/lead-payment`

### Header
```
Content-Type: application/json
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| lead_id | uuid lead `required`     |
| payment_type | payment type `required` (cash_keras, cash_bertahap, kpr)|
| selected_banks | selected bank `required if payment type is kpr`|
| notes | notes `required`|

### Contoh Request Body
```json
{
	"lead_id": "uuid",
  	"payment_type": "cash",
	"selected_banks": ["bca", "bni"],
  	"notes": "Ajukan KPR melalui dua bank"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Success Create Lead Payment",
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
			"required"
		],
	}
}
```

---


## Endpoint: Get All Lead Payment

### **GET** `/crm/lead-payment`

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
| sortKey  | Urutkan berdasarkan kolom (status,duration) |
| sortDir   | Urutkan berdasarkan (asc,desc) |
| status | ('proses_bank', 'sp3k', 'akad_kredit', 'cash') |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success Get All Lead Payment",
	"data": [
		{
			"id": "01973501-ec19-7193-bb9c-28d68a3c5b05",
			"status": "Akad kredit",
			"name": "Galih",
			"phone": "86352675623",
			"notes": null,
			"duration": "0 days",
			"created_at": "2025-06-03"
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

## Endpoint: Get Lead Payment By ID

### **GET** `/crm/lead-payment/{id}`

Bisa di gunakan untuk autofill di form edit payment

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
	"message": "Success Get Lead Payment By Id",
	"data": {
		"id": "01973501-ec19-7193-bb9c-28d68a3c5b05",
		"name": "Galih",
		"phone": "86352675623",
		"email": null,
		"sp3k_status": "approved",
		"sp3k_document": "http://localhost:8000/api/file/crm/lead_payments/sp3k/2fa07783-8037-4661-8bdb-4db8a813c435.pdf",
		"sp3k_bank": "bca",
		"sp3k_code": null,
		"sp3k_date": null,
		"sp3k_number": null,
		"akad_kredit_status": "approved",
		"akad_kredit_penandatanganan_document": "http://localhost:8000/api/file/crm/lead_payments/tanda_tangan_akad_kredit/7bf98171-6b31-4dc5-814c-d8af4cb46c71.pdf",
		"duration": "0 days",
		"status": "akad_kredit",
		"payment_type": "kpr",
		"notes": null,
		"checklists": [
			{
				"key": "checklist_surat_permohonan_akad",
				"name": "Surat permohonan akad",
				"checked": true
			},
			{
				"key": "checklist_permohonan_surat_pip_listrik",
				"name": "Permohonan surat pip listrik",
				"checked": true
			},
			{
				"key": "checklist_permohonan_surat_pip_jalan",
				"name": "Permohonan surat pip jalan",
				"checked": true
			},
			{
				"key": "checklist_permohonan_surat_pip_air",
				"name": "Permohonan surat pip air",
				"checked": true
			},
			{
				"key": "checklist_surat_permohonan_appraisal",
				"name": "Surat permohonan appraisal",
				"checked": true
			},
			{
				"key": "checklist_permohonan_uji_flpp",
				"name": "Permohonan uji flpp",
				"checked": true
			},
			{
				"key": "checklist_upload_foto_rumah",
				"name": "Upload foto rumah",
				"checked": true
			},
			{
				"key": "checklist_permohonan_akad_ke_notaris",
				"name": "Permohonan akad ke notaris",
				"checked": true
			},
			{
				"key": "checklist_upload_data_debitur_ke_notaris",
				"name": "Upload data debitur ke notaris",
				"checked": true
			},
			{
				"key": "checklist_si_pencairan",
				"name": "Si pencairan",
				"checked": true
			},
			{
				"key": "checklist_si_notaris",
				"name": "Si notaris",
				"checked": true
			},
			{
				"key": "checklist_si_kyg",
				"name": "Si kyg",
				"checked": true
			},
			{
				"key": "checklist_spk",
				"name": "Spk",
				"checked": true
			},
			{
				"key": "checklist_approval_flpp",
				"name": "Approval flpp",
				"checked": true
			},
			{
				"key": "checklist_approval_foto_rumah",
				"name": "Approval foto rumah",
				"checked": true
			},
			{
				"key": "checklist_cover_note",
				"name": "Cover note",
				"checked": true
			},
			{
				"key": "checklist_akta_jual_beli",
				"name": "Akta jual beli",
				"checked": true
			},
			{
				"key": "checklist_balik_nama_sertifikat",
				"name": "Balik nama sertifikat",
				"checked": true
			}
		]
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

## Endpoint: Update Lead Payment

### **POST** `/crm/lead-payment/{id}`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| sp3k_status | Status SP3K (pending, approved) `required` |
| sp3k_document | Dokumen SP3K (pdf) |
| sp3k_bank | Bank SP3K (btn, bjb, bni, bri, bca, mandiri) |
| sp3k_code | Kode SP3K |
| sp3k_date | Tanggal SP3K |
| sp3k_number | Nomor SP3K |
| akad_kredit_status | Status Akad Kredit (pending, approved) `required` |
| akad_kredit_penandatanganan_document | Dokumen Penandatanganan Akad Kredit (pdf) |
| notes | Catatan |
| checklist_surat_permohonan_akad | Checklist Surat Permohonan Akad `required` |
| checklist_permohonan_surat_pip_listrik | Checklist Permohonan Surat PIP Listrik `required` |
| checklist_permohonan_surat_pip_jalan | Checklist Permohonan Surat PIP Jalan `required` |
| checklist_permohonan_surat_pip_air | Checklist Permohonan Surat PIP Air `required` |
| checklist_surat_permohonan_appraisal | Checklist Surat Permohonan Appraisal `required` |
| checklist_permohonan_uji_flpp | Checklist Permohonan Uji FLPP `required` |
| checklist_upload_foto_rumah | Checklist Upload Foto Rumah `required` |
| checklist_permohonan_akad_ke_notaris | Checklist Permohonan Akad ke Notaris `required` |
| checklist_upload_data_debitur_ke_notaris | Checklist Upload Data Debitur ke Notaris `required` |
| checklist_si_pencairan | Checklist SI Pencairan `required` |
| checklist_si_notaris | Checklist SI Notaris `required` |
| checklist_si_kyg | Checklist SI KYG `required` |
| checklist_spk | Checklist SPK `required` |
| checklist_approval_flpp | Checklist Approval FLPP `required` |
| checklist_approval_foto_rumah | Checklist Approval Foto Rumah `required` |
| checklist_cover_note | Checklist Cover Note `required` |
| checklist_akta_jual_beli | Checklist Akta Jual Beli `required` |
| checklist_balik_nama_sertifikat | Checklist Balik Nama Sertifikat `required` |
| proposed_name_1 | Nama Debitur yang di ajukan 1 |
| proposed_name_2 | Nama Debitur yang di ajukan 2 |


### Contoh Request Body
```json
// multipart/form-data
{
  "sp3k_status": "approved",
  "sp3k_document": "file",
  "sp3k_bank": "bca",
  "sp3k_code": "123",
  "sp3k_date": "2022-01-01",
  "sp3k_number": "123",
  "akad_kredit_status": "approved",
  "akad_kredit_penandatanganan_document": "file",
  "notes": "testing",
  "checklist_surat_permohonan_akad": true,
  "checklist_permohonan_surat_pip_listrik": true,
  "checklist_permohonan_surat_pip_jalan": true,
  "checklist_permohonan_surat_pip_air": true,
  "checklist_surat_permohonan_appraisal": true,
  "checklist_permohonan_uji_flpp": true,
  "checklist_upload_foto_rumah": true,
  "checklist_permohonan_akad_ke_notaris": true,
  "checklist_upload_data_debitur_ke_notaris": true,
  "checklist_si_pencairan": true,
  "checklist_si_notaris": true,
  "checklist_si_kyg": true,
  "checklist_spk": true,
  "checklist_approval_flpp": true,
  "checklist_approval_foto_rumah": true,
  "checklist_cover_note": true,
  "checklist_akta_jual_beli": true,
  "checklist_balik_nama_sertifikat": true,
  "proposed_name_1": "test",
  "proposed_name_2": "test"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success Update Lead Payment",
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
		"sp3k_status": [
			"required"
		],
	}
}
```

---

## Endpoint: Delete Lead Payment

### **DELETE** `/crm/lead-payment/{id}`

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