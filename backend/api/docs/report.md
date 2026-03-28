
# Dokumentasi API Report

## Endpoint: Get Laba Rugi

### **GET** `/finance/report/laba-rugi`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| year | Tahun |
| month | Bulan |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": {
		"period": "September 2025",
		"total_pendapatan": 0,
		"total_biaya_pendapatan": 0,
		"total_biaya_operasional": 0,
		"total_pendapatan_lainnya": 0,
		"total_biaya_lainnya": 0,
		"total_tarikan": 0,
		"laba_kotor": 0,
		"laba_rugi": 0,
		"detail": {
			"4-000 PENDAPATAN": {
				"4-100 Pendapatan Akad KPR Bank": {
					"4-110 Pendapatan Pencairan Akad": 0,
					"4-111 Retensi Sertifikat Balik Nama": 0,
					"4-112 Retensi PBG": 0,
					"4-113 Retensi Air": 0,
					"4-114 Retensi Listrik": 0,
					"4-115 Retensi Bestek": 0,
					"4-116 Retensi Bangunan": 0,
					"4-118 Pendapatan SBUM": 0
				},
				"4-200 Penjualan Cash": {
					"4-210 Pendapatan Cash Subsidi": 0
				},
				"4-300 Pendapatan dari Konsumen": {
					"4-310 Pendapatan DP": 0,
					"4-320 Pendapatan Booking": 0
				}
			},
			"5-000 BIAYA ATAS PENDAPATAN": {
				"5-100 HARGA POKOK PRODUKSI": {
					"5-101 Harga Pokok Produksi": 0
				},
				"5-200 PEMBEBASAN LAHAN": {
					"5-201 Pembebasan lahan": 0,
					"5-202 Fee Mediator": 0
				},
				"5-210 PERENCANAAN TEKNIS": {
					"5-211 Konsultan": 0,
					"5-212 RAB, Gambar Kerja & Site Plan Konsultan": 0
				},
				"5-220 BIAYA SERTIFIKAT": {
					"5-221 Pengukuran dan Peta Bidang": 0,
					"5-222 PPH": 0,
					"5-223 SPH/BPHTB": 0,
					"5-224 Penerbitan Warkah Desa": 0,
					"5-225 Retribusi Pajak BNPB (BPN)": 0,
					"5-226 SPPT, PBB": 0,
					"5-227 Pertek": 0,
					"5-228 Penerbitan Sertifikat Induk": 0,
					"5-229 Splitcing Sertifikat": 0
				},
				"5-230 BIAYA PERIJINAN": {
					"5-231 Ijin Warga RT,RW & Desa": 0,
					"5-232 Rekom-rekom (Perijinan & Legalitas)": 0,
					"5-233 Penerbitan PBG Induk (SKRD)": 0,
					"5-234 Splitcing PBG": 0,
					"5-235 Asosiasi (APERSI)": 0,
					"5-236 Entertain Perijinan": 0
				},
				"5-255 KOMPENSASI WARGA": {
					"5-256 Kompensasi Warga": 0,
					"5-257 Lain-lain Kompensasi": 0
				},
				"5-260 PEMATANGAN LAHAN": {
					"5-261 Cut&Fill (Operasional Alat Berat)": 0,
					"5-262 Mobilisasi alat & Keamanan": 0,
					"5-263 Pengerasan Jalan": 0,
					"5-266 Persiapan Lahan dan Lain2": 0
				},
				"5-270 SARANA PRASARANA": {
					"5-271 Drainase": 0,
					"5-272 Jembatan Utama": 0,
					"5-273 Jembatan Dalam": 0,
					"5-274 Pengecoran Jalan": 0,
					"5-275 Tempat Pembuangan Sampah (TPS)": 0,
					"5-276 Taman/ Penghijauan": 0,
					"5-277 Pagar Batas": 0,
					"5-278 Gapura & Pos Jaga": 0,
					"5-279 Masjid/ Mushalla": 0,
					"5-280 Tempat Pemakaman Umum (TPU)": 0,
					"5-281 Direksikit": 0,
					"5-282 Keperluan Lain Lokasi": 0
				},
				"5-290 LISTRIK DAN AIR": {
					"5-291 Tiang dan Jaringan Listrik": 0,
					"5-292 SLO, BP KWH 900WATT": 0,
					"5-293 Pemasangan PJU": 0,
					"5-294 Pengeboran Sumur": 0,
					"5-295 Pipanisasi & Fasilitasi Air": 0
				},
				"5-300 KONTRUKSI": {
					"5-301 Pembangunan Rumah Type 30/60": 0,
					"5-302 Rumah Contoh": 0,
					"5-303 Tambahan Pekerjaan (Hook dll)": 0
				},
				"5-310 BIAYA AKAD NOTARIS": {
					"5-311 PPH": 0,
					"5-312 BPHTB": 0,
					"5-313 PPJB": 0,
					"5-314 AJB BN": 0,
					"5-315 ROYA": 0,
					"5-316 Akad Notaris": 0,
					"5-317 SPPT, PBB": 0
				},
				"5-320 BIAYA KONSULTAN & ENTERTAIN BANK": {
					"5-321 SLF Konsultan": 0,
					"5-322 Apraisal KJPP": 0,
					"5-323 Entertain Bank (OTS dll)": 0,
					"5-324 Provisi Bank": 0
				},
				"5-325 BIAYA AKAD KONSUMEN": {
					"5-326 Biaya Akad (SP3K)": 0,
					"5-327 DRBM, Entertain dan Konsumsi dll": 0
				}
			},
			"6-000 BIAYA OPERASIONAL": {
				"6-100 GAJI & UPAH": {
					"6-110 Gaji & Tunjangan Operasional Direksi": 0,
					"6-111 Gaji & Tunjangan Operasional Karyawan": 0,
					"6-112 Upah Harian Kantor": 0,
					"6-113 Lain-lain": 0
				},
				"6-200 MARKETING": {
					"6-210 Fee Booking": 0,
					"6-211 Fee Marketing": 0,
					"6-212 Promosi": 0,
					"6-213 Entertain & Lain2": 0
				},
				"6-300 OPERASIONAL LAINNYA": {
					"6-305 Sewa Kantor PT": 0,
					"6-306 Sewa Kantor Pemasaran": 0,
					"6-310 ATK & Peralatan kantor": 0,
					"6-311 Perlengkapan Kantor": 0,
					"6-312 Listrik, Air dan Telephone": 0,
					"6-313 Bensin, Tol dan Parkir": 0,
					"6-314 Retribusi & Entertain": 0,
					"6-315 Pantry (Mamin)": 0,
					"6-316 Lain-lain": 0
				}
			},
			"7-000 PENDAPATAN LAINNYA": {
				"7-000 PENDAPATAN LAINNYA": {
					"7-100 Pendapatan Bunga Bank": 0,
					"7-110 Pendapatan Hook": 0,
					"7-111 Pendapatan Kelebihan Tanah": 0,
					"7-112 Pendapatan Lainnya": 0
				}
			},
			"8-000 BIAYA LAINNYA": {
				"8-000 BIAYA LAINNYA": {
					"8-100 Administrasi bank": 0,
					"8-110 Apraisal Kredit Bank": 0,
					"8-120 Margin Pinjaman BPRS Mentari": 0,
					"8-121 Margin Pinjaman Bank - KYG - PPL": 0,
					"8-122 Margin Pinjaman lainnya": 0,
					"8-123 Roya": 0,
					"8-150 Beban Penyusutan": null,
					"8-200 ZIS": 0,
					"8-300 Insentif/ Jasprod": 0
				}
			},
			"9-000 BAGI HASIL/ TARIKAN PROFIT": {
				"9-000 BAGI HASIL/ TARIKAN PROFIT": {
					"9-100 Tarikan Komisaris": 0,
					"9-200 Tarikan Direktur Utama": 0,
					"9-300 Tarikan Project HL4": 0,
					"9-301 Tarikan Project AQILA": 0,
					"9-302 Tarikan Project Green Harmony": 0,
					"9-400 Tarikan Lain-lain": 0
				}
			}
		}
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

## Endpoint: Export Laba Rugi

### **GET** `/finance/report/laba-rugi/export`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| year | Tahun |
| month | Bulan |

### Contoh Response Berhasil
> code : 200
```blob
file.xlsx
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

## Endpoint: Get Cash In

### **GET** `/finance/report/cash-i`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| year | Tahun |
| month | Bulan |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": {
		"period": "September 2025",
		"total_pendapatan": 0,
		"total_pendapatan_lainnya": 0,
		"detail": {
			"4-000 PENDAPATAN": {
				"4-100 Pendapatan Akad KPR Bank": {
					"4-110 Pendapatan Pencairan Akad": 0,
					"4-111 Retensi Sertifikat Balik Nama": 0,
					"4-112 Retensi PBG": 0,
					"4-113 Retensi Air": 0,
					"4-114 Retensi Listrik": 0,
					"4-115 Retensi Bestek": 0,
					"4-116 Retensi Bangunan": 0,
					"4-118 Pendapatan SBUM": 0
				},
				"4-200 Penjualan Cash": {
					"4-210 Pendapatan Cash Subsidi": 0
				},
				"4-300 Pendapatan dari Konsumen": {
					"4-310 Pendapatan DP": 0,
					"4-320 Pendapatan Booking": 0
				}
			},
			"7-000 PENDAPATAN LAINNYA": {
				"7-000 PENDAPATAN LAINNYA": {
					"7-100 Pendapatan Bunga Bank": 0,
					"7-110 Pendapatan Hook": 0,
					"7-111 Pendapatan Kelebihan Tanah": 0,
					"7-112 Pendapatan Lainnya": 0
				}
			}
		}
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

## Endpoint: Export Cash In

### **GET** `/finance/report/cash-in/export`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| year | Tahun |
| month | Bulan |

### Contoh Response Berhasil
> code : 200
```blob
file.xlsx
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

## Endpoint: Get Neraca

### **GET** `/finance/report/neraca`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| year | Tahun |
| month | Bulan |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": {
		"1-000 AKTIVA": {
			"1-100 AKTIVA LANCAR": {
				"1-110 Kas": "900000",
				"1-221 bank Indo": "900000",
				"1-130 Piutang Usaha": "209099999.00",
				"1-131 Piutang Retensi": "0.00",
				"1-132 Piutang SBUM": 0,
				"1-133 Piutang Karyawan": 0
			},
			"1-200 AKTIVA TETAP": {
				"1-210 Tanah": 0,
				"1-220 Bangunan": 0,
				"1-230 Kendaraan": 0,
				"1-240 Peralatan & Perlengkapan": 0,
				"1-221 Akumulasi Penyusutan": 0,
				"1-250 Surat Berharga": 0
			}
		},
		"2-000 KEWAJIBAN": {
			"2-100 Hutang": {
				"2-110 Pinjaman BPRS Mentari": "35800000.00"
			}
		},
		"3-000 MODAL": {
			"3-000 MODAL": {
				"3-110 Modal Awal": "1299999.00",
				"3-300 Prive": 0,
				"3-400 Laba ditahan": 0,
				"3-500 Laba berjalan": 0,
				"3-600 Laba bulan lalu": 0
			}
		}
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

## Endpoint: Export Neraca

### **GET** `/finance/report/neraca/export`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan |
|----------|-----------|
| year | Tahun |
| month | Bulan |

### Contoh Response Berhasil
> code : 200
```blob
file.xlsx
```

### Contoh Response Gagal (jika token tidak valid)
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

