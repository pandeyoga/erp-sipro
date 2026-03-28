'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'
import axios from '@/lib/axios'
import toast from 'react-hot-toast'
import DataTable, { Column } from '@/components/datatable/datatable'
import ModalDeleteConfirm from '@/components/datatable/modal/ModalDelete'
import { isAllowed } from '@/lib/utils'
import { Pencil, Trash2 } from 'lucide-react'

interface SubContractor {
  id: string
  sub_contractor_name: string
  total_in_progress_constructions: number
  total_done_constructions: number
  on_time_constructions: number
  added_at: string
}

export default function SubContractorPage() {
  const router = useRouter()
  const [selected, setSelected] = useState<SubContractor | null>(null)

  const columns: Column<SubContractor>[] = [
    { key: 'sub_contractor_name', label: 'Name', sortable: true },
    { key: 'total_in_progress_constructions', label: 'In Progress', sortable: true },
    { key: 'total_done_constructions', label: 'Done', sortable: true },
    { key: 'on_time_constructions', label: 'On Time', sortable: true },
    { key: 'added_at', label: 'Added At', sortable: true }
  ]

  const openDeleteModal = (row: SubContractor) => {
    setSelected(row)
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.showModal()
  }

  const closeDeleteModal = () => {
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.close()
  }

  const handleDelete = async () => {
    if (!selected) return
    try {
      await axios.delete(`/property/sub-contractor/${selected.id}`)
      toast.success('Subkontraktor berhasil dihapus')
    } catch {
      toast.error('Gagal menghapus data')
    } finally {
      closeDeleteModal()
    }
  }

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Sub Contractors</h1>

      <DataTable
        endpoint="/property/sub-contractor"
        onClickCreate={isAllowed("property.create_sub_contractor") ? () => router.push('/property/sub-contractor/create') : undefined}
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            {isAllowed("property.update_sub_contractor") && (<button
              className="btn btn-sm btn-warning"
              onClick={() => router.push(`/property/sub-contractor/${row.id}/edit`)}
            >
              <Pencil size={16} className="mr-1" />
            </button>)}

            {isAllowed("property.delete_sub_contractor") && (<button className="btn btn-sm btn-error" onClick={() => openDeleteModal(row)}>
            <Trash2 size={16} className="mr-1" />
            </button>)}
          </div>
        )}
      />

      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        message={`Hapus subkontraktor "${selected?.sub_contractor_name}"?`}
      />
    </div>
  )
}
