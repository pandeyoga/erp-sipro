'use client'

// import axios from '@/lib/axios'
import axiosInstance  from '@/lib/axios'
import { useEffect, useState } from 'react'
import { useParams, useRouter } from 'next/navigation'
import LeadForm from '../../form'
import toast from "react-hot-toast";
import axios from 'axios';

export default function EditLeadPage() {
  const { id } = useParams() as { id: string }
  const router = useRouter()
  
  const [submitLoading, setSubmitLoading] = useState(false)


  const handleUpdate = async (data: any) => {
    try {
      setSubmitLoading(true)
      const formData = new FormData();
      if(data.lead_id) formData.append('lead_id', data.lead_id);
      if(data.status) formData.append('status', data.status);
      if(data.surveyDate) formData.append('survey_date', data.surveyDate);
      if(data.surveyLocation){
      formData.append('survey_location_id', data.surveyLocation);
      }
      if(data.actual_survey_date){
        formData.append('actual_survey_date', data.actual_survey_date);
      }
      if(data.unit_preference_id) formData.append('unit_preference_id', data.unit_preference_id);
      if(data.note) formData.append('note', data.note);
      if(data.pic) formData.append('pic', data.pic);
      if(data.survey_documentation){
        formData.append('survey_documentation', data.survey_documentation);
      }

      await axiosInstance.post(`/crm/survey/${id}`, formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });

      toast.success('Lead berhasil diperbarui!')
      router.push('/crm/survey') // opsional: redirect ke halaman list leads
    } catch (err) {
      console.error('Error updating lead:', err)
    } finally {
      setSubmitLoading(false)
    }
  }

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Edit Survey</h1>
        <LeadForm
          id={id}
          onSubmit={handleUpdate}
          mode="edit"
          loading={submitLoading}
        />
    </div>
  )
}
