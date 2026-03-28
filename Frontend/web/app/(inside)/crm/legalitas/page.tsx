'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'
import axios from '@/lib/axios'
import toast from 'react-hot-toast'
import DataTable, { Column } from '@/components/datatable/datatable'
import ModalDeleteConfirm from '@/components/datatable/modal/ModalDelete'
import LegalitasSummary from './summary'
import { Pencil, Trash2 } from 'lucide-react'
import { isAllowed } from '@/lib/utils'
import FilterLead from './filter'

interface LegalitasAkhir {
  id: string
  name: string
  phone: string
  notes: string
  status: 'bast' | 'retention' | 'complete'
  property_unit: string
  duration: string // <- sesuai API response
}

const statusBadge: Record<string, string> = {
  bast: 'badge badge-info',
  retention: 'badge badge-warning',
  complete: 'badge badge-success',
}

export default function LegalitasAkhirPage() {
  const router = useRouter()
  const [selected, setSelected] = useState<LegalitasAkhir | null>(null)

  const columns: Column<LegalitasAkhir>[] = [
    { key: 'name', label: 'Name', sortable: true },
    { key: 'phone', label: 'Phone', sortable: true },
    { key: 'notes', label: 'Notes', sortable: true },
    {
      key: 'status',
      label: 'Legalitas Akhir Status',
      render: (row) => (
        <span className={statusBadge[row.status] || 'badge'}>{row.status}</span>
      ),
    },
    { key: 'property_unit', label: 'Property', sortable: true },
    { key: 'duration', label: 'DueDates', sortable: true },
  ]

  const openDeleteModal = (row: LegalitasAkhir) => {
    setSelected(row)
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.showModal()
  }

  const closeDeleteModal = () => {
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.close()
  }

  const handleDelete = async () => {
    if (!selected) return
    try {
      await axios.delete(`/crm/final-legality/${selected.id}`)
      toast.success('Data berhasil dihapus')
    } catch {
      toast.error('Gagal menghapus data')
    } finally {
      closeDeleteModal()
    }
  }

  const [filter, setFilter] = useState({});

  const handleFilter = async (status : string) => {
    const filters = { status }

    setFilter(
      Object.entries(filters).reduce((acc, [key, value]) => {
        if (value) (acc as any)[key] = value
        return acc
      }, {} as typeof filters)
    )
  };

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Legalitas Akhir</h1>

      {/* Summary (dummy, sesuaikan nanti dengan data summary dari endpoint) */}
      <LegalitasSummary/>

      <FilterLead handleFilter={handleFilter}/>

      {/* Data Table */}
      <DataTable
        endpoint="/crm/final-legality"
        onClickCreate={isAllowed('lead.create_final_legality') ? () => router.push('/crm/legalitas/create') : undefined }
        columns={columns}
        filter={filter}
        actions={(row) => (
          <div className="flex gap-2">
            {isAllowed('lead.update_final_legality') &&(<button
              className="btn btn-sm btn-warning"
              onClick={() => router.push(`/crm/legalitas/${row.id}/edit`)}
            >
              <Pencil size={16} className="mr-1" />
            </button>)}

            {isAllowed('lead.delete_final_legality') && (<button
              className="btn btn-sm btn-error text-white"
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
        message={`Hapus data untuk "${selected?.name}"?`}
      />
    </div>
  )
}
