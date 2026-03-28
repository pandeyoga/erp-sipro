'use client'

import { useRef, useState } from 'react'
import { useRouter } from 'next/navigation'
import axios from '@/lib/axios'
import toast from 'react-hot-toast'
import DataTable, { Column } from '@/components/datatable/datatable'
import ModalDeleteConfirm from '@/components/datatable/modal/ModalDelete'
import CashInForm from './form'
import { convertToRupiah } from '@/lib/utils'
import { Pencil, Trash2 } from 'lucide-react'
import FilterLead from './filter'
import axiosInstance from '@/lib/axios'
import { useForm } from 'react-hook-form'

interface CashIn {
  id: string
  category: string
  sub_category: number
  total_amount: number
  paid_amount: number
  unpaid_amount: number
  remaining_amount: number
  description: number
  type: string
  created_at: string
  status: string
  notes: string
}

export default function CashInPage() {
  const router = useRouter()
  const [selected, setSelected] = useState<CashIn | null>(null)
  const [filter, setFilter] = useState({});

  const columns: Column<CashIn>[] = [
    { key: 'category', label: 'Category', sortable: true },
    { key: 'sub_category', label: 'Sub Category', sortable: true },
    { key: 'total_amount', label: 'Total Amount', sortable: true, render : (item)=>{
      const formatted = convertToRupiah(item.total_amount);
      return (<div>{formatted}</div>)
    } },
    { key: 'paid_amount', label: 'Paid Amount', sortable: true, render : (item)=>{
      const formatted = convertToRupiah(item.paid_amount);
      return (<div>{formatted}</div>)
    } },
    { key: 'unpaid_amount', label: 'Unpaid Amount', sortable: true, render : (item)=>{
      const formatted = convertToRupiah(item.total_amount - item.paid_amount);
      return (<div>{formatted}</div>)
    } },
    { key: 'description', label: 'Description', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    {
      key: "created_at",
      label: "Date",
      sortable: true,
      render: (item) => new Date(item.created_at).toLocaleDateString(),
    },
    { key: 'notes', label: 'Notes', sortable: true }
  ]

  const openDeleteModal = (row: CashIn) => {
    setSelected(row)
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.showModal()
  }

  const closeDeleteModal = () => {
    ;(document.getElementById('delete_modal') as HTMLDialogElement)?.close()
  }

  const handleDelete = async () => {
    if (!selected) return
    try {
      await axios.delete(`/finance/cash-in/${selected.id}`)
      toast.success('Cash In raktor berhasil dihapus')
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

  const handleFilter = async (status : string, category_id : string, sub_category_id : string) => {
    const filters = { status, category_id,sub_category_id }

    setFilter(
      Object.entries(filters).reduce((acc, [key, value]) => {
        if (value) (acc as any)[key] = value
        return acc
      }, {} as typeof filters)
    )
  };
  
  const TableRef = useRef<{ reload: () => void }>(null);

  const {
    register,
    watch,
    formState: { errors },
  } = useForm<{
    start_date : string;
    end_date : string;
  }>({
    defaultValues : {
      start_date: new Date().toISOString().split("T")[0], // "2025-09-16"
      end_date: new Date().toISOString().split("T")[0]
    }
  });

  const handleExport = async () => {
    try {
      const res = await axiosInstance.get(
        `/finance/cash-in/export`,
        {
          params: {
            startDate: watch("start_date"),
            endDate: watch("end_date"),
          },
          responseType: "blob", // supaya bisa download file
        }
      );
  
      // buat blob url
      const url = window.URL.createObjectURL(new Blob([res.data]));
      const link = document.createElement("a");
      link.href = url;
  
      // kasih nama file sesuai kebutuhan
      link.setAttribute("download", "cash_out_export.xlsx");
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (error) {
      console.error("Gagal export:", error);
    }
  };

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Cash In</h1>

      <CashInForm mode={'create'} handleReload={handleReload}/>

      <FilterLead handleFilter={handleFilter}/>

      <form className="flex items-end gap-4 w-1/2 mb-6">
        <div className="form-control w-full">
          <label className="label font-medium">From</label>
          <input
            type="date"
            {...register("start_date")}
            className="input input-bordered w-full"
            required
          />
        </div>

        <div className="form-control w-full">
          <label className="label font-medium">To</label>
          <input
            type="date"
            {...register("end_date")}
            className="input input-bordered w-full"
            required
          />
        </div>

        <button
          type="button"
          onClick={handleExport}
          className="btn btn-warning"
        >
          Export
        </button>
      </form>

      <DataTable
        ref={TableRef}
        endpoint="/finance/cash-in"
        columns={columns}
        filter={filter}
        actions={(row) => (
          <div className="flex gap-2">
            <button
              className="btn btn-sm btn-warning"
              onClick={() => router.push(`/finance/cash-in/${row.id}/edit`)}
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
