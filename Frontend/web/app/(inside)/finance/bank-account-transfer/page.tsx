"use client";

import DataTable, { Column } from "@/components/datatable/datatable";
import { useForm } from "react-hook-form";
import axios from "@/lib/axios";
import { useEffect, useRef, useState } from "react";
import ModalForm from "@/components/datatable/modal/ModalCreateEdit";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import toast from "react-hot-toast";
import Link from "next/link";
import axiosInstance from "@/lib/axios";
import { NumericFormat } from "react-number-format";
import { Pencil, Trash2 } from "lucide-react";
import { convertToRupiah } from "@/lib/utils";

interface BankAccount {
  id: string;
  from_bank_account_id: string;
  to_bank_account_id: string;
  from_bank_account: string;
  to_bank_account: string;
  transfer_fee: number;
  amount: number;
  note: string;
  date: string;
}

export default function BankAccountPage() {
  const columns: Column<BankAccount>[] = [
    { key: "from_bank_account", label: "From", sortable: true },
    { key: "to_bank_account", label: "To", sortable: true },
    { key: "amount", label: "Amount", sortable: true, render : (item) => {
      let formatted;
      if(!item.amount ) formatted = convertToRupiah(0);
      else formatted = convertToRupiah(item.amount);
      
      return (<div>{formatted}</div>)
    }},
    { key: "transfer_fee", label: "Transfer Fee", sortable: true },
    {
      key: "date",
      label: "Date",
      sortable: true,
      render: (item) => new Date(item.date).toLocaleString(),
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
      setModalMode("create");
      reset();
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
    console.log(data)
    setLoading(true);
    try {
      if (selectedBankAccount) {
        await axios.put(
          `/finance/bank-account/transfer/${selectedBankAccount.id}`,
          data
        );
      } else {
        await axios.post(`/finance/bank-account/transfer`, data);
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
      await axios.delete(
        `/finance/bank-account/transfer/${selectedBankAccount.id}`
      );
      toast.success("Bank Account berhasil dihapus!");
      handleReload();
    } catch (err) {
      toast.error("Gagal menghapus bank .");
    } finally {
      setLoading(false);
      closeDeleteModal();
    }
  };

  const handleReload = async () => {
    TableRef?.current?.reload();
  };

  const TableRef = useRef<{ reload: () => void }>(null);

  const [bank_list, setBankLists] = useState<{ id: string; name: string }[]>(
    []
  );
  useEffect(() => {
    axiosInstance.get("/finance/cash-in/bank-list").then((response) => {
      setBankLists(response.data.data);
    });
  }, []);

  return (
    <div>
      <div role="tablist" className="tabs tabs-border mb-6">
        <Link href={'/finance/bank-account'}>
          <div role="tab" className="tab">Bank List</div>
        </Link>
        <Link href={'/finance/bank-account-transfer'}>
          <div role="tab" className="tab tab-active">Bank Transfer</div>
        </Link>
      </div>

      <h1 className="text-xl font-bold text-gray-600 mb-6">
        Bank Transfers
      </h1>

      <DataTable
        ref={TableRef}
        endpoint="/finance/bank-account/transfer"
        onClickCreate={() => openModal()}
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
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
          modalMode === "create" ? "Create BankAccount" : "Edit BankAccount"
        }
        onClose={closeModal}
        onSubmit={handleSubmit(onSubmit)}
        loading={loading}
      >
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">From Bank</legend>
          <select
            className="select select-bordered w-full"
            {...register("from_bank_account_id", { required: true })}
          >
            <option value="">-- Pilih Bank --</option>
            {bank_list.map((value) => (
              <option key={value.id} value={value.id}>
                {value.name}
              </option>
            ))}
          </select>
          {errors.from_bank_account_id && (
            <p className="text-red-500 text-sm">Wajib diisi</p>
          )}
        </fieldset>
        <fieldset className="fieldset w-full">
          <legend className="fieldset-legend">To Bank</legend>
          <select
            className="select select-bordered w-full"
            {...register("to_bank_account_id", { required: true })}
          >
            <option value="">-- Pilih Bank --</option>
            {bank_list.map((value) => (
              <option key={value.id} value={value.id}>
                {value.name}
              </option>
            ))}
          </select>
          {errors.to_bank_account_id && (
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
          {errors.amount && <p className="text-red-500 text-sm">Wajib diisi</p>}
        </fieldset>
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">Transfer Fee</legend>
          <NumericFormat
                        thousandSeparator="."
                        decimalSeparator=","
                        prefix="Rp "
                        value={watch("transfer_fee")}
                        className="input input-bordered w-full"
                        {...register("transfer_fee")}
                        onValueChange={(values) => {
                          const { floatValue } = values;
                          setValue("transfer_fee", floatValue || 0);
                        }}
                      />
          {errors.transfer_fee && <p className="text-red-500 text-sm">Wajib diisi</p>}
        </fieldset>
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">Note</legend>
          <input
            type="text"
            className="input w-full"
            {...register("note")}
          />
          {errors.note && <p className="text-red-500 text-sm">Wajib diisi</p>}
        </fieldset>
      </ModalForm>

      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        loading={loading}
        message={`Are you sure you want to delete bank-account "${selectedBankAccount?.id}"?`}
      />
    </div>
  );
}
