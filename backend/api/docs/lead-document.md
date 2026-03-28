
# Dokumentasi API Management Lead Document (for Customer)

## Endpoint: Get Summary Lead Document By Status

### **GET** `/crm/lead-document/summary`

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
			"status": "input",
			"total": 0
		},
		{
			"status": "verification",
			"total": 1
		},
		{
			"status": "completed",
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

## Endpoint: Get Customer Document Types

### **GET** `/crm/lead-document/buyer-document-types`

Menampilkan list tipe dokumen customer

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
			"code": "ktp_applicant",
			"label": "KTP Pemohon"
		},
		{
			"code": "ktp_partner",
			"label": "KTP Pasangan"
		},
		{
			"code": "npwp",
			"label": "NPWP"
		},
		{
			"code": "kk",
			"label": "Kartu Keluarga"
		},
		{
			"code": "marriage_certificate",
			"label": "Surat Nikah"
		},
		{
			"code": "divorce_certificate",
			"label": "Surat Cerai"
		},
		{
			"code": "emergency_contact_ktp",
			"label": "KTP Kontak Darurat"
		},
		{
			"code": "applicant_photo",
			"label": "Foto Pemohon"
		},
		{
			"code": "partner_photo",
			"label": "Foto Pasangan"
		},
		{
			"code": "unmarried_certificate",
			"label": "Surat Keterangan Belum Menikah"
		},
		{
			"code": "house_ownership_certificate",
			"label": "Surat Kepemilikan Rumah"
		},
		{
			"code": "domisili_certificate",
			"label": "Surat Keterangan Domisili"
		},
		{
			"code": "salary_slip_3_months",
			"label": "Slip Gaji 3 Bulan Terakhir"
		},
		{
			"code": "bank_statement_3_months",
			"label": "Rekening Koran 3 Bulan Terakhir"
		},
		{
			"code": "income_statement_6_months",
			"label": "Laporan Pendapatan 6 Bulan Terakhir"
		},
		{
			"code": "bank_statement_6_months",
			"label": "Rekening Koran 6 Bulan Terakhir"
		},
		{
			"code": "business_photo",
			"label": "Foto Usaha"
		},
		{
			"code": "business_certificate",
			"label": "Surat Izin Usaha"
		},
		{
			"code": "bank_form",
			"label": "Formulir Bank"
		},
		{
			"code": "flpp",
			"label": "FLPP"
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

## Endpoint: Get Reserved Lead (For Creating Document)

### **GET** `/crm/lead-document/get-reserved-lead`

Menampilkan list lead yang memiliki status reserve

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
	"message": "Leads fetched successfully",
	"data": [
		{
			"id": "0197360d-4b6b-7376-a40c-485ba5205687",
			"reservation_id": "0197360e-b8a4-7037-b69a-9befe0fd337f",
			"name": "bocil jajang",
			"phone": "4247324628",
			"email": "jajang@yopmail.com",
			"survey_location_id": null,
			"survey_date": "2025-05-23",
			"marketing_agent_id": "0f0f3c9c-fdf8-4733-b5b3-f28e8107ac8a",
			"marketing_agent": "Prof. Jessy Pfeffer"
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

## Endpoint: Create Document

### **POST** `/crm/lead-document`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| lead_id | uuid lead `required`     |
| doc_ktp_applicant | KTP Pemohon (jpg,jpeg,png,pdf) |
| doc_ktp_partner | KTP Pasangan (jpg,jpeg,png,pdf) |
| doc_npwp | NPWP (jpg,jpeg,png,pdf) |
| doc_kk | Kartu Keluarga (jpg,jpeg,png,pdf) |
| doc_marriage_or_divorce_certificate | Surat Nikah (jpg,jpeg,png,pdf) |
| doc_applicant_photo | Foto Pemohon (jpg,jpeg,png) |
| doc_partner_photo | Foto Pasangan (jpg,jpeg,png) |
| doc_house_ownership_certificate | Surat Belum Memiliki Rumah (jpg,jpeg,png,pdf) |
| doc_domisili_certificate | Surat Domisili (jpg,jpeg,png,pdf) |
| doc_spr_bank | Surat Perjanjian Bank (jpg,jpeg,png,pdf) |
| check_cash | Untuk Bypas jika cash `required` |
| pekerja_materai_60_lembar | Checklist Materai 60 Lembar `required` |
| pekerja_rekening_koran_3_bulan | Checklist Rekening Koran 3 Bulan `required` |
| pekerja_no_telp_dan_nama_atasan | Checklist No Telp dan Nama Atasan `required` |
| pekerja_foto_tempat_kerja_dan_serlok | Checklist Foto Tempat Kerja dan Serlok `required` |
| pekerja_slip_gaji_3_bulan | Checklist Slip Gaji 3 Bulan `required` |
| pekerja_formulir_bank_dan_flpp | Checklist Formulir Bank dan FLPP `required` |
| wirausaha_materai_60_lembar | Checklist Materai 60 Lembar `required` |
| wirausaha_rekening_koran_6_bulan | Checklist Rekening Koran 6 Bulan `required` |
| wirausaha_sk_usaha_atau_nomor_usaha | Checklist SK Usaha atau Nomor Usaha `required` |
| wirausaha_foto_tempat_usaha | Checklist Foto Tempat Usaha `required` |
| wirausaha_foto_tempat_usaha_dan_serlok | Checklist Foto Tempat Usaha dan Serlok `required` |
| wirausaha_neraca_penghasilan_6_bulan | Checklist Neraca Penghasilan 6 Bulan `required` |
| wirausaha_formulir_bank_dan_flpp | Checklist Formulir Bank dan FLPP `required` |
| notes | Catatan Tambahan |

### Contoh Request Body
```json
// multipart form
{
	"lead_id": "0196b5c2-ed3b-73ef-8b99-b57a48fe32a4",
	"doc_ktp_applicant": "file",
	"doc_ktp_partner": "file",
	"doc_npwp": "file",
	"doc_kk": "file",
	"doc_marriage_or_divorce_certificate": "file",
	"doc_applicant_photo": "file",
	"doc_partner_photo": "file",
	"doc_house_ownership_certificate": "file",
	"doc_domisili_certificate": "file",
	"doc_spr_bank": "file",
	"pekerja_materai_60_lembar": true,
	"pekerja_rekening_koran_3_bulan": true,
	"pekerja_no_telp_dan_nama_atasan": true,
	"pekerja_foto_tempat_kerja_dan_serlok": true,
	"pekerja_slip_gaji_3_bulan": true,
	"pekerja_formulir_bank_dan_flpp": true,
	"wirausaha_materai_60_lembar": true,
	"wirausaha_rekening_koran_6_bulan": true,
	"wirausaha_sk_usaha_atau_nomor_usaha": true,
	"wirausaha_foto_tempat_usaha": true,
	"wirausaha_foto_tempat_usaha_dan_serlok": true,
	"wirausaha_neraca_penghasilan_6_bulan": true,
	"wirausaha_formulir_bank_dan_flpp": true,
	"notes": "testing"
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Lead Documents uploaded successfully",
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


## Endpoint: Get All Lead Document

### **GET** `/crm/lead-document`

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
| status | 'input', 'verification', 'completed' |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Leads Documents fetched successfully",
	"data": [
		{
			"id": "c78dc9d8-ef23-4f64-81c6-74ae262ac5e3",
			"lead_id": "01972b7e-3886-7170-a936-c0691ec68435",
			"name": "Galih",
			"phone": "86352675623",
			"notes": "ahjdwgjha",
			"status": "completed",
			"property_unit_id": "6d20a605-d856-4102-bd8d-461a35e23990",
			"property_unit": "",
			"due_date": "2025-06-06 20:48:55",
			"duration": "2 days",
			"created_at": "2025-06-01"
		}
	],
	"pagination": {
		"total": 1,
		"per_page": 3,
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

## Endpoint: Get Lead Document By ID

### **GET** `/crm/lead-document/{id}`

Bisa di gunakan untuk autofill di form edit document

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
	"message": "Lead Document fetched successfully",
	"data": {
		"id": "5cd22713-ae5f-4e6a-8c74-6f68340472ef",
		"lead_id": "0197360d-4b6b-7376-a40c-485ba5205687",
		"name": "bocil jajang",
		"phone": "4247324628",
		"email": "jajang@yopmail.com",
		"status": "completed",
		"notes": "ahjdwgjha",
		"documents": [
			{
				"type": "ktp_applicant",
				"is_uploaded": true,
				"is_validated": true,
				"file_url": "http://localhost:8000/api/file/crm/lead_documents/ktp_applicant/604f1a8e-d108-496c-b3a2-a1a9de9302a4.pdf"
			},
			{
				"type": "ktp_partner",
				"is_uploaded": true,
				"is_validated": true,
				"file_url": "http://localhost:8000/api/file/crm/lead_documents/ktp_partner/32fa66a7-a9c1-443b-ab0e-0073fda7816e.pdf"
			},
			{
				"type": "npwp",
				"is_uploaded": true,
				"is_validated": true,
				"file_url": "http://localhost:8000/api/file/crm/lead_documents/npwp/8048aff6-9c1e-4c7d-ab92-a2e3f092fd4c.pdf"
			},
			{
				"type": "kk",
				"is_uploaded": true,
				"is_validated": true,
				"file_url": "http://localhost:8000/api/file/crm/lead_documents/kk/7e190621-d5e5-4487-b856-2892f93591ae.pdf"
			},
			{
				"type": "marriage_or_divorce_certificate",
				"is_uploaded": true,
				"is_validated": true,
				"file_url": "http://localhost:8000/api/file/crm/lead_documents/marriage_or_divorce_certificate/c386f80e-0f38-4eb8-8631-b261222e8225.pdf"
			},
			{
				"type": "applicant_photo",
				"is_uploaded": true,
				"is_validated": true,
				"file_url": "http://localhost:8000/api/file/crm/lead_documents/applicant_photo/4cd0837c-33eb-4742-a312-db97b74ad687.png"
			},
			{
				"type": "partner_photo",
				"is_uploaded": true,
				"is_validated": true,
				"file_url": "http://localhost:8000/api/file/crm/lead_documents/partner_photo/72361e31-f79c-49ed-8e6e-b477397e6250.png"
			},
			{
				"type": "house_ownership_certificate",
				"is_uploaded": true,
				"is_validated": true,
				"file_url": "http://localhost:8000/api/file/crm/lead_documents/house_ownership_certificate/4c9f3ba7-761b-4b5b-94f0-91aefa7c5676.pdf"
			},
			{
				"type": "domisili_certificate",
				"is_uploaded": true,
				"is_validated": true,
				"file_url": "http://localhost:8000/api/file/crm/lead_documents/domisili_certificate/d380bcfc-dac9-41cc-b30b-b3e46bd07619.pdf"
			},
			{
				"type": "spr_bank",
				"is_uploaded": true,
				"is_validated": true,
				"file_url": "http://localhost:8000/api/file/crm/lead_documents/spr_bank/df8515e3-8de4-4d67-b75f-0a83e2e0d45f.pdf"
			}
		],
		"checklist": [
			{
				"key": "pekerja_materai_60_lembar",
				"name": "Pekerja materai 60 lembar",
				"checked": true
			},
			{
				"key": "pekerja_rekening_koran_3_bulan",
				"name": "Pekerja rekening koran 3 bulan",
				"checked": true
			},
			{
				"key": "pekerja_no_telp_dan_nama_atasan",
				"name": "Pekerja no telp dan nama atasan",
				"checked": true
			},
			{
				"key": "pekerja_foto_tempat_kerja_dan_serlok",
				"name": "Pekerja foto tempat kerja dan serlok",
				"checked": true
			},
			{
				"key": "pekerja_slip_gaji_3_bulan",
				"name": "Pekerja slip gaji 3 bulan",
				"checked": true
			},
			{
				"key": "pekerja_formulir_bank_dan_flpp",
				"name": "Pekerja formulir bank dan flpp",
				"checked": true
			},
			{
				"key": "wirausaha_materai_60_lembar",
				"name": "Wirausaha materai 60 lembar",
				"checked": false
			},
			{
				"key": "wirausaha_rekening_koran_6_bulan",
				"name": "Wirausaha rekening koran 6 bulan",
				"checked": false
			},
			{
				"key": "wirausaha_sk_usaha_atau_nomor_usaha",
				"name": "Wirausaha sk usaha atau nomor usaha",
				"checked": false
			},
			{
				"key": "wirausaha_foto_tempat_usaha",
				"name": "Wirausaha foto tempat usaha",
				"checked": false
			},
			{
				"key": "wirausaha_foto_tempat_usaha_dan_serlok",
				"name": "Wirausaha foto tempat usaha dan serlok",
				"checked": false
			},
			{
				"key": "wirausaha_neraca_penghasilan_6_bulan",
				"name": "Wirausaha neraca penghasilan 6 bulan",
				"checked": false
			},
			{
				"key": "wirausaha_formulir_bank_dan_flpp",
				"name": "Wirausaha formulir bank dan flpp",
				"checked": false
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

## Endpoint: Update Lead Document

### **POST** `/crm/lead-document/{id}`

### Header
```
Content-Type: multipart/form-data
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| doc_ktp_applicant | KTP Pemohon (jpg,jpeg,png,pdf) |
| doc_ktp_partner | KTP Pasangan (jpg,jpeg,png,pdf) |
| doc_npwp | NPWP (jpg,jpeg,png,pdf) |
| doc_kk | Kartu Keluarga (jpg,jpeg,png,pdf) |
| doc_marriage_or_divorce_certificate | Surat Nikah (jpg,jpeg,png,pdf) |
| doc_applicant_photo | Foto Pemohon (jpg,jpeg,png) |
| doc_partner_photo | Foto Pasangan (jpg,jpeg,png) |
| doc_house_ownership_certificate | Surat Belum Memiliki Rumah (jpg,jpeg,png,pdf) |
| doc_domisili_certificate | Surat Domisili (jpg,jpeg,png,pdf) |
| doc_spr_bank | Surat Perjanjian Bank (jpg,jpeg,png,pdf) |
| pekerja_materai_60_lembar | Checklist Materai 60 Lembar `required` |
| pekerja_rekening_koran_3_bulan | Checklist Rekening Koran 3 Bulan `required` |
| pekerja_no_telp_dan_nama_atasan | Checklist No Telp dan Nama Atasan `required` |
| pekerja_foto_tempat_kerja_dan_serlok | Checklist Foto Tempat Kerja dan Serlok `required` |
| pekerja_slip_gaji_3_bulan | Checklist Slip Gaji 3 Bulan `required` |
| pekerja_formulir_bank_dan_flpp | Checklist Formulir Bank dan FLPP `required` |
| wirausaha_materai_60_lembar | Checklist Materai 60 Lembar `required` |
| wirausaha_rekening_koran_6_bulan | Checklist Rekening Koran 6 Bulan `required` |
| wirausaha_sk_usaha_atau_nomor_usaha | Checklist SK Usaha atau Nomor Usaha `required` |
| wirausaha_foto_tempat_usaha | Checklist Foto Tempat Usaha `required` |
| wirausaha_foto_tempat_usaha_dan_serlok | Checklist Foto Tempat Usaha dan Serlok `required` |
| wirausaha_neraca_penghasilan_6_bulan | Checklist Neraca Penghasilan 6 Bulan `required` |
| wirausaha_formulir_bank_dan_flpp | Checklist Formulir Bank dan FLPP `required` |
| notes | Catatan Tambahan |


### Contoh Request Body
```json
{
	"doc_ktp_applicant": "file",
	"doc_ktp_partner": "file",
	"doc_npwp": "file",
	"doc_kk": "file",
	"doc_marriage_or_divorce_certificate": "file",
	"doc_applicant_photo": "file",
	"doc_partner_photo": "file",
	"doc_house_ownership_certificate": "file",
	"doc_domisili_certificate": "file",
	"doc_spr_bank": "file",
	"pekerja_materai_60_lembar": true,
	"pekerja_rekening_koran_3_bulan": true,
	"pekerja_no_telp_dan_nama_atasan": true,
	"pekerja_foto_tempat_kerja_dan_serlok": true,
	"pekerja_slip_gaji_3_bulan": true,
	"pekerja_formulir_bank_dan_flpp": true,
	"wirausaha_materai_60_lembar": true,
	"wirausaha_rekening_koran_6_bulan": true,
	"wirausaha_sk_usaha_atau_nomor_usaha": true,
	"wirausaha_foto_tempat_usaha": true,
	"wirausaha_foto_tempat_usaha_dan_serlok": true,
	"wirausaha_neraca_penghasilan_6_bulan": true,
	"wirausaha_formulir_bank_dan_flpp": true,
	"notes": "testing"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Lead Document updated successfully",
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
		"pekerja_materai_60_lembar": ["required"]
	}
}
```

---

## Endpoint: Update status per document

### **POST** `/crm/lead-document/{id}/status`

digunakan untuk ceklis validasi per type dokumen di dokumen customer

### Header
```
Content-Type: application/json
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter| Keterangan               |
|----------|--------------------------|
| type     | type dokumen (lihat type di api Get Customer Document Types)|
| status   | status dokumen (verified, unverified)|


### Contoh Request Body
```json
{
	"type":"ktp_applicant",
	"status":"verified"
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success update status document",
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
		"type": ["required"]
	}
}
```

---

## Endpoint: Delete Lead And Document

### **DELETE** `/crm/lead-document/{id}`

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