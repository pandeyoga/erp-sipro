"use client";

import DataTable, { Column } from "@/components/datatable/datatable";
import { useForm } from "react-hook-form";
import axios from "@/lib/axios";
import { useEffect, useRef, useState } from "react";
import ModalForm from "@/components/datatable/modal/ModalCreateEdit";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import LeadSummary from "./summary";
import { redirect } from "next/navigation";
import toast from "react-hot-toast";
import { Eye, Pencil, Trash2 } from "lucide-react";
import { isAllowed } from "@/lib/utils";
import FilterLead from "./filter";

interface Lead {
  id: string;
  name: string;
  phone: string;
  notes: string | null;
  status: string;
  scheduled_date: string;
  actual_survey_date: string;
  duration: string;
  created_at: string;
}

export default function LeadPage() {
  const columns: Column<Lead>[] = [
    { key: "name", label: "Name", sortable: true },
    { key: "phone", label: "Phone", sortable: false },
    { key: "scheduled_date", label: "Scheduled Date", sortable: false },
    { key: "actual_survey_date", label: "Actual Survey Date", sortable: false },
    { key: "status", label: "Status", sortable: false },
    {
      key: "duration",
      label: "Duration",
      sortable: true,
    },
    { key: "notes", label: "Notes", sortable: false },
  ];

  const [selectedLead, setSelectedLead] = useState<Lead | null>(null);
  

  const openDeleteModal = (role: Lead) => {
    const modal = document.getElementById("delete_modal") as HTMLDialogElement;
    setSelectedLead(role);
    modal.showModal();
  };

  const closeModal = () => {
    (document.getElementById("modify") as HTMLDialogElement)?.close();
  };

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const handleDelete = async () => {
    if (!selectedLead) return;
    try {
      await axios.delete(`/crm/survey/${selectedLead.id}`);
      toast.success("Lead berhasil dihapus!");
    } catch (err) {
      toast.error("Gagal menghapus role.");
    } finally {
      closeDeleteModal();
    }
  };

  const handleReload = async () => {
      TableRef?.current?.reload();
    };

  const [filter, setFilter] = useState({});

  const handleFilter = async (status : string,source : string, marketing_id : string ) => {
    const filters = { status, source, marketing_id }

    setFilter(
      Object.entries(filters).reduce((acc, [key, value]) => {
        if (value) (acc as any)[key] = value
        return acc
      }, {} as typeof filters)
    )
  };
  
  const TableRef = useRef<{ reload: () => void }>(null);


  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Surveys</h1>
      <LeadSummary />

      <FilterLead handleFilter={handleFilter}/>

      <DataTable
        ref={TableRef}
        endpoint="/crm/survey"
        onClickCreate={isAllowed('lead.create') ? () => redirect("/crm/survey/create") : undefined }
        columns={columns}
        filter={filter}
        actions={(row) => (
          <div className="flex gap-2">

            {isAllowed('lead.update') && (<button
              className="btn btn-sm btn-warning"
              onClick={() => redirect(`/crm/survey/${row.id}/edit`)}
            >
              <Pencil size={16} className="mr-1" />
            </button>)}
          </div>
        )}
      />

      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        message={`Are you sure you want to delete survey "${selectedLead?.name}"?`}
      />
    </div>
  );
}
