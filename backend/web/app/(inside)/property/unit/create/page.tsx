'use client'
import axios from "@/lib/axios";
import KprForm from "../form"; // Pastikan kamu punya file ini
import { useRouter } from "next/navigation";
import { useState } from "react";
import toast from "react-hot-toast";
import UnitForm from "../form";

export default function CreateLegalitasAkhirPage() {
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
      <h1 className="text-xl font-bold text-gray-600 mb-6">Create Unit</h1>
      <UnitForm mode="create" />
    </div>
  )
}
