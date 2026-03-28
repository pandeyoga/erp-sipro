"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import DataTable, { Column } from "@/components/datatable/datatable";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import RetentionSummary from "./summary";
import { isAllowed } from "@/lib/utils";
import { Pencil, Trash2 } from "lucide-react";

interface Retention {
  id: string;
  description: string; // deskripsi kasus retention
  notes: string | null; // catatan (optional)
  lead_name: string; // nama konsumen/lead
  status: "open" | "in_progress" | "resolved"; // status retention
  opened_at: string; // tanggal kasus dibuka (ISO string)
  estimated_resolved_at: string; // estimasi selesai (ISO string)
  project_name: string; // nama project
  cluster_name: string; // nama cluster
  unit_type: string; // tipe unit
  sub_contractor_name: string; // nama sub-kontraktor
  sub_contractor_id: string; // ID sub-kontraktor
  unit_number: string; // nomor unit
  duration: number; // durasi (hari)
  case_pictures?: string[]; // URL gambar bukti kasus
  case_documentations?: string[]; // URL dokumentasi after update
}

export default function RetentionPage() {
  const router = useRouter();
  const [selected, setSelected] = useState<Retention | null>(null);

  const columns: Column<Retention>[] = [
    { key: "lead_name", label: "Consument", sortable: true },
    { key: "project_name", label: "Project", sortable: true },
    { key: "cluster_name", label: "Cluster", sortable: true },
    { key: "unit_type", label: "Unit Type", sortable: true },
    { key: "status", label: "Retention Status", sortable: true },
    { key: "description", label: "Case Description", sortable: false },
    { key: "opened_at", label: "Opened At", sortable: true },
    { key: "estimated_resolved_at", label: "Est. Resolved", sortable: true },
    { key: "duration", label: "Duration", sortable: true },
    { key: "sub_contractor_name", label: "Subcon", sortable: true },
    { key: "notes", label: "Notes", sortable: false },
  ];

  const openDeleteModal = (row: Retention) => {
    setSelected(row);
    (document.getElementById("delete_modal") as HTMLDialogElement)?.showModal();
  };

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const handleDelete = async () => {
    if (!selected) return;
    try {
      await axios.delete(`/property/retention/${selected.id}`);
      toast.success("Data berhasil dihapus");
    } catch {
      toast.error("Gagal menghapus data");
    } finally {
      closeDeleteModal();
    }
  };

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Retention</h1>

      {/* Summary (dummy, sesuaikan nanti dengan data summary dari endpoint) */}
      <RetentionSummary />

      {/* Data Table */}
      <DataTable
        endpoint="/property/retention"
        onClickCreate={
          isAllowed("property.create_retention")
            ? () => router.push("/property/retention/create")
            : undefined
        }
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            {isAllowed("property.update_retention") && (<button
              className="btn btn-sm btn-warning"
              onClick={() => router.push(`/property/retention/${row.id}/edit`)}
            >
              <Pencil size={16} className="mr-1" />
            </button>)}
            {isAllowed("property.delete_retention") && (<button
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
        message={`Hapus data untuk "${selected?.project_name}"?`}
      />
    </div>
  );
}
