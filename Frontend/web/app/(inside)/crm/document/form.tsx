'use client'

import { useEffect, useState } from 'react'
import { useForm } from 'react-hook-form'
import axios from '@/lib/axios'
import toast from 'react-hot-toast'
import FileDropzone from '@/components/dropzone'
import Select from "react-select";
import { useRouter } from 'next/navigation'
import { Accept } from 'react-dropzone'

interface LeadDocumentFormProps {
  id?: string
  mode?: 'create' | 'edit'
}

interface LeadDocument {
  [key: string]: any
}
interface ReservedLead {
    id: string
    name: string
    phone: string
    email: string
    survey_date: string
    marketing_agent: string
  }

  const documentFields: {
    name: string;
    label: string;
    accept: Accept;
  }[] = [
    {
      name: 'doc_ktp_applicant',
      label: 'KTP Pemohon',
      accept: {
        'application/pdf': ['.pdf'],
        'image/*': ['.jpg', '.jpeg', '.png'],
      },
    },
    {
      name: 'doc_ktp_partner',
      label: 'KTP Pasangan',
      accept: {
        'application/pdf': ['.pdf'],
        'image/*': ['.jpg', '.jpeg', '.png'],
      },
    },
    {
      name: 'doc_npwp',
      label: 'NPWP',
      accept: {
        'application/pdf': ['.pdf'],
        'image/*': ['.jpg', '.jpeg', '.png'],
      },
    },
    {
      name: 'doc_kk',
      label: 'Kartu Keluarga',
      accept: {
        'application/pdf': ['.pdf'],
        'image/*': ['.jpg', '.jpeg', '.png'],
      },
    },
    {
      name: 'doc_marriage_or_divorce_certificate',
      label: 'Surat Nikah / Cerai',
      accept: {
        'application/pdf': ['.pdf'],
        'image/*': ['.jpg', '.jpeg', '.png'],
      },
    },
    {
      name: 'doc_applicant_photo',
      label: 'Foto Pemohon',
      accept: {
        'image/*': ['.jpg', '.jpeg', '.png'],
      },
    },
    {
      name: 'doc_partner_photo',
      label: 'Foto Pasangan',
      accept: {
        'image/*': ['.jpg', '.jpeg', '.png'],
      },
    },
    {
      name: 'doc_house_ownership_certificate',
      label: 'Surat Tidak Memiliki Rumah',
      accept: {
        'application/pdf': ['.pdf'],
        'image/*': ['.jpg', '.jpeg', '.png'],
      },
    },
    {
      name: 'doc_domisili_certificate',
      label: 'Surat Domisili',
      accept: {
        'application/pdf': ['.pdf'],
        'image/*': ['.jpg', '.jpeg', '.png'],
      },
    },
  ];
  
const documentPekerja = [
  {
    key: 'pekerja_materai_60_lembar',
    label: 'Materai 60 Lembar',
  },
  {
    key: 'pekerja_no_telp_dan_nama_atasan',
    label: 'No Telpon & Nama Atasan',
  },
  {
    key: 'pekerja_slip_gaji_3_bulan',
    label: 'Slip Gaji 3 Bulan Terakhir',
  },
  {
    key: 'pekerja_rekening_koran_3_bulan',
    label: 'Rekening Koran 3 Bulan Terakhir',
  },
  {
    key: 'pekerja_foto_tempat_kerja_dan_serlok',
    label: 'Foto Tempat Kerja dan Share Lokasi',
  },
  {
    key: 'pekerja_formulir_bank_dan_flpp',
    label: 'Formulir Bank Dan Flpp',
  },
];

const documentWirausaha = [
  {
    key: 'wirausaha_materai_60_lembar',
    label: 'Materai 60 Lembar',
  },
  {
    key: 'wirausaha_sk_usaha_atau_nomor_usaha',
    label: 'SK Usaha/Nomor Induk Usaha',
  },
  {
    key: 'wirausaha_neraca_penghasilan_6_bulan',
    label: 'Neraca Penghasilan 6 Bulan Terakhir',
  },
  {
    key: 'wirausaha_foto_tempat_usaha',
    label: 'Foto Tempat Usaha',
  },
  {
    key: 'wirausaha_rekening_koran_6_bulan',
    label: 'Rekening Koran 6 Bulan Terakhir',
  },
  {
    key: 'wirausaha_foto_tempat_usaha_dan_serlok',
    label: 'Foto Tempat Usaha dan Share Lokasi',
  },
  {
    key: 'wirausaha_formulir_bank_dan_flpp',
    label: 'Formulir Bank Dan Flpp',
  },
];


const bankOptions = [
  'Bank 1', 'Bank 2', 'Bank 3', 'Bank 4',
  'Bank 5', 'Bank 6', 'Bank 7', 'Bank 8'
]


export default function LeadDocumentForm({ id, mode = 'create' }: LeadDocumentFormProps) {
  const [loading, setLoading] = useState<boolean>(true)
  const { register, handleSubmit, setValue,watch, reset,getValues, formState: { errors } } = useForm<LeadDocument>()
  const [reservedLeads, setReservedLeads] = useState<ReservedLead[]>([])

  const [searchTermLead, setSearchTermLead] = useState("");
 
  useEffect(() => {
    if (!searchTermLead) return;
  
    const delay = setTimeout(() => {
      axios
        .get(`/crm/lead-document/get-reserved-lead?search=${searchTermLead}`)
        .then((res) => {
          const options = res.data.data.map((p: any) => ({label : p.name, value: p.id, ...p}))
          setReservedLeads(options)
        })
        .catch(() => console.error("Gagal ambil data"));
    }, 400);
  
    return () => clearTimeout(delay);
  }, [searchTermLead]);

  useEffect(() => {
    const fetchData = async () => {
      if (mode === 'edit' && id) {
        try {
          const res = await axios.get(`/crm/lead-document/${id}`)
          const data = res.data.data
          const values = {
            ...data,
            ...data.documents.reduce((acc: any, curr: any) => {
              acc[curr.type] = curr.file_url
              return acc
            }, {}),
            ...data.checklist.reduce((acc: any, curr: any) => {
              acc[curr.key] = curr.checked
              return acc
            }, {}),
          }
          reset(values)
          setValue('email',data.email)
          setValue('phone',data.phone)
        } catch (err) {
          toast.error('Gagal memuat data dokumen')
        }
      }

      const resLeads = await axios.get('/crm/lead-document/get-reserved-lead?search=')
      setReservedLeads(resLeads.data.data?.map((v: any)=>({label : v.name, value : v.id, ...v})) || [])

      setLoading(false)
    }
    fetchData()
  }, [id, mode, reset])

  const router = useRouter()
  const [loadingSubmit, setLoadingSubmit] = useState<boolean>(false)

  const onSubmit = async (data: LeadDocument) => {
    const formData = new FormData()
    for (const key in data) {
      const value = data[key]
      if (typeof value === 'boolean') {
        formData.append(key, value ? '1' : '0');
      } else {
        formData.append(key, value !== undefined && value !== null ? value : '-');
      }      
    }
    formData.append('notes', data['notes'] ?? '-');
    try {
      setLoadingSubmit(true)
      const endpoint = mode === 'edit' ? `/crm/lead-document/${id}` : '/crm/lead-document'
      const res = await axios.post(endpoint, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      toast.success(res.data.message)
      router.push('/crm/document')
    } catch (err) {
      console.log(err)
      toast.error('Gagal menyimpan dokumen')
    }finally{
      setLoadingSubmit(false)
    }
  }

  // if (loading) return <PageLoading />

  useEffect(()=>{
      if(mode === 'create' ){
        const selectedLead = reservedLeads.find((lead) => lead.id === watch('lead_id'));
        const email = selectedLead?.email;
        const phone = selectedLead?.phone;
        setValue('email',email)
        setValue('phone',phone)
      }
  },[watch('lead_id')])

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Lead Information</legend>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="form-control mt-4">
            <label className="label"><span className="label-text">Lead</span></label>
            {
              mode === 'edit' ? 
              (
                <input {...register('name')} readOnly className="input input-bordered w-full" />
              ) :
              (
                <Select
                options={reservedLeads}
                placeholder="Select or search Reservation Lead"
                onChange={(value : any) => {
                  setValue("lead_id", value.value);
                }}
                onInputChange={(value, { action }) => {
                  if (action === "input-change") {
                    setSearchTermLead(value); 
                  }
                }}
                className="w-full"
                styles={{
                  control: (base) => ({
                    ...base,
                    borderColor: "#d1d5db", // tailwind: border-gray-300
                    borderRadius: "0.5rem",
                    padding: "2px",
                  }),
                }}
              />
              )
            }
          </div>
          <div className="form-control mt-4">
            <label className="label"><span className="label-text">No. Telepon </span></label>
            <input {...register('phone')} placeholder="No. Telepon" className="input input-bordered w-full" readOnly />
          </div>
          <div className="form-control mt-4">
            <label className="label"><span className="label-text">Email </span></label>
            <input {...register('email')} placeholder="Email" className="input input-bordered w-full" readOnly/>
          </div>
        </div>
      </fieldset>

      {/* {JSON.stringify(getValues())} */}

      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Dokumen Konsumen</legend>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {
            documentFields.map((doc) => {
              const documents = watch('documents') ?? [];
              const current_doc = documents.find((obj: any) => `doc_${obj.type}` === doc.name)
              const file_url = current_doc?.file_url
              
              return (
                <FileDropzone key={doc.name} name={doc.name} label={doc.label} setValue={setValue} initialPreviewSrc={file_url} accept={doc.accept}/>
              )
            })
          }
        </div>
      </fieldset>
      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Cash</legend>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          
            <label className="flex items-center gap-2">
              <input type="checkbox" {...register(`check_cash`)} className="checkbox checkbox-sm" />
              <span>Cash</span>
            </label>
        </div>
      </fieldset>

      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Document Konsumen (Karyawan)</legend>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          
          {documentPekerja.map((item) => (
            <label key={item.key} className="flex items-center gap-2">
              <input type="checkbox" {...register(`${item.key}`)} className="checkbox checkbox-sm" />
              <span>{item.label}</span>
            </label>
          ))}
        </div>
      </fieldset>


      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Document Konsumen (Wirausaha)</legend>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {documentWirausaha.map((item) => (
            <label key={item.key} className="flex items-center gap-2">
              <input type="checkbox" {...register(`${item.key}`)} className="checkbox checkbox-sm" />
              <span>{item.label}</span>
            </label>
          ))}
        </div>
      </fieldset>


      <SprBankDoc setValue={setValue} watch={watch}/>


      {/* <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Bank KPR</legend>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {bankOptions.map((bank) => (
            <label key={bank} className="flex items-center gap-2">
              <input type="checkbox" {...register(`banks.${bank}`)} className="checkbox checkbox-sm" />
              <span>{bank}</span>
            </label>
          ))}
        </div>
      </fieldset> */}



      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Catatan</legend>
        <textarea {...register('notes')} placeholder="Catatan Tambahan" className="textarea textarea-bordered w-full" />
      </fieldset>

      <div className="flex justify-end">
        <button type="submit" className="btn btn-primary" disabled={loadingSubmit} >
        {loadingSubmit && <span className="loading loading-spinner"></span>}
          {mode === 'edit' ? 'Update Document' : 'Create Document'}
        </button>
      </div>
    </form>
  )
}

function SprBankDoc({ setValue, watch } : any){
  const documents = watch('documents') ?? [];
  const current_doc = documents.find((obj: any) => `doc_${obj.type}` === 'doc_spr_bank')
  const file_url = current_doc?.file_url
  return (
    <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Dokumen Developer (SPR Bank) </legend>
        <FileDropzone name="doc_spr_bank" label="SPR Bank" setValue={setValue} initialPreviewSrc={file_url}  />
      </fieldset>
  )
}
