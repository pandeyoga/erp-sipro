'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'
import axios from '@/lib/axios'
import toast from 'react-hot-toast'
import DataTable, { Column } from '@/components/datatable/datatable'
import ModalDeleteConfirm from '@/components/datatable/modal/ModalDelete'
import { isAllowed } from '@/lib/utils'
import { Pencil, Trash2 } from 'lucide-react'

interface Unit {
  id: string
  type: string
  building_area: string
  land_area: string
  notes?: string
}

export default function UnitPage() {
  const router = useRouter()
  const [selected, setSelected] = useState<Unit | null>(null)

  const columns: Column<Unit>[] = [
    { key: 'type', label: 'Tipe Unit', sortable: true },
    { key: 'building_area', label: 'L. Bangunan (m²)', sortable: true },
    { key: 'land_area', label: 'L. Tanah (m²)', sortable: true },
    { key: 'notes', label: 'Catatan' },
  ]

  const openDeleteModal = (row: Unit) => {
    setSelected(row)
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.showModal()
  }

  const closeDeleteModal = () => {
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.close()
  }

  const handleDelete = async () => {
    if (!selected) return
    try {
      await axios.delete(`/property/unit/${selected.id}`)
      toast.success('Unit berhasil dihapus')
    } catch {
      toast.error('Gagal menghapus unit')
    } finally {
      closeDeleteModal()
    }
  }

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Manajemen Unit</h1>

      <DataTable
        endpoint="/property/unit"
        onClickCreate={isAllowed("property.create_unit") ? () => router.push('/property/unit/create') : undefined}
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            {isAllowed("property.update_unit") && (<button
              className="btn btn-sm btn-warning"
              onClick={() => router.push(`/property/unit/${row.id}/edit`)}
            >
              <Pencil size={16} className="mr-1" />
            </button>)}
            {isAllowed("property.delete_unit") && (<button
              className="btn btn-sm btn-error"
              onClick={() => openDeleteModal(row)}
            >
              <Trash2 size={16} className="mr-1" />
            </button>)}
          </div>
        )}
      />

      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        message={`Hapus unit "${selected?.type}"?`}
      />
    </div>
  )
}
