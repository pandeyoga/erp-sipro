"use client";

import DataTable, { Column } from "@/components/datatable/datatable";
import { useState } from "react";
import { useForm } from "react-hook-form";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import { redirect } from "next/navigation";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import ReservationSummary from "./summary";
import { isAllowed } from "@/lib/utils";
import { Pencil, Trash2 } from "lucide-react";
import FilterLead from "./filter";

interface Reservation {
  id: string;
  name: string;
  phone: string;
  notes: string | null;
  reservation_status: string;
  property_name: string;
  reservation_date: string;
  duration: string;
}

export default function ReservationPage() {
  const columns: Column<Reservation>[] = [
    { key: "name", label: "Name", sortable: true },
    { key: "phone", label: "Phone", sortable: true },
    { key: "notes", label: "Notes", sortable: true },
    { key: "property_name", label: "Property", sortable: true },
    { key: "reservation_status", label: "Status", sortable: true },
    {
      key: "reservation_date",
      label: "Reservation Date",
      sortable: true,
      render: (item) => new Date(item.reservation_date).toLocaleDateString(),
    },
    { key: "duration", label: "Duration", sortable: true },
  ];

  const [selectedReservation, setSelectedReservation] = useState<Reservation | null>(null);

  const openDeleteModal = (row: Reservation) => {
    setSelectedReservation(row);
    const modal = document.getElementById("delete_modal") as HTMLDialogElement;
    modal?.showModal();
  };

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const handleDelete = async () => {
    if (!selectedReservation) return;
    try {
      await axios.delete(`/crm/reservation/${selectedReservation.id}`);
      toast.success("Reservation berhasil dihapus!");
    } catch (err) {
      toast.error("Gagal menghapus reservation.");
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
      <h1 className="text-xl font-bold text-gray-600 mb-6">Reservations</h1>
      <ReservationSummary />

      <FilterLead handleFilter={handleFilter}/>

      <DataTable
        endpoint="/crm/reservation"
        onClickCreate={isAllowed('lead.create_reservation') ? () => redirect("/crm/reservation/create") : undefined}
        columns={columns}
        filter={filter}
        actions={(row) => (
          <div className="flex gap-2">
            {isAllowed('lead.update_reservation') && (<button
              className="btn btn-sm btn-warning"
              onClick={() => redirect(`/crm/reservation/${row.id}/edit`)}
            >
              <Pencil size={16} className="mr-1" />
            </button>)}
            {isAllowed('lead.delete_reservation') &&  (<button className="btn btn-sm btn-error" onClick={() => openDeleteModal(row)}>
            <Trash2 size={16} className="mr-1" />
            </button>)}
          </div>
        )}
      />

      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        message={`Are you sure you want to delete reservation for "${selectedReservation?.name}"?`}
      />
    </div>
  );
}
