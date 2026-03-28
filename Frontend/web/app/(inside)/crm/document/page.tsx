"use client";

import DataTable, { Column } from "@/components/datatable/datatable";
import { useState } from "react";
import { useForm } from "react-hook-form";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import { redirect } from "next/navigation";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import DocumentSummary from "./summary";
import { CheckCircle, Pencil, Trash2 } from "lucide-react";
import { isAllowed } from "@/lib/utils";
import FilterLead from "./filter";

interface Document {
  id: string;
  name: string;
  phone: string;
  notes: string | null;
  status: string;
  property_unit: string;
  created_at: string;
  duration: string;
}

export default function DocumentPage() {
  const columns: Column<Document>[] = [
    { key: "name", label: "Name", sortable: true },
    { key: "phone", label: "Phone", sortable: true },
    { key: "notes", label: "Notes", sortable: true },
    { key: "property_unit", label: "Property", sortable: true },
    { key: "status", label: "Status", sortable: true },
    {
      key: "created_at",
      label: "Document Date",
      sortable: true,
      render: (item) => new Date(item.created_at).toLocaleDateString(),
    },
    { key: "duration", label: "Duration", sortable: true },
  ];

  const [selectedDocument, setSelectedDocument] = useState<Document | null>(
    null
  );

  const openDeleteModal = (row: Document) => {
    setSelectedDocument(row);
    const modal = document.getElementById("delete_modal") as HTMLDialogElement;
    modal?.showModal();
  };

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const handleDelete = async () => {
    if (!selectedDocument) return;
    try {
      await axios.delete(`/crm/lead-document/${selectedDocument.id}`);
      toast.success("Document berhasil dihapus!");
    } catch (err) {
      toast.error("Gagal menghapus document.");
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
      <h1 className="text-xl font-bold text-gray-600 mb-6">Documents</h1>
      <DocumentSummary />

      <FilterLead handleFilter={handleFilter} />

      <DataTable
        endpoint="/crm/lead-document"
        onClickCreate={() => redirect("/crm/document/create")}
        columns={columns}
        filter={filter}
        actions={(row) => (
          <div className="flex gap-2">
            {isAllowed('lead.verify_document') && (<button
              className="btn btn-sm btn-info"
              onClick={() =>
                redirect(`/crm/document/${row.id}/verify
                `)
              }
            >
              <CheckCircle size={16} className="mr-1" />
            </button>)}
            <button
              className="btn btn-sm btn-warning"
              onClick={() => redirect(`/crm/document/${row.id}/edit`)}
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
        message={`Are you sure you want to delete document for "${selectedDocument?.name}"?`}
      />
    </div>
  );
}
