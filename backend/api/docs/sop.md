# 🧾 SOP CRM

---

## ✅ 1. Membuat Contact Baru  
**📍 Halaman:** Contacts

### 👣 Langkah-langkah:
1. Klik tombol **"Create New"**.
2. Isi:
   - `Name` (wajib)
   - `Phone` (wajib)
   - `Email` dan `Location` (opsional)
3. Klik **Save**.

### 🔁 Alternatif:
- Klik tombol **"Import Excel"** untuk unggah data massal.
- Gunakan file template Excel yang dapat diunduh di bagian kanan atas.

### ⚠️ Catatan:
- Sistem otomatis mendeteksi **duplicate** berdasarkan nomor telepon.
- Contact yang sudah diproses menjadi **Lead** tidak dapat dihapus.

---

## ✅ 2. Membuat Lead dari Contact  
**📍 Halaman:** Lead → Create New

### 👣 Langkah-langkah:
1. Klik **"Create New"**.
2. Pilih **Contact**: hanya muncul kontak yang belum menjadi lead.
3. Form akan otomatis mengisi:
   - `Phone`
   - `Email`
4. Lengkapi data:
   - `Survey Location`
   - `Survey Date`
   - `Lead Source` (contoh: Marketing, Referral)  
     - Jika `Lead Source = Marketing`, maka **Select Agent wajib diisi**.
   - `PIC`
   - `Notes` (opsional)
5. Klik **Save Lead**.

### ⚠️ Validasi:
- Agent marketing wajib diisi **hanya jika Lead Source = Marketing**.

---

## 🔄 2.1 Update Status Lead  
**📍 Halaman:** Lead → Edit

### 👣 Langkah-langkah:
1. Klik tombol **Edit** pada lead yang ingin diperbarui.
2. Di bagian bawah atau samping form, akan muncul:
   - Field **Status** (readonly jika sudah `prospect`)
   - **Default status:** `new`
   - **Pilihan update:** hanya dapat diubah ke `prospect`
3. Pilih `prospect`, lalu klik **Save**.

### ⚠️ Catatan:
- Setelah status berubah menjadi **prospect**, maka lead dapat diproses menjadi reservation.
- Tidak dapat kembali dari `prospect` ke `new`.

---

## ✅ 3. Membuat Reservation  
**📍 Halaman:** Reservation → Create New

### 👣 Langkah-langkah:
1. Klik **"Create New"**.
2. Pilih **Prospect Lead** (hanya tampil jika status lead adalah `prospect`).
3. Data akan otomatis terisi:
   - `Phone`, `Email`, `Survey Date`, `Agent`
4. Lengkapi informasi:
   - `Reservation Date`
   - `Property`
   - `Reservation Fee`
   - `Notes` (opsional)
5. Klik **Save Reservation**.

---

## ✏️ 3.1. Edit Reservation  
**📍 Halaman:** Reservation → Edit

### 👣 Langkah-langkah:
1. Klik tombol **Edit** pada reservation yang ingin diubah.
2. Anda dapat:
   - Ubah **Status**: `pending`, `confirmed`, `canceled`, `expired`
   - Upload:
     - **Reservation Proof** (image/pdf)
     - **Reservation Letter** (pdf)
   - Update **Reservation Fee** dan **Notes**
3. Klik **Save**.

## ✅ 4. Lead Document (Dokumen Konsumen)
📍 Halaman: Document → Create New / Edit / Verify

### 🎯 Tujuan:
Mengunggah dan melengkapi seluruh dokumen persyaratan konsumen setelah reservasi.

---

### 👣 Langkah-langkah: Create / Edit Document

1. Klik **Create New** di halaman Document.
2. Pilih **Lead** dari dropdown (hanya yang sudah memiliki reservation).
3. Field seperti `Phone` dan `Email` akan otomatis terisi.
4. Upload dokumen konsumen sesuai peran:
   - 📄 **KTP Pemohon / Pasangan**
   - 📄 **NPWP, KK, Surat Nikah / Cerai**
   - 📷 **Foto Pemohon / Pasangan**
   - 📃 **Surat Kepemilikan Rumah / Domisili**
5. Centang **Checklist Pekerja / Wirausaha** sesuai status pekerjaan:
   - Materai, Slip Gaji, Rekening Koran, Formulir Bank dan FLPP, dll.
6. Upload dokumen developer: **SPR Bank**
7. Tambahkan catatan bila perlu.
8. Klik **Save**.

---

### ✅ Status Dokumen:
- **input** → Saat dokumen baru dibuat.
- **verification** → Setelah dokumen dikirim untuk diverifikasi.
- **completed** → Semua dokumen dinyatakan valid (verified).

---

### 🔍 Langkah-langkah: Verifikasi Dokumen

1. Klik tombol **"Verify"** pada baris dokumen di halaman utama.
2. Di halaman verifikasi akan ditampilkan semua dokumen yang telah diunggah.
3. Klik **"Lihat Dokumen"** untuk melihat file.
4. Tandai setiap dokumen dengan status:
   - ✅ `Verified`
   - ❌ `Unverified`
5. Setelah semua dokumen diperiksa dan valid, status otomatis akan menjadi `completed`.

---

### ⚠️ Validasi:
- Semua dokumen wajib diunggah dalam format yang valid (jpg, png, pdf).
- Checklist **wajib diisi** tergantung pekerjaan:
  - Pekerja → slip gaji, telp atasan, serlok kantor, dll.
  - Wirausaha → SK Usaha, neraca penghasilan, foto usaha, dll.
- Dokumen yang tidak lengkap atau tidak valid akan **menahan proses ke pembayaran**.

---

### 🔁 Alur Status:
```plaintext
input → verification → completed
```
## ✅ 5. Lead Payment (Pembayaran Properti)
📍 Halaman: Payment → Create New / Edit

### 🎯 Tujuan:
Menentukan metode pembayaran properti oleh calon pembeli (Cash atau KPR), serta melengkapi proses dokumen SP3K dan Akad Kredit jika memilih KPR.

---

### 👣 Langkah-langkah: Create Payment

1. Klik **Create New** pada halaman Payment.
2. Pilih **Lead** dari dropdown.  
   > Hanya lead yang status dokumennya sudah `completed` (selesai diverifikasi) yang akan muncul.
3. Field **Email** dan **No. Telepon** akan terisi otomatis.
4. Pilih **Payment Type**:
   - `KPR`
   - `Cash`
5. Jika memilih `KPR`, centang satu atau lebih bank dari daftar:
   - BTN, BNI, BRI, BCA, BJB, Mandiri
6. Tambahkan **Catatan Tambahan** bila perlu.
7. Klik **Save** untuk menyimpan data awal pembayaran.

---

### ✅ Status Pembayaran:
- `proses_bank`: status awal setelah data dibuat
- `sp3k`: dokumen SP3K telah diunggah & disetujui
- `akad_kredit`: semua dokumen akad lengkap & disetujui
- `cash`: jika metode pembayaran bukan KPR

---

### 👁️ Tampilan List Payment

Kolom utama:
- `Name`, `Phone`, `Notes`
- `KPR Status`: status alur (proses_bank, sp3k, akad_kredit, cash)
- `DueDates`: durasi sejak data dibuat
- Aksi: **Edit**, **Delete**

---

### ✏️ Langkah-langkah: Edit Payment (SP3K & Akad Kredit)

#### 📂 Bagian SP3K
1. Isi:
   - **Status SP3K**: `Approved` / `Pending`
   - Upload **SP3K Document** (PDF)
   - **Kode SP3K**
   - **Nomor SP3K**
   - **Bank SP3K**
   - **Tanggal SP3K** dan **Tanggal Expired SP3K**

#### 🖋️ Bagian Akad Kredit
2. Isi:
   - **Status Akad Kredit**: `Approved` / `Pending`
   - Upload **Dokumen Penandatanganan Akad Kredit**
3. Centang checklist dokumen sebagai bukti kelengkapan:
   - Surat Permohonan Akad
   - Permohonan Surat PIP (Air, Listrik, Jalan)
   - Surat Appraisal
   - Upload Foto Rumah
   - Upload Data Debitur ke Notaris
   - SI (Instruksi) Pencairan, KYG, Notaris
   - SPK, Cover Note, Approval FLPP
   - Akta Jual Beli, Balik Nama Sertifikat
4. Tambahkan **Catatan Tambahan** jika ada.
5. Klik **Save**.

---

### ⚠️ Validasi:

- Jika memilih **KPR**, user **harus memilih minimal satu bank**.
- Data SP3K dan Akad Kredit **wajib diisi dan lengkap** saat status disetujui.
- Checklist digunakan sebagai syarat kelulusan ke **Final Legality**.
- Tidak boleh membuat payment ganda untuk satu lead.

---

### 🔁 Alur Status Singkat:
```plaintext
Create Payment
   ↓
[proses_bank]
   ↓ (isi SP3K)
[sp3k]
   ↓ (upload akad + checkl
```

## ✅ 6. Final Legality (Legalitas Akhir)
📍 Halaman: Legalitas → Create New / Edit

### 🎯 Tujuan:
Melengkapi proses akhir legalitas penyerahan unit kepada konsumen setelah pembayaran selesai (KPR atau Cash), melalui dokumen BAST dan Retensi.

---

### 👣 Langkah-langkah: Create Legalitas Akhir

1. Klik **Create New** di halaman Legalitas.
2. Pilih **Lead** dari dropdown:
   > Hanya lead yang sudah menyelesaikan pembayaran (`status = akad_kredit` atau `cash`) yang akan muncul.
3. Field **Email** dan **No. Telepon** akan terisi otomatis.
4. Di bagian **BAST**, lengkapi:
   - Upload **Dokumen BAST** (PDF)
   - Upload **Foto Penyerahan BAST** (gambar)
   - Isi **Tanggal BAST**
5. Klik **Save**.  
   > Setelah ini, status akan otomatis menjadi `BAST`.

---

### 👁️ Tampilan List Legalitas Akhir

Menampilkan daftar legalitas berdasarkan status:
- **BAST**
- **Retention**
- **Complete**

Kolom:
- Nama, No. Telepon, Notes
- Legalitas Akhir Status
- Property, DueDates
- Aksi: **Edit**, **Delete**

---

### ✏️ Langkah-langkah: Update Retensi

1. Klik **Edit** pada data legalitas yang sudah memiliki BAST.
2. Di bagian **RETENSI**, lengkapi:
   - Upload **Dokumen Retensi** (PDF)
   - Upload **Foto Penyerahan Retensi**
   - Isi **Tanggal Retensi Dimulai**
3. Klik **Save**.
   > Setelah tersimpan, status akan berubah menjadi `Retention`.

---

### ✅ Otomatisasi Status Complete

- Sistem akan secara otomatis mengubah **status dari `Retention` menjadi `Complete`** setelah **3 bulan dari tanggal Retensi Dimulai**.
- Tidak perlu input manual dari user/admin untuk menandai penyelesaian.

---

### ⚠️ Validasi:
- Dokumen BAST wajib disimpan terlebih dahulu sebelum mengisi data retensi.
- Format file: `.pdf`, `.jpg`, `.png`.
- **Tanggal Retensi** digunakan sebagai acuan sistem untuk menghitung otomatisasi penyelesaian.

---

### 🔁 Alur Status:
```plaintext
Create (Isi BAST)     → Status = BAST
Update (Isi Retensi)  → Status = Retention
+3 Bulan dari Retensi → Status = Complete (otomatis)
```
