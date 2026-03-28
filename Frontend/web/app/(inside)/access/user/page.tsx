"use client";

import DataTable, { Column, DataTableRef } from "@/components/datatable/datatable";
import { useForm } from "react-hook-form";
import axios from "@/lib/axios";
import { useRef, useState } from "react";
import ModalForm from "@/components/datatable/modal/ModalCreateEdit";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import RoleFieldset from "./role-select";
import toast from "react-hot-toast";
import { isAllowed } from "@/lib/utils";
import { Pencil, PencilIcon, Trash } from "lucide-react";
import { useTranslations } from "next-intl";

export interface User {
  id: string;
  name: string;
  email: string;
  role_id: string;
  password : string;
}

export default function UserPage() {
  const t = useTranslations('common');
  const columns: Column<User>[] = [
    { key: "name", label: t('name'), sortable: true },
    { key: "email", label: t('email'), sortable: true },
  ];

  const {
    register,
    handleSubmit,
    setValue,
    reset,
    formState: { errors },
  } = useForm<User>();

  const [modalMode, setModalMode] = useState<"create" | "edit">("create");
  const [selectedUser, setSelectedUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(false);

  const openModal = (user: User | null = null) => {
    const modal = document.getElementById("modify") as HTMLDialogElement;
    if (user) {
      setModalMode("edit");
      reset(user);
      setSelectedUser(user);
    } else {
      setModalMode("create");
      reset({
        "email" : "",
        "name" : "",
        "id" : "",
        "password" : "",
        "role_id" : ""
      });
      setSelectedUser(null);
    }
    modal.showModal();
  };

  const openDeleteModal = (user: User) => {
    const modal = document.getElementById("delete_modal") as HTMLDialogElement;
    setSelectedUser(user);
    modal.showModal();
  };

  const closeModal = () => {
    (document.getElementById("modify") as HTMLDialogElement)?.close();
  };

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const onSubmit = async (data: User) => {
    try {
      setLoading(true)
      if(modalMode == 'create'){
        await axios.post("/manage/user", data);
      }else{
        await axios.put("/manage/user/"+data.id, data);
      }
      handleRefresh()
    } catch (err: any) {
      toast.error("Gagal menyimpan user.");
    } finally {
      setLoading(false)
      closeModal();
    }
  };

  const handleDelete = async () => {
    if (!selectedUser) return;
    try {
      await axios.delete(`/manage/user/${selectedUser.id}`);
      toast.success("User berhasil dihapus!");
      handleRefresh()
    } catch (err) {
      toast.error("Gagal menghapus user.");
    } finally {
      closeDeleteModal();
    }
  };

  const tableRef = useRef<DataTableRef>(null)

  const handleRefresh = () => {
    tableRef.current?.reload()
  }
  

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Manage User</h1>

      <DataTable
        ref={tableRef}
        endpoint="/manage/user"
        onClickCreate={isAllowed('user.create') ? () => openModal() : undefined}
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            { isAllowed('user.update') && <button className="btn btn-sm btn-warning" onClick={() => openModal(row)}><Pencil size={16} className="mr-1" /></button>}
            { isAllowed('user.delete') && <button className="btn btn-sm btn-error" onClick={() => openDeleteModal(row)}><Trash size={16} className="mr-1" /></button>} 
          </div>
        )}
      />

      <ModalForm
        id="modify"
        title={modalMode === "create" ? "Create User" : "Edit User"}
        onClose={closeModal}
        onSubmit={handleSubmit(onSubmit)}
        loading={loading}
      >
        <fieldset className="fieldset w-full">
          <legend className="fieldset-legend">Name</legend>
          <input type="text" className="input w-full" {...register("name", { required: true })} />
          {errors.name && <p className="text-red-500 text-sm">{errors.name.message}</p>}
        </fieldset>
        <fieldset className="fieldset w-full">
          <legend className="fieldset-legend">Email</legend>
          <input type="text" className="input w-full" {...register("email", { required: true })} />
          {errors.email && <p className="text-red-500 text-sm">{errors.email.message}</p>}
        </fieldset>
        <RoleFieldset register={register} errors={errors}/>
        <fieldset className="fieldset w-full">
          <legend className="fieldset-legend">Password</legend>
          <input type="text" className="input w-full" {...register("password")} />
          {errors.password && <p className="text-red-500 text-sm">{errors.password.message}</p>}
        </fieldset>
      </ModalForm>

      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        loading={loading}
      />
    </div>
  );
}
