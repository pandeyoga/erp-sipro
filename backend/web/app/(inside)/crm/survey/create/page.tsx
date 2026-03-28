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
      const formData = new FormData();
      formData.append('lead_id', data.lead_id);
      if(data.surveyDate) formData.append('survey_date', data.surveyDate);
      formData.append('survey_location_id', data.surveyLocation);
      if(data.actual_survey_date) formData.append('actual_survey_date', data.actual_survey_date);
      formData.append('unit_preference_id', data.unit_preference_id);
      formData.append('note', data.note);
      formData.append('pic', data.pic);
      if(data.survey_documentation){
        formData.append('survey_documentation', data.survey_documentation);
      }
      const res = await axios.post('/crm/survey', formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
  
      toast.success('Survey berhasil dibuat!');
      console.log('Response:', res.data);
      router.push('/crm/survey') // opsional: redirect ke halaman list leads
    } catch (err : any) {
      console.error('Error creating lead:', err.response);
      toast.error(err.response.data.message || "Gagal Membuat Lead");
    } finally {
      setSubmitLoading(false)
    }
  };
  
  

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Create Survey</h1>
      <LeadForm onSubmit={handleCreate} loading={submitLoading}/>
    </div>
  );
}
