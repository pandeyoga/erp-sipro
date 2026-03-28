'use client'

import axios from '@/lib/axios'
import { useEffect, useState } from 'react'
import { useParams, useRouter } from 'next/navigation'
import toast from "react-hot-toast";
import ReservationForm from '../../form';

export default function EditReservationPage() {
  const { id } = useParams() as { id: string }
  const router = useRouter()
  
  const [submitLoading, setSubmitLoading] = useState(false)

  const handleUpdate = async (data: any) => {
    try {
      setSubmitLoading(true)
      await axios.post(`/crm/reservation/${id}`, data,
        {
          headers: { 'Content-Type': 'multipart/form-data' },
        }
      )
      
      toast.success('Reservation berhasil diperbarui!')
      router.push('/crm/reservation') 
    } catch (err) {
      console.error('Error updating lead:', err)
      toast.error('Gagal memperbarui lead')
    } finally {
      setSubmitLoading(false)
    }
  }

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Edit Reservation</h1>
        
      <ReservationForm
        id = {id}
        onSubmit={handleUpdate}
        mode="edit"
        loading={submitLoading}
      />
      
    </div>
  )
}
