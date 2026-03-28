"use client";

import DataTable, { Column } from "@/components/datatable/datatable";
import { useForm } from "react-hook-form";
import axios from "@/lib/axios";
import { useRef, useState } from "react";
import ModalForm from "@/components/datatable/modal/ModalCreateEdit";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import toast from "react-hot-toast";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { ArrowUpDown, Pencil, Trash2 } from "lucide-react";
import { NumericFormat } from "react-number-format";
import { convertToRupiah } from "@/lib/utils";

interface BankAccount {
  id: string;
  name: string;
  code: string;
  account_number: string;
  opening_balance: number | null;
  balance: number;
  created_at: string; // bisa diganti ke Date jika ingin langsung diparsing
}

export default function BankAccountPage() {
  const columns: Column<BankAccount>[] = [
    { key: "code", label: "Code", sortable: true },
    { key: "name", label: "Name", sortable: true },
    { key: "account_number", label: "Account Number", sortable: true },
    {
      key: "balance",
      label: "Balance",
      sortable: true,
      render: (item) => {
        let formatted;
        if (!item.balance) formatted = convertToRupiah(0);
        else formatted = convertToRupiah(item.balance);

        return <div>{formatted}</div>;
      },
    },
  ];

  const {
    register,
    handleSubmit,
    setValue,
    reset,
    watch,
    formState: { errors },
  } = useForm<BankAccount>();

  const router = useRouter();

  const [modalMode, setModalMode] = useState<"create" | "edit">("create");
  const [selectedBankAccount, setSelectedBankAccount] =
    useState<BankAccount | null>(null);
  const [loading, setLoading] = useState(false);

  const openModal = (bank_account: BankAccount | null = null) => {
    const modal = document.getElementById("modify") as HTMLDialogElement;
    if (bank_account) {
      setModalMode("edit");
      reset(bank_account);
      setSelectedBankAccount(bank_account);
    } else {
      reset();
      setValue("opening_balance", 0);
      setModalMode("create");
      setSelectedBankAccount(null);
    }
    modal.showModal();
  };

  const openDeleteModal = (bank_account: BankAccount) => {
    const modal = document.getElementById("delete_modal") as HTMLDialogElement;
    setSelectedBankAccount(bank_account);
    modal.showModal();
  };

  const closeModal = () => {
    (document.getElementById("modify") as HTMLDialogElement)?.close();
  };

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const onSubmit = async (data: BankAccount) => {
    setLoading(true);
    try {
      if (/^\d/.test(data.code)) {
        toast.error("Code harus diawali huruf (A-Z)");
        return;
      }
      if (selectedBankAccount) {
        await axios.put(
          `/finance/bank-account/${selectedBankAccount.id}`,
          data
        );
      } else {
        await axios.post(`/finance/bank-account`, data);
      }
      toast.success("BankAccount berhasil disimpan!");
      handleReload();
    } catch (err: any) {
      toast.error("Gagal menyimpan bank-account.");
    } finally {
      setLoading(false);
      closeModal();
    }
  };

  const handleDelete = async () => {
    setLoading(true);
    if (!selectedBankAccount) return;
    try {
      await axios.delete(`/finance/bank-account/${selectedBankAccount.id}`);
      toast.success("Bank Account berhasil dihapus!");
      handleReload();
    } catch (err) {
      toast.error("Gagal menghapus bank .");
    } finally {
      setLoading(false);
      closeDeleteModal();
      handleReload();
    }
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
      await axios.post("/finance/contact/import", formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      toast.success("Kontak berhasil diimport!");
      handleReload();
    } catch {
      toast.error("Gagal mengimpor kontak");
    } finally {
      setImporting(false);
      resetImport();
      (document.getElementById("import_modal") as HTMLDialogElement)?.close();
    }
  };

  const handleReload = async () => {
    TableRef?.current?.reload();
  };

  const TableRef = useRef<{ reload: () => void }>(null);

  return (
    <div>
      <div role="tablist" className="tabs tabs-border mb-6">
        <Link href={"/finance/bank-account"}>
          <div role="tab" className="tab tab-active">
            Bank List
          </div>
        </Link>
        <Link href={"/finance/bank-account-transfer"}>
          <div role="tab" className="tab">
            Bank Transfer
          </div>
        </Link>
      </div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Bank Accounts</h1>

      <DataTable
        ref={TableRef}
        endpoint="/finance/bank-account"
        onClickCreate={() => openModal()}
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            <button
              className="btn btn-sm btn-info"
              onClick={() =>
                router.push(
                  `/finance/bank-account/${row.id}/mutasi?bank=${row.name}`
                )
              }
            >
              <ArrowUpDown size={16} className="mr-1" />
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
        title={
          modalMode === "create" ? "Create Bank Account" : "Edit Bank Account"
        }
        onClose={closeModal}
        onSubmit={handleSubmit(onSubmit)}
        loading={loading}
      >
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">Code</legend>
          <input
            type="text"
            className="input w-full"
            {...register("code", { required: true })}
          />
          {errors.code && <p className="text-red-500 text-sm">Wajib diisi</p>}
        </fieldset>
        <fieldset className="fieldset w-full">
          <legend className="fieldset-legend">Name</legend>
          <input
            type="text"
            className="input w-full"
            {...register("name", { required: true })}
          />
          {errors.name && <p className="text-red-500 text-sm">Wajib diisi</p>}
        </fieldset>
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">Account Number</legend>
          <input
            type="text"
            className="input w-full"
            {...register("account_number", { required: true })}
          />
          {errors.account_number && (
            <p className="text-red-500 text-sm">Wajib diisi</p>
          )}
        </fieldset>
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">Opening Balance</legend>
          <NumericFormat
            thousandSeparator="."
            decimalSeparator=","
            prefix="Rp "
            value={watch("opening_balance")}
            className="input input-bordered w-full"
            {...register("opening_balance")}
            onValueChange={(values) => {
              const { floatValue } = values;
              setValue("opening_balance", floatValue || 0);
            }}
          />
          {errors.account_number && (
            <p className="text-red-500 text-sm">Wajib diisi</p>
          )}
        </fieldset>
      </ModalForm>

      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        loading={loading}
        message={`Are you sure you want to delete bank-account "${selectedBankAccount?.name}"?`}
      />
    </div>
  );
}
