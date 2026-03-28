'use client'

import { useForm } from 'react-hook-form'
import { useRouter } from 'next/navigation'
import axios from '@/lib/axios'
import toast from 'react-hot-toast'
import { useEffect, useState } from 'react'

interface Props {
  mode: 'create' | 'edit'
  id?: string
}

type FormValues = {
  name: string
  added_at: string
}

export default function SubContractorForm({ mode, id }: Props) {
  const router = useRouter()
  const { register, handleSubmit, setValue, reset } = useForm<FormValues>()
  const [loading, setLoading] = useState(false)

  // Ambil data jika edit
  useEffect(() => {
    if (mode === 'edit' && id) {
      axios
        .get(`/property/sub-contractor/${id}`)
        .then((res) => {
          const data = res.data.data
          reset({
            name: data.sub_contractor_name,
            added_at: data.added_at?.split(' ')[0] // remove time if exists
          })
        })
        .catch(() => toast.error('Gagal mengambil data'))
    }
  }, [id, mode, reset])

  const onSubmit = async (data: FormValues) => {
    try {
      setLoading(true)

      if (mode === 'edit' && id) {
        await axios.put(`/property/sub-contractor/${id}`, data)
        toast.success('Subkontraktor berhasil diperbarui')
      } else {
        await axios.post('/property/sub-contractor', data)
        toast.success('Subkontraktor berhasil ditambahkan')
      }

      router.push('/property/sub-contractor')
    } catch (err: any) {
      toast.error('Gagal menyimpan data')
    } finally {
      setLoading(false)
    }
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      <fieldset className="border border-base-300 rounded-xl p-6 space-y-4">
        <legend className="text-lg font-semibold px-2">
          {mode === 'edit' ? 'Edit Sub Contractor' : 'Tambah Sub Contractor'}
        </legend>

        <div className="form-control">
          <label className="label">
            <span className="label-text">Nama Subkontraktor</span>
          </label>
          <input
            type="text"
            {...register('name', { required: true })}
            className="input input-bordered w-full"
            placeholder="Contoh: CV Bangun Jaya"
          />
        </div>

        <div className="form-control">
          <label className="label">
            <span className="label-text">Tanggal Ditambahkan</span>
          </label>
          <input
            type="date"
            {...register('added_at', { required: true })}
            className="input input-bordered w-full"
          />
        </div>
      </fieldset>

      <div className="flex justify-between">
        <button type="button" onClick={() => router.back()} className="btn">
          Kembali
        </button>
        <button type="submit" className="btn btn-primary" disabled={loading}>
          {loading && <span className="loading loading-spinner"></span>}
          {mode === 'edit' ? 'Update' : 'Simpan'}
        </button>
      </div>
    </form>
  )
}
