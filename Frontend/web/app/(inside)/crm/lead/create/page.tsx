'use client'
import axios from "@/lib/axios";
import LeadForm from "../form";
import { useRouter } from "next/navigation";
import { useState } from "react";
import toast from "react-hot-toast";

export default function CreateLeadPage() {
  const router = useRouter()
  const [submitLoading, setSubmitLoading] = useState(false)
  const handleCreate = async (data: any) => {
    try {
      setSubmitLoading(true)
      const res = await axios.post('/crm/lead', {
        contact_id: data.contact,
        marketing_id: data.marketingAgent, // atau sesuaikan jika multiple
        survey_date: data.surveyDate || null,
        survey_location_id: data.surveyLocation,
        notes: data.note || '',
        source: data.leadSource.toLowerCase(),
        pic : data.pic
      });
  
      toast.success('Lead berhasil dibuat!');
      console.log('Response:', res.data);
      router.push('/crm/lead') // opsional: redirect ke halaman list leads
    } catch (err : any) {
      console.error('Error creating lead:', err.response);
      toast.error(err.response.data.message || "Gagal Membuat Lead");
    } finally {
      setSubmitLoading(false)
    }
  };
  
  

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Create Lead</h1>
      <LeadForm onSubmit={handleCreate} loading={submitLoading}/>
    </div>
  );
}
