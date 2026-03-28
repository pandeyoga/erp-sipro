"use client";

import DataTable, { Column } from "@/components/datatable/datatable";
import PermissionForm from "./permission-form";
import { useForm } from "react-hook-form";
import axios from "@/lib/axios";
import { useEffect, useRef, useState } from "react";
import ModalForm from "@/components/datatable/modal/ModalCreateEdit";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import toast from "react-hot-toast";
import { isAllowed } from "@/lib/utils";
import { Pencil, Trash } from "lucide-react";
import { object } from "zod";
import { useTranslations } from "next-intl";

interface Role {
  id: string;
  name: string;
  description: string;
  group: string;
  permissions: Array<
    string | {
      id: string;
      role_id: string;
      permission_code: string;
      created_at: string;
      updated_at: string;
    } | {code: string ; name:string}
  >;
}

export default function RolePage() {
  const t = useTranslations('common');
  const columns: Column<Role>[] = [
    { key: "name", label: t('name'), sortable: true },
    { key: "description", label: t('description'), sortable: true },
  ];

  const {
    register,
    handleSubmit,
    setValue,
    reset,
    watch,
    formState: { errors },
  } = useForm<Role>();

  const [modalMode, setModalMode] = useState<"create" | "edit">("create");
  const [selectedRole, setSelectedRole] = useState<Role | null>(null);
  const [loading, setLoading] = useState(false);
  const [groupOptions, setGroupOptions] = useState<{ key: string; name: string }[]>([]);

  useEffect(() => {
    const fetchGroups = async () => {
      try {
        const res = await axios.get("/manage/role/group");
        setGroupOptions(res.data.data);
      } catch (err) {
        console.error("Failed to fetch role groups", err);
      }
    };
    fetchGroups();
  }, []);

  const openModal = (role: Role | null = null) => {
    const modal = document.getElementById("modify") as HTMLDialogElement;
    if (role) {
      setModalMode("edit");
      setSelectedRole(null)
      reset(role);
      setSelectedRole(role);
    } else {
      setModalMode("create");
      setValue("name","")
      setValue("description","")
      setValue("group","")
      setValue("permissions",[])
      setSelectedRole(null);
    }
    modal.showModal();
  };

  const openDeleteModal = (role: Role) => {
    const modal = document.getElementById("delete_modal") as HTMLDialogElement;
    setSelectedRole(role);
    modal.showModal();
  };

  const closeModal = () => {
    (document.getElementById("modify") as HTMLDialogElement)?.close();
  };

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const onSubmit = async (data: Role) => {
    setLoading(true);
    try {
      if((data.permissions[0] as any).code){
        data.permissions = (data.permissions as any).map((p : any)=> p.code)
      }
      if (modalMode === "create") {
        await axios.post("/manage/role", data);
      } else if (modalMode === "edit" && selectedRole) {
        
        await axios.put(`/manage/role/${selectedRole.id}`, data);
      }
      toast.success("Role berhasil disimpan!");
      handleReload()
    } catch (err: any) {
      toast.error("Gagal menyimpan role.");
    } finally {
      closeModal();
      setLoading(false);
    }
  };

  const handleReload = async () => {
    TableRef?.current?.reload()
  };

  const handleDelete = async () => {
    if (!selectedRole) return;
    try {
      await axios.delete(`/manage/role/${selectedRole.id}`);
      toast.success("Role berhasil dihapus!");
      handleReload()
    } catch (err) {
      toast.error("Gagal menghapus role.");
    } finally {
      closeDeleteModal();
    }
  };
  const TableRef = useRef<{ reload: () => void }>(null);

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Manage {t('role')}</h1>

      <DataTable
        ref={TableRef}
        endpoint="/manage/role"
        onClickCreate={isAllowed('role.create') ? () => openModal() : undefined}
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            { isAllowed('role.update') && <button className="btn btn-sm btn-warning" onClick={() => openModal(row)}><Pencil size={16} className="mr-1" /></button>}
            { isAllowed('role.delete') && <button className="btn btn-sm btn-error" onClick={() => openDeleteModal(row)}><Trash size={16} className="mr-1" /></button>} 
          </div>
        )}
      />

      <ModalForm
        id="modify"
        title={modalMode === "create" ? "Create Role" : "Edit Role"}
        onClose={closeModal}
        onSubmit={handleSubmit(onSubmit)}
        loading={loading}
        width="2xl"
      >
        <fieldset className="fieldset w-full">
          <legend className="fieldset-legend">{t('name')}</legend>
          <input type="text" className="input w-full" {...register("name", { required: true })} />
          {errors.name && <p className="text-red-500 text-sm">{t('required_field')}</p>}
        </fieldset>

        <fieldset className="fieldset w-full">
          <legend className="fieldset-legend">{t('description')}</legend>
          <input type="text" className="input w-full" {...register("description", { required: true })} />
          {errors.description && <p className="text-red-500 text-sm">{t('required_field')}</p>}
        </fieldset>

        <fieldset className="fieldset w-full">
          <legend className="fieldset-legend">Group</legend>
          <select className="select select-bordered w-full" {...register("group", { required: true })}>
            <option value="">{t('select')} group</option>
            {groupOptions.map((opt) => (
              <option key={opt.key} value={opt.key}>
                {opt.name}
              </option>
            ))}
          </select>
          {errors.group && <p className="text-red-500 text-sm">{t('required_field')}</p>}
        </fieldset>

        <h2 className="mt-4 font-medium">Permission</h2>

        <PermissionForm setPermission={(value) => setValue("permissions", value)} defaultValues={ watch('permissions') } />
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