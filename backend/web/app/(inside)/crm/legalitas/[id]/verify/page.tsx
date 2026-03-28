'use client'

import { useEffect, useState } from 'react'
import axios from '@/lib/axios'
import { PageLoading } from '@/components/loading/loading'
import { useParams } from 'next/navigation'
import ProtectedImage from '@/components/protected-image'

type DocumentStatus = 'valid' | 'invalid' | ''

interface VerifikasiDokumen {
  name: string
  label: string
  fileUrl?: string
  status?: DocumentStatus
  note?: string
}
const documentFields: VerifikasiDokumen[] = [
    { name: 'doc_ktp_applicant', label: 'KTP Pemohon' },
    { name: 'doc_ktp_partner', label: 'KTP Pasangan' },
    { name: 'doc_npwp', label: 'NPWP' },
    { name: 'doc_kk', label: 'Kartu Keluarga' },
    { name: 'doc_marriage_certificate', label: 'Surat Nikah' },
    { name: 'doc_divorce_certificate', label: 'Surat Cerai' },
    { name: 'doc_emergency_contact_ktp', label: 'KTP Kontak Darurat' },
    { name: 'doc_applicant_photo', label: 'Foto Pemohon' },
    { name: 'doc_partner_photo', label: 'Foto Pasangan' },
    { name: 'doc_unmarried_certificate', label: 'Surat Keterangan Belum Menikah' },
    { name: 'doc_house_ownership_certificate', label: 'Surat Kepemilikan Rumah' },
    { name: 'doc_domisili_certificate', label: 'Surat Domisili' },
    { name: 'doc_salary_slip_3_months', label: 'Slip Gaji 3 Bulan Terakhir' },
    { name: 'doc_bank_statement_3_months', label: 'Rekening Koran 3 Bulan Terakhir' },
    { name: 'doc_income_statement_6_months', label: 'Laporan Pendapatan 6 Bulan Terakhir' },
    { name: 'doc_bank_statement_6_months', label: 'Rekening Koran 6 Bulan Terakhir' },
    { name: 'doc_business_photo', label: 'Foto Usaha' },
    { name: 'doc_business_certificate', label: 'Surat Izin Usaha' },
    { name: 'doc_bank_form', label: 'Formulir Bank' },
    { name: 'doc_flpp', label: 'FLPP' },
  ]
  
  export interface LeadDocumentFile {
    type: string;            // kode dokumen seperti "ktp_applicant", "npwp", dst.
    is_uploaded: boolean;    // apakah file sudah di-upload
    is_validated: boolean;   // apakah file sudah divalidasi
    file_url: string | null; // URL file (jika ada), bisa null kalau belum upload
  }
  

export default function DocumentVerificationPage() {
  const { id } = useParams() as { id: string }
  const [documents, setDocuments] = useState<LeadDocumentFile[]>([])
  const [verifications, setVerifications] = useState<Record<string, DocumentStatus>>({})
  const [notes, setNotes] = useState<Record<string, string>>({})
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const fetch = async () => {
      try {
        const res = await axios.get(`/crm/lead-document/${id}`)
        setDocuments(res.data.data.documents)
      } catch (err) {
        console.error('Gagal mengambil dokumen:', err)
      } finally {
        setLoading(false)
      }
    }

    fetch()
  }, [id])

  const handleStatusChange = (name: string, status: DocumentStatus) => {
    setVerifications((prev) => ({ ...prev, [name]: status }))
  }

  const handleNoteChange = (name: string, note: string) => {
    setNotes((prev) => ({ ...prev, [name]: note }))
  }

  const handleSubmit = async () => {
    const hasilVerifikasi = documentFields.map((field) => ({
      name: field.name,
      status: verifications[field.name] || '',
      note: notes[field.name] || '',
    }))

    try {
      await axios.post(`/crm/document/${id}/verify`, { hasil: hasilVerifikasi })
      alert('Verifikasi berhasil dikirim.')
    } catch (err) {
      console.error(err)
      alert('Gagal mengirim verifikasi.')
    }
  }

  const total = documentFields.length
    const sudahDiverifikasiValid = Object.values(verifications).filter((v) => v === 'valid').length
    const sudahDiverifikasiInvalid = Object.values(verifications).filter((v) => v === 'invalid').length
    const belumDiperiksa = total - (sudahDiverifikasiValid + sudahDiverifikasiInvalid)



  useEffect(() => {
    const newVerifications: Record<string, DocumentStatus> = {}
  
    documentFields.forEach((doc) => {
      const matchedDoc = documents.find(d => `doc_${d.type}` === doc.name)
  
      if (matchedDoc?.is_validated) {
        newVerifications[doc.name] = 'valid'
      } else if (matchedDoc?.is_uploaded && !matchedDoc.is_validated) {
        newVerifications[doc.name] = ''
      }
    })
  
    setVerifications(newVerifications)
  }, [documents])

    
  if (loading) return <PageLoading />

  

  return (
    <div className=" mx-auto p-6">
      <h1 className="text-xl font-bold mb-6">Verifikasi Dokumen</h1>
      <div className="text-sm text-gray-500 mb-6">
        Total dokumen: <strong>{total}</strong> | 
        Sudah diverifikasi (Valid): <strong>{sudahDiverifikasiValid}</strong> | 
        Sudah diverifikasi (Tidak Valid): <strong>{sudahDiverifikasiInvalid}</strong> | 
        Belum diperiksa: <strong>{belumDiperiksa}</strong>
        </div>
      <div className="grid grid-cols-2 gap-6">
        {documentFields.map((doc) => {
          const matchedDoc = documents.find(d => `doc_${d.type}` === doc.name)
           
          const file_url = matchedDoc?.file_url
          const is_uploaded = matchedDoc?.is_uploaded
          const is_validated = matchedDoc?.is_validated
          return (
            <div key={doc.name} className="border p-4 rounded-lg">
              <div className="font-semibold">{doc.label}</div>
              {file_url ? (
                <ProtectedImage imageUrl={file_url} linkOnly={true}/>
              ) : (
                <p className="text-sm text-gray-400 italic">Tidak ada file</p>
              )}
              <div className="mt-2 flex items-center gap-4">
                <label className="label cursor-pointer">
                  <input
                    type="radio"
                    className="radio mr-2"
                    name={doc.name}
                    checked={verifications[doc.name] === 'valid'}
                    onChange={() => handleStatusChange(doc.name, 'valid')}
                  />
                  <span className="label-text">Valid</span>
                </label>
                <label className="label cursor-pointer">
                  <input
                    type="radio"
                    className="radio mr-2"
                    name={doc.name}
                    checked={verifications[doc.name] === 'invalid'}
                    onChange={() => handleStatusChange(doc.name, 'invalid')}
                  />
                  <span className="label-text">Tidak Valid</span>
                </label>
              </div>
              {verifications[doc.name] === 'invalid' && (
                <textarea
                  className="textarea textarea-bordered mt-2 w-full"
                  placeholder="Catatan alasan dokumen tidak valid"
                  value={notes[doc.name] || ''}
                  onChange={(e) => handleNoteChange(doc.name, e.target.value)}
                />
              )}
            </div>
          )
        })}
      </div>
    </div>
  )
}
