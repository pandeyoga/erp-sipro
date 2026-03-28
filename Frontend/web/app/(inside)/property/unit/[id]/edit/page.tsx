'use client'


import { useParams, useRouter } from 'next/navigation'
import LegalitasAkhirForm from '../../form';

export default function EditKPRPage() {
  const { id } = useParams() as { id: string }
  

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Edit Unit</h1>
        
      <LegalitasAkhirForm
        id = {id}
        mode="edit"
      />
      
    </div>
  )
}
