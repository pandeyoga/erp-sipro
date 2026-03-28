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
import { useParams, useSearchParams } from "next/navigation";
import { convertToRupiah } from "@/lib/utils";

interface BankAccount {
  id: string;
  name: string;
  type: string;
  transfer_fee: number;
  amount: number;
  note: string;
  date: string;
}

export default function BankAccountPage() {
  const { id } = useParams() as { id: string };
  const searchParams = useSearchParams();
  const bank = searchParams.get("bank"); // 👉 "Mandiri"
  const columns: Column<BankAccount>[] = [
    { key: "type", label: "Tipe transaksi", sortable: true, render: (item) => (<div>{item.name} - {item.type == 'in' ? 'Kredit': 'Debit' }</div>) },
    { key: "amount", label: "Amount", sortable: true,
      render: (item) => {
        const formatted = convertToRupiah(item.amount);
        if(item.type == 'in') {
          return <div className="text-green-500 font-bold">{formatted}</div>
        }else{
          return <div className="text-red-500 font-bold">-{formatted}</div>
        }
      },
    },
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

  const handleReload = async () => {
    TableRef?.current?.reload();
  };

  const TableRef = useRef<{ reload: () => void }>(null);

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">
        Mutasi Bank {bank}
      </h1>

      <DataTable
        ref={TableRef}
        endpoint={`/finance/bank-account/${id}/transaction`}
        columns={columns}
      />
    </div>
  );
}
