'use client'


import { useParams, useRouter } from 'next/navigation'
import ConstractionForm from '../../form';

export default function EditConstractionPage() {
  const { id } = useParams() as { id: string }

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Edit Sub Constractor</h1>
        
      <ConstractionForm
        id = {id}
        mode="edit"
      />
      
    </div>
  )
}
