'use client'
import axios from "@/lib/axios";
import PaymentForm from "../form"; // Pastikan kamu punya file ini
import { useRouter } from "next/navigation";
import { useState } from "react";
import toast from "react-hot-toast";
import KprFormEdit from "../form-edit";

export default function CreateKprPage() {
  const router = useRouter()
  const [submitLoading, setSubmitLoading] = useState(false)

  const handleCreate = async (data: any) => {
    try {
      setSubmitLoading(true)

      const { name, phone, notes, property_unit, status, duration } = data
      console.log({ name, phone, notes, property_unit, status, duration })

      const res = await axios.post('/crm/kpr', {
        name,
        phone,
        notes,
        property_unit,
        status,
        duration,
      })

      setSubmitLoading(false)
      toast.success('KPR berhasil dibuat!')
      router.push('/crm/kpr')
    } catch (err) {
      console.error('Error creating KPR:', err)
      toast.error('Gagal membuat KPR')
    }
  }

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Create KPR</h1>
      <PaymentForm mode="create"  />
      {/* <KprFormEdit
              id = {'1'}
            /> */}
    </div>
  )
}
