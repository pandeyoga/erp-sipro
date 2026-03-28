'use client'

import { useRef, useState } from 'react'
import { useRouter } from 'next/navigation'
import axios from '@/lib/axios'
import toast from 'react-hot-toast'
import DataTable, { Column } from '@/components/datatable/datatable'
import ModalDeleteConfirm from '@/components/datatable/modal/ModalDelete'
import SubmissionForm from './form'
import { Pencil, Trash2 } from 'lucide-react'
import { convertToRupiah } from '@/lib/utils'

interface Submission {
  id: string
  category: string
  sub_category: number
  total_amount: number
  paid_amount: number
  remaining_amount: number
  description: number
  type: string
  created_at: string
  status: string
  notes: string
}

export default function SubmissionPage() {
  const router = useRouter()
  const [selected, setSelected] = useState<Submission | null>(null)

  const columns: Column<Submission>[] = [
    { key: 'category', label: 'Category', sortable: true },
    { key: 'sub_category', label: 'Sub Category', sortable: true },
    { key: 'total_amount', label: 'Total Amount', sortable: true, render : (item)=>{
      const formatted = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0, // hilangkan koma jika tidak mau desimal
      }).format(item.total_amount);
      return (<div>{formatted}</div>)
    } },
    // { key: 'paid_amount', label: 'Paid Amount', sortable: true, render : (item)=>{
    //       const formatted = convertToRupiah(item.paid_amount);
    //       return (<div>{formatted}</div>)
    //     } },
    //   { key: 'remaining_amount', label: 'Remaining Amount', sortable: true, render : (item)=>{
    //     const formatted = convertToRupiah(item.total_amount - item.paid_amount);
    //     return (<div>{formatted}</div>)
    //   } },
    { key: 'description', label: 'Description', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'created_at', label: 'Date', sortable: true },
    { key: 'notes', label: 'Notes', sortable: true }
  ]

  const openDeleteModal = (row: Submission) => {
    setSelected(row)
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.showModal()
  }

  const closeDeleteModal = () => {
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.close()
  }

  const handleDelete = async () => {
    if (!selected) return
    try {
      await axios.delete(`/finance/submission/${selected.id}`)
      toast.success('Submission raktor berhasil dihapus')
    } catch {
      toast.error('Gagal menghapus data')
    } finally {
      closeDeleteModal()
      handleReload()
    }
  }
  const handleReload = async () => {
      TableRef?.current?.reload()
    };
  
  const TableRef = useRef<{ reload: () => void }>(null);

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Submission</h1>

      <SubmissionForm mode={'create'} handleReload={handleReload}/>

      <DataTable
        ref={TableRef}
        endpoint="/finance/submission"
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            <button
              className="btn btn-sm btn-warning"
              onClick={() => router.push(`/finance/submission/${row.id}/edit`)}
            >
              <Pencil size={16} className="mr-1" />
            </button>

            <button
              className="btn btn-sm btn-error text-white"
              onClick={() => openDeleteModal(row)}
            >
              <Trash2 size={16} className="mr-1" />
            </button>
          </div>
        )}
      />

      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        message={`Hapus subkontraktor "${selected?.category}"?`}
      />
    </div>
  )
}
