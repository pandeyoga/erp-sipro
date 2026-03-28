"use client";

import { useParams } from "next/navigation";
import CashOutForm from "../../form";
import DataTable, { Column } from "@/components/datatable/datatable";
import { useRef, useState } from "react";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";

interface CashOut {
  transaction_id: string;
  id: string;
  category: string;
  sub_category: number;
  description: number;
  total_amount: number;
  amount: number;
  status: string;
  created_at: string;
}

export default function EditConstractionPage() {
  const { id } = useParams() as { id: string };
  const [selected, setSelected] = useState<CashOut | null>(null);

  const columns: Column<CashOut>[] = [
    { key: "category", label: "Category", sortable: true },
    { key: "sub_category", label: "Sub Category", sortable: true },
    { key: "created_at", label: "Date", sortable: true },
    { key: 'amount', label: 'Amount', sortable: true, render : (item)=>{
      const formatted = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0, // hilangkan koma jika tidak mau desimal
      }).format(item.amount);
      return (<div>{formatted}</div>)
    } },
    { key: "description", label: "Description", sortable: true },
  ];

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const openDeleteModal = (row: CashOut) => {
    console.log(row)
    setSelected(row);
    (document.getElementById("delete_modal") as HTMLDialogElement)?.showModal();
  };

  const handleDelete = async () => {
    if (!selected) return;
    try {
      await axios.delete(`/finance/cash-out/transaction/${selected.transaction_id}`);
      handleReload()
      toast.success("Transaksi berhasil dihapus");
    } finally {
      closeDeleteModal();
    }
  };

  const handleReload = async () => {
    TableRef?.current?.reload()
  };

  const TableRef = useRef<{ reload: () => void }>(null);

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Update Cash Out</h1>
      <CashOutForm id={id} mode="edit" handleReload={handleReload}/>
      <DataTable
        ref={TableRef}
        endpoint={`/finance/cash-out/${id}/transaction`}
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            <button
              className="btn btn-sm btn-error"
              onClick={() => openDeleteModal(row)}
            >
              Delete
            </button>
          </div>
        )}
      />
      <ModalDeleteConfirm
              id="delete_modal"
              onClose={closeDeleteModal}
              onConfirm={handleDelete}
              message={`Hapus transaksi "${selected?.category}"?`}
            />
    </div>
  );
}
