'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'
import axios from '@/lib/axios'
import toast from 'react-hot-toast'
import DataTable, { Column } from '@/components/datatable/datatable'
import ModalDeleteConfirm from '@/components/datatable/modal/ModalDelete'
import ConstructionSummary from './summary' // Ganti dari LegalitasSummary
import { Pencil, Trash2 } from 'lucide-react'
import { isAllowed } from '@/lib/utils'
import FilterLead from './filter'

interface Construction {
  id: string
  lead_name: string
  project_name: string
  cluster_name: string
  unit_type: string
  status: string
  start_date: string
  estimated_end_date: string
  duration: number
  sub_contractor_name: string
  unit_number : string;
}

export default function ConstructionPage() {
  const router = useRouter()
  const [selected, setSelected] = useState<Construction | null>(null)

  const columns: Column<Construction>[] = [
    { key: 'lead_name', label: 'Consument', sortable: true },
    { key: 'project_name', label: 'Project', sortable: true },
    { key: 'cluster_name', label: 'Cluster', sortable: true },
    { key: 'unit_type', label: 'Unit Type', sortable: true },
    { key: 'unit_number', label: 'Unit Number', sortable: true },
    
    { key: 'status', label: 'Construction Status', sortable: true },
    { key: 'start_date', label: 'Construction Start', sortable: true },
    { key: 'estimated_end_date', label: 'Est. Finished', sortable: true },
    { key: 'duration', label: 'Duration', sortable: true },
    { key: 'sub_contractor_name', label: 'Subcon', sortable: true }
  ]

  const openDeleteModal = (row: Construction) => {
    setSelected(row)
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.showModal()
  }

  const closeDeleteModal = () => {
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.close()
  }

  const handleDelete = async () => {
    if (!selected) return
    try {
      await axios.delete(`/property/construction/${selected.id}`)
      toast.success('Data berhasil dihapus')
    } catch {
      toast.error('Gagal menghapus data')
    } finally {
      closeDeleteModal()
    }
  }

  const [filter, setFilter] = useState({});

  const handleFilter = async (status : string,project_id : string, cluster_id : string ) => {
    const filters = { status, project_id, cluster_id }

    setFilter(
      Object.entries(filters).reduce((acc, [key, value]) => {
        if (value) (acc as any)[key] = value
        return acc
      }, {} as typeof filters)
    )
  };

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Construction Progress</h1>

      {/* Summary */}
      <ConstructionSummary />

      <FilterLead handleFilter={handleFilter}/>

      {/* Data Table */}
      <DataTable
        endpoint="/property/construction"
        onClickCreate={isAllowed("property.create_construction") ? () => router.push('/property/construction/create') : undefined}
        columns={columns}
        filter={filter}
        actions={(row) => (
          <div className="flex gap-2">
            {isAllowed("property.update_construction") && (<button
              className="btn btn-sm btn-warning"
              onClick={() => router.push(`/property/construction/${row.id}/edit`)}
            >
              <Pencil size={16} className="mr-1" />
            </button>)}

            {isAllowed("property.delete_construction") && (<button className="btn btn-sm btn-error" onClick={() => openDeleteModal(row)}>
              <Trash2 size={16} className="mr-1" />
            </button>)}
          </div>
        )}
      />

      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        message={`Hapus data untuk "${selected?.lead_name}"?`}
      />
    </div>
  )
}
