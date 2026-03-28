'use client'
import axios from "@/lib/axios";
import ReservationForm from "../form";
import { useRouter } from "next/navigation";
import { useState } from "react";
import toast from "react-hot-toast";

export default function CreateReservationPage() {
  const router = useRouter()
  const [submitLoading, setSubmitLoading] = useState(false)
  const handleCreate = async (data: any) => {
    try {
      setSubmitLoading(true)
      const res = await axios.post('/crm/reservation', data);
      toast.success('Reservation berhasil dibuat!');
      router.push('/crm/reservation') // opsional: redirect ke halaman list leads
    } catch (err) {
      console.error('Error creating Reservation:', err);
      toast.error('Gagal membuat reservation');
    } finally {
      setSubmitLoading(false)
    }
  };
  
  
  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Create Reservation</h1>
      <ReservationForm onSubmit={handleCreate} loading={submitLoading}/>
    </div>
  );
}
