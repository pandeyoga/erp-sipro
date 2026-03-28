<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashOutMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'PEMBEBASAN LAHAN' => [
                'Pembebasan lahan',
                'Fee Mediator '
            ],
            'PERENCANAAN TEKNIS' => [
                'Konsultan',
                'RAB, Gambar Kerja & Site Plan Konsultan',
            ],
            'BIAYA SERTIFIKAT' => [
                "Pengukuran dan Peta Bidang",
                "PPH",
                "SPH/BPHTB",
                "Penerbitan Warkah Desa",
                "Retribusi Pajak BNPB (BPN)",
                "SPPT, PBB",
                "Pertek",
                "Penerbitan Sertifikat Induk",
                "Splicing Sertifikat",
            ],
            'BIAYA PERIJINAN' => [
                "ijin Warga RT,RW & Desa",
                "Rekom-rekom (Perijinan & Legalitas)",
                "Penerbitan PBG Induk (SKRD)",
                "Splicing PBG",
                "Asosiasi (APERSI)",
                "Entertain Perijinan",
            ],
            'KOMPENSASI WARGA' => [
                'Kompensasi Warga',
                'Lain-lain Kompensasi',
            ],
            'PEMATANGAN LAHAN' => [
                'Cut&Fill (Operasional Alat Berat)',
                'Mobilisasi alat & Keamanan',
                'Pengerasan Jalan',
                'Persiapan Lahan dan Lain2',
            ],
            'SARANA PRASARANA' => [
                "Drainase",
                "Jembatan Utama",
                "Jembatan Dalam",
                "Pengecoran Jalan",
                "Tempat Pembuangan Sampah (TPS)",
                "Taman/Penghijauan",
                "Pagar Batas",
                "Gapura & Pos Jaga",
                "Masjid/ Mushalla",
                "Tempat Pemakaman Umum (TPU)",
                "Direksikit",
                "Keperluan Lain Lokasi",
            ],
            'LISTRIK DAN AIR' => [
                "Tiang dan Jaringan Listrik",
                "SLO, BP KWH 900WATT",
                "Pemasangan PJU",
                "Pengeboran Sumur",
                "Pipanisasi & Fasilitasi Air",
            ],
            'KONTRUKSI' => [
                'Pembangunan Rumah',
                'Rumah Contoh',
                'Tambahan Pekerjaan (Hook dll)',
            ],
            'BIAYA AKAD NOTARIS' => [
                "PPH",
                "BPHTB",
                "PPJB",
                "AJB BN",
                "ROYA",
                "Akad Notaris",
                "SPPT, PBB",
            ],
            'BIAYA KONSULTAN & ENTERTAIN BANK' => [
                'SLF Konsultan',
                'Apraisal KJPP',
                'Entertain Bank (OTS dll)',
                'Provisi Bank',
            ],
            'BIAYA AKAD KONSUMEN' => [
                'Biaya Akad (SP3K)',
                'Entertain dan Konsumsi dll',
            ],
            'GAJI & UPAH' => [
                'Gaji & Tunjangan Operasional Direksi',
                'Gaji & Tunjangan Operasional Karyawan',
                'Upah Harian Kantor',
                'Lain-lain',
            ],
            'MARKETING' => [
                'Fee Booking',
                'Fee Marketing',
                'Promosi',
                'Entertain & Lain2',
            ],
            'OPERASIONAL LAINNYA' => [
                "Sewa Kantor PT",
                "Sewa Kantor Pemasaran",
                "ATK & Peralatan kantor",
                "Perlengkapan Kantor",
                "Listrik, Air dan Telephone",
                "Bensin, Tol dan Parkir",
                "Retribusi & Entertain",
                "Pantry (Mamin)",
                "Lain-lain",
            ],
            'BIAYA LAINNYA' => [
                "Administrasi bank",
                "Apraisal Kredit Bank",
                "Margin Pinjaman BPRS Mentari",
                "Margin Pinjaman Bank KYG-PPL",
                "Margin Pinjaman lainnya",
                "Roya",
                "Beban Penyusutan",
                "ZIS",
                "Insentif/Jasprod",
            ],
            'TARIKAN' => [
                "Tarikan Komisaris",
                "Tarikan Direktur Utama",
                "Tarikan Project HL4",
                "Tarikan Project AQILA",
                "Tarikan Project Green Harmony",
                "Tarikan Lain-lain",
            ],
            "Hutang Karyawan" => [
                "Hutang Karyawan "
            ]
        ];

        // truncate
        DB::table('cash_out_categories')->truncate();
        DB::table('cash_out_sub_categories')->truncate();
        DB::table('cash_flow_outs')->truncate();

        foreach ($data as $category => $items) {
            $name = Str::ucfirst(Str::lower($category));
            $id = DB::table('cash_out_categories')->insertGetId([
                'id' => Str::uuid(),
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            foreach ($items as $item) {
                DB::table('cash_out_sub_categories')->insert([
                    'id' => Str::uuid(),
                    'category_id' => $id,
                    'name' => $item,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
