"use client";

import DataTable, { Column } from "@/components/datatable/datatable";
import { useForm } from "react-hook-form";
import { useEffect, useRef, useState } from "react";
import { useParams, useSearchParams } from "next/navigation";
import { convertToRupiah } from "@/lib/utils";
import axiosInstance from "@/lib/axios";

interface Cashflow {
  id: string;
  category: string;
  sub_category: string;
  description: number;
  debit: string;
  credit: string;
  date: string;
  bank_name : string;
  saldo : number;
}


export default function CashflowPage() {
  const columns: Column<Cashflow>[] = [
    { key: "category", label: "Category", sortable: true },
    { key: "sub_category", label: "Sub Category", sortable: true },
    { key: "description", label: "Description", sortable: true },
    { key: "debit", label: "Debit", sortable: true, render: (item) => convertToRupiah(parseFloat(item.debit)), },
    { key: "credit", label: "Credit", sortable: true, render: (item) => convertToRupiah(parseFloat(item.credit)), },
    {
      key: "date",
      label: "Date",
      sortable: true,
      render: (item) => new Date(item.date).toLocaleDateString(),
    },
    { key: "bank_name", label: "Bank Name", sortable: true },
    { key: "saldo", label: "Saldo", sortable: true,render: (item) => convertToRupiah(item.saldo), },
  ];
  

  const {
    register,
    watch,
    formState: { errors },
  } = useForm<{
    start_date : string;
    end_date : string;
  }>({
    defaultValues : {
      start_date: new Date().toISOString().split("T")[0], // "2025-09-16"
      end_date: new Date().toISOString().split("T")[0]
    }
  });

  const [modalMode, setModalMode] = useState<"create" | "edit">("create");
  const [selectedBankAccount, setSelectedBankAccount] =
    useState<Cashflow | null>(null);
  const [loading, setLoading] = useState(false);

  const handleReload = async () => {
    TableRef?.current?.reload();
  };

  const TableRef = useRef<{ reload: () => void }>(null);

  const handleExport = async () => {
    try {
      const res = await axiosInstance.get(
        `/finance/cashflow/export`,
        {
          params: {
            start_date: watch("start_date"),
            end_date: watch("end_date"),
          },
          responseType: "blob", // supaya bisa download file
        }
      );
  
      // buat blob url
      const url = window.URL.createObjectURL(new Blob([res.data]));
      const link = document.createElement("a");
      link.href = url;
  
      // kasih nama file sesuai kebutuhan
      link.setAttribute("download", "cashflow_export.xlsx");
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (error) {
      console.error("Gagal export:", error);
    }
  };

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">
        Cashflow
      </h1>

      <form className="flex items-end gap-4 w-1/2 mb-6">
        <div className="form-control w-full">
          <label className="label font-medium">From</label>
          <input
            type="date"
            {...register("start_date")}
            className="input input-bordered w-full"
            required
          />
        </div>

        <div className="form-control w-full">
          <label className="label font-medium">To</label>
          <input
            type="date"
            {...register("end_date")}
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
        endpoint={`/finance/cashflow`}
        columns={columns}
        filter={
          watch('start_date') && watch('end_date') ?
          {
            start_date : watch('start_date'),
            end_date : watch('end_date')
          } : {}
        }
      />
    </div>
  );
}
