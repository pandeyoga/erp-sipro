'use client'

import { useForm } from 'react-hook-form'
import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import axios from '@/lib/axios'
import toast from 'react-hot-toast'

interface UnitFormProps {
  mode: 'create' | 'edit'
  id?: string
}

export default function UnitForm({ mode, id }: UnitFormProps) {
  const router = useRouter()
  const { register, handleSubmit, reset, setValue } = useForm()
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    if (mode === 'edit' && id) {
      const fetchData = async () => {
        try {
          const res = await axios.get(`/property/unit/${id}`)
          const data = res.data.data
          reset({
            type: data.type,
            building_area: data.building_area,
            land_area: data.land_area,
            notes: data.notes,
          })
        } catch (error) {
          toast.error('Gagal memuat data unit')
        }
      }
      fetchData()
    }
  }, [mode, id, reset])

  const onSubmit = async (data: any) => {
    setLoading(true)

    try {
      if (mode === 'edit' && id) {
        await axios.put(`/property/unit/${id}`, {
          type: data.type,
          building_area: data.building_area,
          land_area: data.land_area,
          notes: data.notes,
        })
        toast.success('Unit berhasil diperbarui')
      } else {
        const formData = new FormData()
        formData.append('type', data.type)
        formData.append('building_area', data.building_area)
        formData.append('land_area', data.land_area)
        if (data.notes) formData.append('notes', data.notes)

        await axios.post('/property/unit', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        })
        toast.success('Unit berhasil ditambahkan')
      }

      router.push('/property/unit')
    } catch (error) {
      toast.error('Gagal menyimpan unit')
    } finally {
      setLoading(false)
    }
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      <div className="form-control">
        <label className="label">
          <span className="label-text">Tipe Unit</span>
        </label>
        <input
          type="text"
          {...register('type')}
          className="input input-bordered w-full"
          required
        />
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="form-control">
          <label className="label">
            <span className="label-text">Luas Bangunan (m²)</span>
          </label>
          <input
            type="number"
            step="0.01"
            {...register('building_area')}
            className="input input-bordered w-full"
            required
          />
        </div>

        <div className="form-control">
          <label className="label">
            <span className="label-text">Luas Tanah (m²)</span>
          </label>
          <input
            type="number"
            step="0.01"
            {...register('land_area')}
            className="input input-bordered w-full"
            required
          />
        </div>
      </div>

      <div className="form-control">
        <label className="label">
          <span className="label-text">Catatan (Opsional)</span>
        </label>
        <textarea
          {...register('notes')}
          className="textarea textarea-bordered w-full"
          placeholder="Tambahkan catatan tambahan (jika ada)"
        />
      </div>

      <div className="flex justify-between">
        <button type="button" onClick={() => router.back()} className="btn">
          Kembali
        </button>
        <button type="submit" className="btn btn-primary" disabled={loading}>
          {loading && <span className="loading loading-spinner"></span>}
          {mode === 'edit' ? 'Update' : 'Tambah'}
        </button>
      </div>
    </form>
  )
}
