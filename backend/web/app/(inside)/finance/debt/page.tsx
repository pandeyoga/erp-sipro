"use client";

import DataTable, { Column } from "@/components/datatable/datatable";
import { useForm } from "react-hook-form";
import axios from "@/lib/axios";
import { useEffect, useRef, useState } from "react";
import ModalForm from "@/components/datatable/modal/ModalCreateEdit";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import toast from "react-hot-toast";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { ArrowUpDown, CreditCard, Pencil, Trash2, Wallet } from "lucide-react";
import { NumericFormat } from "react-number-format";
import { convertToRupiah } from "@/lib/utils";
import axiosInstance from "@/lib/axios";
import React from "react";

interface Debt {
  id: string;
  name: string;
  description: string;
  total_amount: number | null;
  paid_amount: number;
  category: string;
  created_by_name: string;
  created_at: string; // bisa diganti ke Date jika ingin langsung diparsing
}

export default function DebtPage() {
  const columns: Column<Debt>[] = [
    { key: "name", label: "Name", sortable: true },
    { key: "description", label: "Description", sortable: true },
    { key: "category", label: "Category", sortable: true },
    {
      key: "total_amount",
      label: "Total Amount",
      sortable: true,
      render: (item) => {
        let formatted;
        if (!item.total_amount) formatted = convertToRupiah(0);
        else formatted = convertToRupiah(item.total_amount);

        return <div>{formatted}</div>;
      },
    },
    {
      key: "paid_amount",
      label: "Paid Amount",
      sortable: true,
      render: (item) => {
        let formatted;
        if (!item.paid_amount) formatted = convertToRupiah(0);
        else formatted = convertToRupiah(item.paid_amount);

        return <div>{formatted}</div>;
      },
    },
    { key: "created_by_name", label: "Create by", sortable: true },
    {
      key: "created_at",
      label: "Date",
      sortable: true,
      render: (item) => new Date(item.created_at).toLocaleDateString(),
    },
  ];

  const [categories, setCategories] = useState<{ id: string; name: string }[]>(
    []
  );

  useEffect(() => {
    axiosInstance.get("/finance/loan/categories").then((response) => {
      setCategories(response.data.data);
    });
  }, []);

  const [bank_list, setBankLists] = useState<{ id: string; name: string }[]>(
    []
  );
  useEffect(() => {
    axiosInstance.get("/finance/cash-in/bank-list").then((response) => {
      setBankLists(response.data.data);
    });
  }, []);

  const {
    register,
    handleSubmit,
    setValue,
    reset,
    watch,
    formState: { errors },
  } = useForm();

  const router = useRouter();

  const [modalMode, setModalMode] = useState<"create" | "edit" | "payment">(
    "create"
  );
  const [selectedDebt, setDebt] = useState<Debt | null>(null);
  const [loading, setLoading] = useState(false);

  const openModal = (debt: Debt | null = null, isPayment = false) => {
    const modal = document.getElementById("modify") as HTMLDialogElement;
    if (debt) {
      reset({});
      if (isPayment) {
        setModalMode("payment");
      } else {
        setModalMode("edit");
      }
      reset(debt);
      setDebt(debt);
    } else {
      reset({});
      setModalMode("create");
      setDebt(null);
    }
    modal.showModal();
  };

  const openDeleteModal = (debt: Debt) => {
    const modal = document.getElementById("delete_modal") as HTMLDialogElement;
    setDebt(debt);
    modal.showModal();
  };

  const closeModal = () => {
    (document.getElementById("modify") as HTMLDialogElement)?.close();
  };

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const onSubmit = async (data: any) => {
    setLoading(true);
    try {
      if (selectedDebt) {
        if(modalMode == "payment"){
          await axios.put(`/finance/loan/${selectedDebt.id}/payment`, data);
          toast.success("Hutang berhasil dibayar!");
        }else{
          await axios.put(`/finance/loan/${selectedDebt.id}`, data);
          toast.success("Hutang berhasil disimpan!");
        }
      } else {
        await axios.post(`/finance/loan`, data);
        toast.success("Hutang berhasil disimpan!");
      }
      handleReload();
    } catch (err: any) {
      toast.error("Gagal menyimpan/membayar Hutang.");
    } finally {
      setLoading(false);
      closeModal();
    }
  };

  const handleDelete = async () => {
    setLoading(true);
    if (!selectedDebt) return;
    try {
      await axios.delete(`/finance/loan/${selectedDebt.id}`);
      toast.success("Hutang berhasil dihapus!");
      handleReload();
    } catch (err) {
      toast.error("Gagal menghapus Hutang .");
    } finally {
      setLoading(false);
      closeDeleteModal();
      handleReload();
    }
  };

  const handleReload = async () => {
    TableRef?.current?.reload();
  };

  const TableRef = useRef<{ reload: () => void }>(null);

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Hutang</h1>
      <button> </button>
      <DataTable
        ref={TableRef}
        endpoint="/finance/loan"
        onClickCreate={() => openModal()}
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            <button
              className="btn btn-sm btn-info"
              onClick={() => openModal(row, true)}
            >
              <Wallet size={16} className="mr-1" />
            </button>
            <button
              className="btn btn-sm btn-warning"
              onClick={() => openModal(row)}
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

      <ModalForm
        id="modify"
        title={modalMode === "create" ? "Buat Hutang" : modalMode === "edit"  ? "Edit Hutang" : "Bayar Hutang"}
        onClose={closeModal}
        onSubmit={handleSubmit(onSubmit)}
        loading={loading}
      >
        {
          modalMode == "payment" ? (
            <React.Fragment>
              <fieldset className="fieldset w-full mt-4">
                <legend className="fieldset-legend">Bank</legend>
                <select
                  className="select select-bordered w-full"
                  {...register("bank_account_id", { required: true })}
                >
                  <option value="">-- Pilih Bank --</option>
                  {bank_list.map((value) => (
                    <option key={value.id} value={value.id}>
                      {value.name}
                    </option>
                  ))}
                </select>
                {errors.bank_account_id && (
                  <p className="text-red-500 text-sm">Wajib diisi</p>
                )}
              </fieldset>

              <fieldset className="fieldset w-full mt-4">
                <legend className="fieldset-legend">Amount</legend>
                <NumericFormat
                  thousandSeparator="."
                  decimalSeparator=","
                  prefix="Rp "
                  value={watch("amount")}
                  className="input input-bordered w-full"
                  {...register("amount")}
                  onValueChange={(values) => {
                    const { floatValue } = values;
                    setValue("amount", floatValue || 0);
                  }}
                />
                {errors.amount && (
                  <p className="text-red-500 text-sm">Wajib diisi</p>
                )}
              </fieldset>
            </React.Fragment>
          ) :
          (
            <React.Fragment>
              <fieldset className="fieldset w-full mt-4">
                <legend className="fieldset-legend">Kategori</legend>
                <select
                  className="select select-bordered w-full"
                  {...register("category_id", { required: true })}
                >
                  <option value="">-- Pilih Kategori --</option>
                  {categories.map((value) => (
                    <option key={value.id} value={value.id}>
                      {value.name}
                    </option>
                  ))}
                </select>
                {errors.category_id && (
                  <p className="text-red-500 text-sm">Wajib diisi</p>
                )}
              </fieldset>
              <fieldset className="fieldset w-full">
                <legend className="fieldset-legend">Nama</legend>
                <input
                  type="text"
                  className="input w-full"
                  {...register("name", { required: true })}
                />
                {errors.name && <p className="text-red-500 text-sm">Wajib diisi</p>}
              </fieldset>

              <fieldset className="fieldset w-full mt-4">
                <legend className="fieldset-legend">Bank</legend>
                <select
                  className="select select-bordered w-full"
                  {...register("bank_account_id", { required: true })}
                >
                  <option value="">-- Pilih Bank --</option>
                  {bank_list.map((value) => (
                    <option key={value.id} value={value.id}>
                      {value.name}
                    </option>
                  ))}
                </select>
                {errors.bank_account_id && (
                  <p className="text-red-500 text-sm">Wajib diisi</p>
                )}
              </fieldset>

              <fieldset className="fieldset w-full mt-4">
                <legend className="fieldset-legend">Amount</legend>
                <NumericFormat
                  thousandSeparator="."
                  decimalSeparator=","
                  prefix="Rp "
                  value={watch("amount")}
                  className="input input-bordered w-full"
                  {...register("amount")}
                  onValueChange={(values) => {
                    const { floatValue } = values;
                    setValue("amount", floatValue || 0);
                  }}
                />
                {errors.amount && (
                  <p className="text-red-500 text-sm">Wajib diisi</p>
                )}
              </fieldset>

              <fieldset className="fieldset w-full mt-4">
                <legend className="fieldset-legend">Deskripsi</legend>
                <input
                  type="text"
                  className="input w-full"
                  {...register("description", { required: true })}
                />
                {errors.description && (
                  <p className="text-red-500 text-sm">Wajib diisi</p>
                )}
              </fieldset>
            </React.Fragment>
          )
        }
      </ModalForm>

      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        loading={loading}
        message={`Are you sure you want to delete debt "${selectedDebt?.name}"?`}
      />
    </div>
  );
}
