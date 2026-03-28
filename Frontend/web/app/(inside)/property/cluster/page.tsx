'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'
import axios from '@/lib/axios'
import toast from 'react-hot-toast'
import DataTable, { Column } from '@/components/datatable/datatable'
import ModalDeleteConfirm from '@/components/datatable/modal/ModalDelete'
import { isAllowed } from '@/lib/utils'
import { Pencil, Trash2 } from 'lucide-react'

export interface Cluster {
  id: string
  name: string
  project_name: string
  block_code: string
}

export default function ClusterPage() {
  const router = useRouter()
  const [selected, setSelected] = useState<Cluster | null>(null)

  const columns: Column<Cluster>[] = [
    { key: 'name', label: 'Name', sortable: true },
    { key: 'project_name', label: 'Project', sortable: true },
    { key: 'block_code', label: 'Block Code', sortable: true }
  ]

  const openDeleteModal = (row: Cluster) => {
    setSelected(row)
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.showModal()
  }

  const closeDeleteModal = () => {
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.close()
  }

  const handleDelete = async () => {
    if (!selected) return
    try {
      await axios.delete(`/property/cluster/${selected.id}`)
      toast.success('Cluster berhasil dihapus')
    } catch {
      toast.error('Gagal menghapus cluster')
    } finally {
      closeDeleteModal()
    }
  }

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Cluster</h1>


      {/* Data Table */}
      <DataTable
        endpoint="/property/cluster"
        onClickCreate={
          isAllowed("property.create_cluster")
          ?() => router.push('/property/cluster/create')
          : undefined}
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            {isAllowed("property.update_cluster") && (<button
              className="btn btn-sm btn-warning"
              onClick={() => router.push(`/cluster/${row.id}/edit`)}
            >
              <Pencil size={16} className="mr-1" />
            </button>)}
            {isAllowed("property.delete_cluster") && (<button className="btn btn-sm btn-error" onClick={() => openDeleteModal(row)}>
              <Trash2 size={16} className="mr-1" />
            </button>)}
          </div>
        )}
      />

      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        message={`Hapus cluster "${selected?.name}"?`}
      />
    </div>
  )
}
