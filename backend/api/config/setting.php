<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Lead Status Durations
    |--------------------------------------------------------------------------
    |
    | Define the lead status durations in days.
    |
    */

    'lead_statuses' => [
        'new',
        'prospect',
        'reserve',
        'document_and_legal_process',
        'complete',
        'cancel',
    ],

    'reservation_statuses' => [
        'pending',
        'confirmed',
        'canceled',
        'expired',
    ],
    
    'construction_phases' => [
        'pondasi',
        'naik_bata',
        'naik_atap',
        'plester_aci',
        'keramik_cat',
        'finishing'
    ],

    'document_statuses' => [
        'input, verification, completed'
    ],

    'buyer_document_types' => [
        ['code' => 'ktp_applicant', 'label' => 'KTP Pemohon'],
        ['code' => 'ktp_partner', 'label' => 'KTP Pasangan*'],
        ['code' => 'npwp', 'label' => 'NPWP'],
        ['code' => 'kk', 'label' => 'Kartu Keluarga'],
        ['code' => 'marriage_or_divorce_certificate', 'label' => 'Surat Nikah/Cerai'],
        ['code' => 'applicant_photo', 'label' => 'Foto Pemohon'],
        ['code' => 'partner_photo', 'label' => 'Foto Pasangan'],
        ['code' => 'house_ownership_certificate', 'label' => 'Surat Kepemilikan Rumah'],
        ['code' => 'domisili_certificate', 'label' => 'Surat Keterangan Domisili'],
        ['code' => 'spr_bank', 'label' => 'SPR Bank'],
    ],

    'dummy_survey_location' => [
        [
            "id" => "6d20a605-d856-4102-bd8d-461a35e23988",
            "name" => "Dummy Survey Location 1",
        ],
        [
            "id" => "6d20a605-d856-4102-bd8d-461a35e23989",
            "name" => "Dummy Survey Location 2",
        ],
    ],

    'dummy_property' => [
        [
            "id" => "6d20a605-d856-4102-bd8d-461a35e23990",
            "name" => "Dummy Property 1",
        ],
        [
            "id" => "6d20a605-d856-4102-bd8d-461a35e23991",
            "name" => "Dummy Property 2",
        ],
    ],

    'bank_list' => [
        "btn" => [
            "code" => "btn",
            "name" => "Bank BTN",
        ],
        "bjb" => [
            "code" => "bjb",
            "name" => "Bank BJB",
        ],
        "bni" => [
            "code" => "bni",
            "name" => "Bank BNI",
        ],
        "bri" => [
            "code" => "bri",
            "name" => "Bank BRI",
        ],
        "bca" => [
            "code" => "bca",
            "name" => "Bank BCA",
        ],
        "mandiri" => [
            "code" => "mandiri",
            "name" => "Bank Mandiri",
        ],

    ],
    
    'payment_statuses' => [
        'proses_bank',
        'sp3k',
        'akad_kredit',
        'cash',
    ],

    'final_legality_statuses' => [
        'pending',
        'bast',
        'retention',
        'complete',
    ],

    'payment_checklist' => [
        ['code' => 'surat_permohonan_akad', 'nama' => 'Surat Permohonan Akad'],
        ['code' => 'permohonan_surat_pip_listrik', 'nama' => 'Permohonan surat pip listrik'],
        ['code' => 'permohonan_surat_pip_jalan', 'nama' => 'Permohonan surat pip jalan'],
        ['code' => 'permohonan_surat_pip_air', 'nama' => 'Permohonan surat pip air'],
        ['code' => 'surat_permohonan_appraisal', 'nama' => 'Surat permohonan  appraisal'],
        ['code' => 'permohonan_uji_flpp', 'nama' => 'Permohonan uji flpp'],
        ['code' => 'upload_foto_rumah', 'nama' => 'Upload foto rumah'],
        ['code' => 'permohonan_akad_ke_notaris', 'nama' => 'Permohonan Akad ke notaris'],
        ['code' => 'upload_data_debitur_ke_notaris', 'nama' => 'Upload data debitur ke notaris'],
        ['code' => 'si_pencairan', 'nama' => 'Si pencairan'],
        ['code' => 'si_notaris', 'nama' => 'Si notaris'],
        ['code' => 'si_kyg', 'nama' => 'Si kyg'],
        ['code' => 'spk', 'nama' => 'SPK'],
        ['code' => 'approval_flpp', 'nama' => 'Approval flpp'],
        ['code' => 'approval_foto_rumah', 'nama' => 'Approval foto rumah'],
        ['code' => 'cover_note', 'nama' => 'Cover note'],
        ['code' => 'akta_jual_beli', 'nama' => 'Akta jual beli'],
        ['code' => 'balik_nama_sertifikat', 'nama' => 'Balik nama sertifikat'],
    ],
    
    'lead_status_durations' => [
        'new' => 1,
        'prospect' => 3,
        'reserve' => 3,
        'payment' => 4,
        'document_and_legal_process' => 3,
        'complete' => null,
        'cancel' => null,
    ],

    'retention_periode_month' => 3,


    "laba_rugi" => [
        '4-000 PENDAPATAN' => [
            '4-100 Pendapatan Akad KPR Bank' => [
                '4-110 Pendapatan Pencairan Akad' => "penjualan-rumah.kpr.pencairan-kpr.pencairan-akad",
                '4-111 Retensi Sertifikat Balik Nama' => "penjualan-rumah.kpr.pencairan-kpr.retensi-sertifikat",
                '4-112 Retensi PBG' => "penjualan-rumah.kpr.pencairan-kpr.imbpbg",
                '4-113 Retensi Air' => "penjualan-rumah.kpr.pencairan-kpr.retensi-air",
                '4-114 Retensi Listrik' => "penjualan-rumah.kpr.pencairan-kpr.restensi-listrik",
                '4-115 Retensi Bestek' => "penjualan-rumah.kpr.pencairan-kpr.retensi-bestek",
                '4-116 Retensi Bangunan' => "penjualan-rumah.kpr.pencairan-kpr.retensi-bangunan",
                '4-118 Pendapatan SBUM' => "pencarian-sbum.pencairan-sbum.general.pencairan-sbum",
            ],
            '4-200 Penjualan Cash' => [
                '4-210 Pendapatan Cash Subsidi' => "penjualan-rumah.cash-keras.penambahan-spek.penambahan-tanah,penjualan-rumah.cash-keras.penambahan-spek.penambahan-spek-bangunan,penjualan-rumah.cash-keras.pembayaran-bertahap.pelunasan,penjualan-rumah.cash-keras.general.all-in,penjualan-rumah.cash-bertahap.penambahan-spek.penambahan-tanah,penjualan-rumah.cash-bertahap.penambahan-spek.penambahan-spek-bangunan,penjualan-rumah.cash-bertahap.general.all-in,penjualan-rumah.cash-bertahap.penambahan-spek.hook,penjualan-rumah.cash-keras.penambahan-spek.hook,penjualan-rumah.cash-bertahap.pembayaran-bertahap.cicilan-pelunasan",
            ],
            '4-300 Pendapatan dari Konsumen' => [
                '4-310 Pendapatan DP' => "penjualan-rumah.cash-bertahap.pembayaran-bertahap.dp,penjualan-rumah.cash-keras.pembayaran-bertahap.dp",
                '4-320 Pendapatan Booking' => "booking.biaya-booking.general.biaya-booking",
            ],
        ],

        '5-000 BIAYA ATAS PENDAPATAN' => [
            "5-100 HARGA POKOK PRODUKSI" => [
                '5-101 Harga Pokok Produksi' => "",
            ],
            '5-200 PEMBEBASAN LAHAN' => [
                '5-201 Pembebasan lahan' => "pembebasan-lahan.pembebasan-lahan",
                '5-202 Fee Mediator' => "pembebasan-lahan.fee-mediator",
            ],
            '5-210 PERENCANAAN TEKNIS' => [
                '5-211 Konsultan' => "perencanaan-teknis.konsultan",
                '5-212 RAB, Gambar Kerja & Site Plan Konsultan' => "perencanaan-teknis.rab-gambar-kerja-site-plan-konsultan",
            ],
            '5-220 BIAYA SERTIFIKAT' => [
                '5-221 Pengukuran dan Peta Bidang' => "biaya-sertifikat.pengukuran-dan-peta-bidang",
                '5-222 PPH' => "biaya-sertifikat.pph",
                '5-223 SPH/BPHTB' => "biaya-sertifikat.sphbphtb",
                '5-224 Penerbitan Warkah Desa' => "biaya-sertifikat.penerbitan-warkah-desa",
                '5-225 Retribusi Pajak BNPB (BPN)' => "biaya-sertifikat.retribusi-pajak-bnpb-bpn",
                '5-226 SPPT, PBB' => "biaya-sertifikat.sppt-pbb",
                '5-227 Pertek' => "biaya-sertifikat.pertek",
                '5-228 Penerbitan Sertifikat Induk' => "biaya-sertifikat.penerbitan-sertifikat-induk",
                '5-229 Splitcing Sertifikat' => "biaya-sertifikat.splicing-sertifikat",
            ],
            '5-230 BIAYA PERIJINAN' => [
                '5-231 Ijin Warga RT,RW & Desa' => "biaya-perijinan.ijin-warga-rtrw-desa",
                '5-232 Rekom-rekom (Perijinan & Legalitas)' => "biaya-perijinan.rekom-rekom-perijinan-legalitas",
                '5-233 Penerbitan PBG Induk (SKRD)' => "biaya-perijinan.penerbitan-pbg-induk-skrd",
                '5-234 Splitcing PBG' => "biaya-perijinan.splicing-pbg",
                '5-235 Asosiasi (APERSI)' => "biaya-perijinan.asosiasi-apersi",
                '5-236 Entertain Perijinan' => "biaya-perijinan.entertain-perijinan",
            ],
            '5-255 KOMPENSASI WARGA' => [
                '5-256 Kompensasi Warga' => "kompensasi-warga.kompensasi-warga",
                '5-257 Lain-lain Kompensasi' => "kompensasi-warga.lain-lain-kompensasi",
            ],
            '5-260 PEMATANGAN LAHAN' => [
                '5-261 Cut&Fill (Operasional Alat Berat)' => "pematangan-lahan.cutfill-operasional-alat-berat",
                '5-262 Mobilisasi alat & Keamanan' => "pematangan-lahan.mobilisasi-alat-keamanan",
                '5-263 Pengerasan Jalan' => "pematangan-lahan.pengerasan-jalan",
                '5-266 Persiapan Lahan dan Lain2' => "pematangan-lahan.persiapan-lahan-dan-lain2",
            ],
            '5-270 SARANA PRASARANA' => [
                '5-271 Drainase' => "sarana-prasarana.drainase",
                '5-272 Jembatan Utama' => "sarana-prasarana.jembatan-utama",
                '5-273 Jembatan Dalam' => "sarana-prasarana.jembatan-dalam",
                '5-274 Pengecoran Jalan' => "sarana-prasarana.pengecoran-jalan",
                '5-275 Tempat Pembuangan Sampah (TPS)' => "sarana-prasarana.tempat-pembuangan-sampah-tps",
                '5-276 Taman/ Penghijauan' => "sarana-prasarana.tamanpenghijauan",
                '5-277 Pagar Batas' => "sarana-prasarana.pagar-batas",
                '5-278 Gapura & Pos Jaga' => "sarana-prasarana.gapura-pos-jaga",
                '5-279 Masjid/ Mushalla' => "sarana-prasarana.masjid-mushalla",
                '5-280 Tempat Pemakaman Umum (TPU)' => "sarana-prasarana.tempat-pemakaman-umum-tpu",
                '5-281 Direksikit' => "sarana-prasarana.direksikit",
                '5-282 Keperluan Lain Lokasi' => "sarana-prasarana.keperluan-lain-lokasi",
            ],
            '5-290 LISTRIK DAN AIR' => [
                '5-291 Tiang dan Jaringan Listrik' => "listrik-dan-air.tiang-dan-jaringan-listrik",
                '5-292 SLO, BP KWH 900WATT' => "listrik-dan-air.slo-bp-kwh-900watt",
                '5-293 Pemasangan PJU' => "listrik-dan-air.pemasangan-pju",
                '5-294 Pengeboran Sumur' => "listrik-dan-air.pengeboran-sumur",
                '5-295 Pipanisasi & Fasilitasi Air' => "listrik-dan-air.pipanisasi-fasilitasi-air",
            ],
            '5-300 KONTRUKSI' => [
                '5-301 Pembangunan Rumah Type 30/60' => "kontruksi.pembangunan-rumah",
                '5-302 Rumah Contoh' => "kontruksi.rumah-contoh",
                '5-303 Tambahan Pekerjaan (Hook dll)' => "kontruksi.tambahan-pekerjaan-hook-dll",
            ],
            '5-310 BIAYA AKAD NOTARIS' => [
                '5-311 PPH' => "biaya-akad-notaris.pph",
                '5-312 BPHTB' => "biaya-akad-notaris.bphtb",
                '5-313 PPJB' => "biaya-akad-notaris.ppjb",
                '5-314 AJB BN' => "biaya-akad-notaris.ajb-bn",
                '5-315 ROYA' => "biaya-akad-notaris.roya",
                '5-316 Akad Notaris' => "biaya-akad-notaris.akad-notaris",
                '5-317 SPPT, PBB' => "biaya-akad-notaris.sppt-pbb",
            ],
            '5-320 BIAYA KONSULTAN & ENTERTAIN BANK' => [
                '5-321 SLF Konsultan' => "biaya-konsultan-entertain-bank.slf-konsultan",
                '5-322 Apraisal KJPP' => "biaya-konsultan-entertain-bank.apraisal-kjpp",
                '5-323 Entertain Bank (OTS dll)' => "biaya-konsultan-entertain-bank.entertain-bank-ots-dll",
                '5-324 Provisi Bank' => "biaya-konsultan-entertain-bank.provisi-bank"
            ],
            '5-325 BIAYA AKAD KONSUMEN' => [
                '5-326 Biaya Akad (SP3K)' => "biaya-akad-konsumen.biaya-akad-sp3k",
                '5-327 DRBM, Entertain dan Konsumsi dll' => "biaya-akad-konsumen.entertain-dan-konsumsi-dll",
            ]
        ],

        '6-000 BIAYA OPERASIONAL' => [
            '6-100 GAJI & UPAH' => [
                '6-110 Gaji & Tunjangan Operasional Direksi' => "gaji-upah.gaji-tunjangan-operasional-direksi",
                '6-111 Gaji & Tunjangan Operasional Karyawan' => "gaji-upah.gaji-tunjangan-operasional-karyawan",
                '6-112 Upah Harian Kantor' => "gaji-upah.upah-harian-kantor",
                '6-113 Lain-lain' => "gaji-upah.lain-lain",
            ],
            '6-200 MARKETING' => [
                '6-210 Fee Booking' => "marketing.fee-booking",
                '6-211 Fee Marketing' => "marketing.fee-marketing",
                '6-212 Promosi' => "marketing.promosi",
                '6-213 Entertain & Lain2' => "marketing.entertain-lain2",
            ],
            '6-300 OPERASIONAL LAINNYA' => [
                '6-305 Sewa Kantor PT' => "operasional-lainnya.sewa-kantor-pt",
                '6-306 Sewa Kantor Pemasaran' => "operasional-lainnya.sewa-kantor-pemasaran",
                '6-310 ATK & Peralatan kantor' => "operasional-lainnya.atk-peralatan-kantor",
                '6-311 Perlengkapan Kantor' => "operasional-lainnya.perlengkapan-kantor",
                '6-312 Listrik, Air dan Telephone' => "operasional-lainnya.listrik-air-dan-telephone",
                '6-313 Bensin, Tol dan Parkir' => "operasional-lainnya.bensin-tol-dan-parkir",
                '6-314 Retribusi & Entertain' => "operasional-lainnya.retribusi-entertain",
                '6-315 Pantry (Mamin)' => "operasional-lainnya.pantry-mamin",
                '6-316 Lain-lain' => "operasional-lainnya.lain-lain",
            ],
        ],

        '7-000 PENDAPATAN LAINNYA' => [
            ['7-100 Pendapatan Bunga Bank' => "pendapatan-bunga-bank.bunga-bank.general.bunga-bank"],
            ['7-110 Pendapatan Hook' => "penjualan-rumah.cash-bertahap.penambahan-spek.hook,penjualan-rumah.kpr.penambahan-spek.hook,penjualan-rumah.cash-keras.penambahan-spek.hook"],
            ['7-111 Pendapatan Kelebihan Tanah' => "penjualan-rumah.cash-keras.penambahan-spek.penambahan-tanah,penjualan-rumah.kpr.penambahan-spek.penambahan-tanah,penjualan-rumah.cash-bertahap.penambahan-spek.penambahan-tanah"],
            ['7-112 Pendapatan Lainnya' => "penjualan-rumah.cash-keras.penambahan-spek.penambahan-spek-bangunan,penjualan-rumah.cash-bertahap.penambahan-spek.penambahan-spek-bangunan,penjualan-rumah.kpr.penambahan-spek.penambahan-spek-bangunan"],
        ],

        '8-000 BIAYA LAINNYA' => [
            ['8-100 Administrasi bank' => "biaya-lainnya.administrasi-bank"],
            ['8-110 Apraisal Kredit Bank' => "biaya-lainnya.apraisal-kredit-bank"],
            ['8-120 Margin Pinjaman BPRS Mentari' => "biaya-lainnya.margin-pinjaman-bprs-mentari"],
            ['8-121 Margin Pinjaman Bank - KYG - PPL' => "biaya-lainnya.margin-pinjaman-bank-kyg-ppl"],
            ['8-122 Margin Pinjaman lainnya' => "biaya-lainnya.margin-pinjaman-lainnya"],
            ['8-123 Roya' => "biaya-lainnya.roya"],
            ['8-150 Beban Penyusutan' => ">>beban-penyusutan"],
            ['8-200 ZIS' => "biaya-lainnya.zis"],
            ['8-300 Insentif/ Jasprod' => "biaya-lainnya.insentifjasprod"],
        ],

        '9-000 BAGI HASIL/ TARIKAN PROFIT' => [
            ['9-100 Tarikan Komisaris' => "tarikan.tarikan-komisaris"],
            ['9-200 Tarikan Direktur Utama' => "tarikan.tarikan-direktur-utama"],
            ['9-300 Tarikan Project HL4' => "tarikan.tarikan-project-hl4"],
            ['9-301 Tarikan Project AQILA' => "tarikan.tarikan-project-aqila"],
            ['9-302 Tarikan Project Green Harmony' => "tarikan.tarikan-project-green-harmony"],
            ['9-400 Tarikan Lain-lain' => "tarikan.tarikan-lain-lain"],
        ],
    ],

    "cash_in_report" => [
        '4-000 PENDAPATAN' => [
            '4-100 Pendapatan Akad KPR Bank' => [
                '4-110 Pendapatan Pencairan Akad' => "penjualan-rumah.kpr.pencairan-kpr.pencairan-akad",
                '4-111 Retensi Sertifikat Balik Nama' => "penjualan-rumah.kpr.pencairan-kpr.retensi-sertifikat",
                '4-112 Retensi PBG' => "penjualan-rumah.kpr.pencairan-kpr.imbpbg",
                '4-113 Retensi Air' => "penjualan-rumah.kpr.pencairan-kpr.retensi-air",
                '4-114 Retensi Listrik' => "penjualan-rumah.kpr.pencairan-kpr.restensi-listrik",
                '4-115 Retensi Bestek' => "penjualan-rumah.kpr.pencairan-kpr.retensi-bestek",
                '4-116 Retensi Bangunan' => "penjualan-rumah.kpr.pencairan-kpr.retensi-bangunan",
                '4-118 Pendapatan SBUM' => "pencarian-sbum.pencairan-sbum.general.pencairan-sbum",
            ],
            '4-200 Penjualan Cash' => [
                '4-210 Pendapatan Cash Subsidi' => "penjualan-rumah.cash-keras.penambahan-spek.penambahan-tanah,penjualan-rumah.cash-keras.penambahan-spek.penambahan-spek-bangunan,penjualan-rumah.cash-keras.pembayaran-bertahap.pelunasan,penjualan-rumah.cash-keras.general.all-in,penjualan-rumah.cash-bertahap.penambahan-spek.penambahan-tanah,penjualan-rumah.cash-bertahap.penambahan-spek.penambahan-spek-bangunan,penjualan-rumah.cash-bertahap.general.all-in,penjualan-rumah.cash-bertahap.penambahan-spek.hook,penjualan-rumah.cash-keras.penambahan-spek.hook,penjualan-rumah.cash-bertahap.pembayaran-bertahap.cicilan-pelunasan",
            ],
            '4-300 Pendapatan dari Konsumen' => [
                '4-310 Pendapatan DP' => "penjualan-rumah.cash-bertahap.pembayaran-bertahap.dp,penjualan-rumah.cash-keras.pembayaran-bertahap.dp",
                '4-320 Pendapatan Booking' => "booking.biaya-booking.general.biaya-booking",
            ],
        ],
        '7-000 PENDAPATAN LAINNYA' => [
            ['7-100 Pendapatan Bunga Bank' => "pendapatan-bunga-bank.bunga-bank.general.bunga-bank"],
            ['7-110 Pendapatan Hook' => "penjualan-rumah.cash-bertahap.penambahan-spek.hook,penjualan-rumah.kpr.penambahan-spek.hook,penjualan-rumah.cash-keras.penambahan-spek.hook"],
            ['7-111 Pendapatan Kelebihan Tanah' => "penjualan-rumah.cash-keras.penambahan-spek.penambahan-tanah,penjualan-rumah.kpr.penambahan-spek.penambahan-tanah,penjualan-rumah.cash-bertahap.penambahan-spek.penambahan-tanah"],
            ['7-112 Pendapatan Lainnya' => "penjualan-rumah.cash-keras.penambahan-spek.penambahan-spek-bangunan,penjualan-rumah.cash-bertahap.penambahan-spek.penambahan-spek-bangunan,penjualan-rumah.kpr.penambahan-spek.penambahan-spek-bangunan"],
        ]
    ]

];
