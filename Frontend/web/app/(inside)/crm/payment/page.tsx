"use client";

import DataTable, { Column } from "@/components/datatable/datatable";
import { useState } from "react";
import { useForm } from "react-hook-form";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import { redirect } from "next/navigation";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import KprSummary from "./summary";
import PaymentSummary from "./summary";
import { Pencil, Trash2 } from "lucide-react";
import { isAllowed } from "@/lib/utils";
import FilterLead from "./filter";

interface KprDocument {
  id: string;
  name: string;
  phone: string;
  notes: string | null;
  status: "Input" | "Verification" | "Complete";
  property_unit: string;
  duration: number;
}

const statusBadge = {
  Input: "badge badge-info",
  Verification: "badge badge-warning",
  Complete: "badge badge-success",
};

export default function KprPage() {
  const columns: Column<KprDocument>[] = [
    { key: "name", label: "Name", sortable: true },
    { key: "phone", label: "Phone", sortable: true },
    { key: "notes", label: "Notes", sortable: true },
    {
      key: "status",
      label: "KPR Status",
      sortable: true,
      render: (item) => (
        <span className={statusBadge[item.status] || "badge"}>{item.status}</span>
      ),
    },
    { key: "property_unit", label: "Property", sortable: true },
    { key: "duration", label: "DueDates", sortable: true },
  ];

  const [selected, setSelected] = useState<KprDocument | null>(null);

  const openDeleteModal = (row: KprDocument) => {
    setSelected(row);
    (document.getElementById("delete_modal") as HTMLDialogElement)?.showModal();
  };

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const handleDelete = async () => {
    if (!selected) return;
    try {
      await axios.delete(`/crm/kpr/${selected.id}`);
      toast.success("Data berhasil dihapus.");
    } catch (err) {
      toast.error("Gagal menghapus data.");
    } finally {
      closeDeleteModal();
    }
  };

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
      <h1 className="text-xl font-bold text-gray-600 mb-6">Payment List</h1>

      <PaymentSummary/>

      <FilterLead handleFilter={handleFilter} />

      <DataTable
        endpoint="/crm/lead-payment"
        onClickCreate={isAllowed('lead.create_payment') ?  () => redirect("/crm/payment/create") : undefined}
        columns={columns}
        filter={filter}
        actions={(row) => (
          <div className="flex gap-2">
            {isAllowed('lead.update_payment') &&(<button
              className="btn btn-sm btn-warning"
              onClick={() => redirect(`/crm/payment/${row.id}/edit`)}
            >
              <Pencil size={16} className="mr-1" />
            </button>)}

            {isAllowed('lead.delete_payment') &&(<button
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
        message={`Are you sure you want to delete KPR for "${selected?.name}"?`}
      />
    </div>
  );
}
