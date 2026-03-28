"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import DataTable, { Column } from "@/components/datatable/datatable";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import { Pencil, Trash2, Map } from "lucide-react";
import { isAllowed } from "@/lib/utils";

interface Project {
  id: string;
  name: string;
  location: string;
  developer: string;
  status: "active" | "inactive"; // sesuai API
  area_total_sqm: string;
  start_date: string;
}

const statusBadge: Record<Project["status"], string> = {
  active: "badge badge-success",
  inactive: "badge badge-ghost",
};

export default function ProjectPage() {
  const router = useRouter();
  const [selected, setSelected] = useState<Project | null>(null);

  const columns: Column<Project>[] = [
    { key: "name", label: "Name", sortable: true },
    { key: "location", label: "Location", sortable: true },
    { key: "developer", label: "Developer", sortable: true },
    { key: "area_total_sqm", label: "Area Total (m²)", sortable: true },
    {
      key: "status",
      label: "Status",
      render: (row) => (
        <span className={statusBadge[row.status] || "badge"}>{row.status}</span>
      ),
    },
    { key: "start_date", label: "Start Date", sortable: true },
  ];

  const openDeleteModal = (row: Project) => {
    setSelected(row);
    (document.getElementById("delete_modal") as HTMLDialogElement)?.showModal();
  };

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const handleDelete = async () => {
    if (!selected) return;
    try {
      await axios.delete(`/property/projects/${selected.id}`);
      toast.success("Data berhasil dihapus");
    } catch {
      toast.error("Gagal menghapus data");
    } finally {
      closeDeleteModal();
    }
  };

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Project</h1>

      {/* Data Table */}
      <DataTable
        endpoint="/property/projects"
        onClickCreate={
          isAllowed("property.create_project")
            ? () => router.push("/property/project/create")
            : undefined
        }
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            {isAllowed("property.get_all_property") && (
              <button
                className="btn btn-sm btn-info"
                onClick={() =>
                  router.push(`/property/siteplan?project-id=${row.id}`)
                }
              >
                <Map size={16} className="mr-1" />
              </button>
            )}
            {isAllowed("property.update_project") && (
              <button
                className="btn btn-sm btn-warning"
                onClick={() => router.push(`/property/project/${row.id}/edit`)}
              >
                <Pencil size={16} className="mr-1" />
              </button>
            )}
            {isAllowed("property.delete_project") && (
              <button
                className="btn btn-sm btn-error"
                onClick={() => openDeleteModal(row)}
              >
                <Trash2 size={16} className="mr-1" />
              </button>
            )}
          </div>
        )}
      />

      {/* Modal Delete */}
      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        message={`Hapus project "${selected?.name}"?`}
      />
    </div>
  );
}
