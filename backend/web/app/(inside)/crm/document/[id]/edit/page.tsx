'use client'

import axios from '@/lib/axios'
import { useEffect, useState } from 'react'
import { useParams, useRouter } from 'next/navigation'
import toast from "react-hot-toast";
import DocumentForm from '../../form';

export default function EditDocumentPage() {
  const { id } = useParams() as { id: string }
  

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Edit Document</h1>
        
      <DocumentForm
        id = {id}
        mode="edit"
      />
      
    </div>
  )
}
