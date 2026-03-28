"use client";

import DataTable, { Column } from "@/components/datatable/datatable";
import { useForm } from "react-hook-form";
import axios from "@/lib/axios";
import { useRef, useState } from "react";
import ModalForm from "@/components/datatable/modal/ModalCreateEdit";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import toast from "react-hot-toast";
import Link from "next/link";
import { Pencil, Trash, Trash2 } from "lucide-react";
import { isAllowed } from "@/lib/utils";
import { useTranslations } from "next-intl";
import axiosInstance from "@/lib/axios";

interface Contact {
  id: string;
  name: string;
  email: string | null;
  phone: string;
  location: string;
  is_duplicate: boolean;
  date: string;
  created_at: string; // bisa diganti ke Date jika ingin langsung diparsing
}

export default function ContactPage() {
  const t = useTranslations('common');
  const columns: Column<Contact>[] = [
    { key: "name", label: t('name'), sortable: true },
    { key: "email", label: t('email'), sortable: true },
    { key: "phone", label: t('phone'), sortable: true, render: (item) => (
      <Link href={`https://wa.me/${item.phone.replace(/\D/g, '')}`} target="_blank" rel="noopener noreferrer">
        {item.phone}
      </Link>
    )},
    { key: "location", label: t('city'), sortable: true },
    {
      key: "is_duplicate",
      label: "Duplicate",
      sortable: true,
      render: (item) => (item.is_duplicate ?  t('yes'): t('no')),
    },
    {
      key: "created_at",
      label: t('created_at'),
      sortable: true,
      render: (item) => new Date(item.created_at).toLocaleString(),
    },
  ];

  const {
    register,
    handleSubmit,
    setValue,
    reset,
    formState: { errors },
  } = useForm<Contact>();

  const [modalMode, setModalMode] = useState<"create" | "edit">("create");
  const [selectedContact, setSelectedContact] = useState<Contact | null>(null);
  const [loading, setLoading] = useState(false);

  const openModal = (contact: Contact | null = null) => {
    const modal = document.getElementById("modify") as HTMLDialogElement;
    if (contact) {
      setModalMode("edit");
      reset(contact);
      setSelectedContact(contact);
    } else {
      setModalMode("create");
      reset();
      setSelectedContact(null);
    }
    modal.showModal();
  };

  const openDeleteModal = (contact: Contact) => {
    const modal = document.getElementById("delete_modal") as HTMLDialogElement;
    setSelectedContact(contact);
    modal.showModal();
  };

  const closeModal = () => {
    (document.getElementById("modify") as HTMLDialogElement)?.close();
  };

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const onSubmit = async (data: Contact) => {
    setLoading(true);
    try {
      if (selectedContact) {
        await axios.put(`/crm/contact/${selectedContact.id}`, data);
      } else {
        await axios.post(`/crm/contact`, data);
      }
      toast.success("Contact berhasil disimpan!");
      handleReload()
    } catch (err: any) {
      toast.error("Gagal menyimpan contact.");
    } finally {
      setLoading(false);
      closeModal();
    }
  };

  const handleDelete = async () => {
    setLoading(true);
    if (!selectedContact) return;
    try {
      await axios.delete(`/crm/contact/${selectedContact.id}`);
      toast.success("Contact berhasil dihapus!");
      handleReload()
    } catch (err) {
      toast.error("Gagal menghapus contact.");
    } finally {
      setLoading(false);
      closeDeleteModal();
    }
  };

  const handleReload = async () => {
    TableRef?.current?.reload()
  };

  const [importing, setImporting] = useState(false);

  const {
    register: registerImport,
    handleSubmit: handleSubmitImport,
    reset: resetImport,
  } = useForm();

  const handleImport = async (data: any) => {
    if (!data.file || data.file.length === 0) {
      toast.error("Mohon pilih file Excel terlebih dahulu.");
      return;
    }

    const formData = new FormData();
    formData.append("file", data.file[0]);

    try {
      setImporting(true);
      await axios.post("/crm/contact/import", formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      toast.success("Kontak berhasil diimport!");
      handleReload()
    } catch {
      toast.error("Gagal mengimpor kontak");
    } finally {
      setImporting(false);
      resetImport();
      (document.getElementById("import_modal") as HTMLDialogElement)?.close();
    }
  };

  const TableRef = useRef<{ reload: () => void }>(null);

  const {
    register : registerExport,
    watch : watchExport
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
        `/crm/contact/export`,
        {
          params: {
            startDate: watchExport("start_date"),
            endDate: watchExport("end_date"),
          },
          responseType: "blob", // supaya bisa download file
        }
      );
  
      // buat blob url
      const url = window.URL.createObjectURL(new Blob([res.data]));
      const link = document.createElement("a");
      link.href = url;
  
      // kasih nama file sesuai kebutuhan
      link.setAttribute("download", "contacts_export.xlsx");
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (error) {
      console.error("Gagal export:", error);
    }
  };

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Contacts</h1>

      <div className="flex justify-between mb-4">
        <button
          className="btn btn-secondary"
          onClick={() => (document.getElementById("import_modal") as HTMLDialogElement)?.showModal()}
        >
          Import Excel
        </button>
        
      </div>

      <form className="flex items-end gap-4 w-1/2 mb-6">
        <div className="form-control w-full">
          <label className="label font-medium">From</label>
          <input
            type="date"
            {...registerExport("start_date")}
            className="input input-bordered w-full"
            required
          />
        </div>

        <div className="form-control w-full">
          <label className="label font-medium">To</label>
          <input
            type="date"
            {...registerExport("end_date")}
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
        endpoint="/crm/contact"
        onClickCreate={isAllowed('contact.create') ? () => openModal() : undefined}
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            { isAllowed('contact.update') && <button className="btn btn-sm btn-warning" onClick={() => openModal(row)}><Pencil size={16} className="mr-1" /></button>}
            { isAllowed('contact.delete') && <button className="btn btn-sm btn-error" onClick={() => openDeleteModal(row)}><Trash2 size={16} className="mr-1" /></button>} 
          </div>
        )}
      />

      <ModalForm
        id="modify"
        title={modalMode === "create" ? "Create Contact" : "Edit Contact"}
        onClose={closeModal}
        onSubmit={handleSubmit(onSubmit)}
        loading={loading}
      >
        <fieldset className="fieldset w-full">
          <legend className="fieldset-legend">{t('name')}</legend>
          <input
            type="text"
            className="input w-full"
            {...register("name", { required: true })}
          />
          {errors.name && <p className="text-red-500 text-sm">Wajib diisi</p>}
        </fieldset>

        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">{t('email')}</legend>
          <input type="email" className="input w-full" {...register("email")} />
        </fieldset>

        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">{t('phone')}</legend>
          <input
            type="text"
            className="input w-full"
            {...register("phone", { required: true })}
          />
          {errors.phone && <p className="text-red-500 text-sm">Wajib diisi</p>}
        </fieldset>

        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">{t('city')} </legend>
          <input
            type="text"
            className="input w-full"
            {...register("location")}
          />
        </fieldset>
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">{t('date')} </legend>
          <input
            type="date"
            className="input w-full"
            {...register("date")}
          />
        </fieldset>
      </ModalForm>

      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        loading={loading}
        message={`Are you sure you want to delete contact "${selectedContact?.name}"?`}
      />
      <dialog id="import_modal" className="modal">
        <div className="modal-box">
          <h3 className="font-bold text-lg">Import Kontak dari Excel</h3>
          <a
          href={`${process.env.NEXT_PUBLIC_FILES_URL}/static/importable-contact-template.xlsx`}
          target="_blank"
          rel="noopener noreferrer"
          className="text-sm underline text-blue-500"
        >
          Download Template Excel
        </a>
          <form
            onSubmit={handleSubmitImport(handleImport)}
            className="mt-4 space-y-4"
          >
            <input
              type="file"
              accept=".xlsx,.xls"
              {...registerImport("file", { required: true })}
              className="file-input file-input-bordered w-full"
            />
            <div className="flex justify-end gap-2 mt-4">
              <button
                type="button"
                onClick={() => (document.getElementById("import_modal") as HTMLDialogElement)?.close()}
                className="btn"
              >
                Batal
              </button>
              <button
                type="submit"
                className="btn btn-primary"
                disabled={importing}
              >
                {importing && <span className="loading loading-spinner"></span>}
                Import
              </button>
            </div>
          </form>
        </div>
      </dialog>
    </div>
  );
}
