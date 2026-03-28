'use client'
import axios from "@/lib/axios";
import DocumentForm from "../form";
import { useRouter } from "next/navigation";
import { useState } from "react";
import toast from "react-hot-toast";

export default function CreateDocumentPage() {
  const router = useRouter()
  const [submitLoading, setSubmitLoading] = useState(false)
  const handleCreate = async (data: any) => {
    try {
      setSubmitLoading(true)
      const { lead_id, document_date, property_id, document_fee, notes } = data;
      console.log({ lead_id, document_date, property_id, document_fee, notes });
      const res = await axios.post('/crm/document', { lead_id, document_date, property_id, document_fee, notes });
      
      setSubmitLoading(false)
  
      toast.success('Document berhasil dibuat!');
      console.log('Response:', res.data);
      router.push('/crm/document') // opsional: redirect ke halaman list leads
    } catch (err) {
      console.error('Error creating Document:', err);
      toast.error('Gagal membuat document');
    }
  };
  
  
  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Create Document</h1>
      <DocumentForm mode="create"/>
    </div>
  );
}
