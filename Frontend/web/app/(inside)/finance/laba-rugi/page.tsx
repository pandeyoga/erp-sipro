"use client";

import DataTable, { Column } from "@/components/datatable/datatable";
import { useForm } from "react-hook-form";
import { useEffect, useRef, useState } from "react";
import { useParams, useSearchParams } from "next/navigation";
import { convertToRupiah } from "@/lib/utils";
import axiosInstance from "@/lib/axios";

interface Cashflow {
  id: string;
  name: string;
  value: string;
}

export default function CashflowPage() {
  const columns: Column<Cashflow>[] = [
    { key: "name", label: "Name", sortable: true },
    { key: 'value', label: "Total", sortable: true, render : (item)=>{
      if(!item.value ) return "-";
      const formatted = convertToRupiah(parseFloat(item.value));
      return (<div>{formatted}</div>)
    } },
  ];

  const {
    register,
    watch,
    formState: { errors },
  } = useForm<{
    year: string;
    month: string;
  }>({
    defaultValues : {
      year: new Date().getFullYear().toString(), 
      month: (new Date().getMonth() + 1).toString()
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
      const res = await axiosInstance.get(`/finance/report/neraca/export`, {
        params: {
          year: watch("year"),
          month: watch("month"),
        },
        responseType: "blob", // supaya bisa download file
      });

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
      <h1 className="text-xl font-bold text-gray-600 mb-6">Laba Rugi</h1>

      <form className="flex items-end gap-4 w-1/2 mb-6">
        <div className="form-control w-full">
          <label className="label font-medium">Month</label>
          <select
            {...register("month")}
            className="select select-bordered w-full"
            required
          >
            <option value="">-- Pilih Bulan --</option>
            <option value="1">Januari</option>
            <option value="2">Februari</option>
            <option value="3">Maret</option>
            <option value="4">April</option>
            <option value="5">Mei</option>
            <option value="6">Juni</option>
            <option value="7">Juli</option>
            <option value="8">Agustus</option>
            <option value="9">September</option>
            <option value="10">Oktober</option>
            <option value="11">November</option>
            <option value="12">Desember</option>
          </select>
        </div>

        <div className="form-control w-full">
          <label className="label font-medium">Year</label>
          <select
            {...register("year")}
            className="select select-bordered w-full"
            required
          >
            <option value="">-- Pilih Tahun --</option>
            {Array.from({ length: 20 }, (_, i) => {
              const year = new Date().getFullYear() - i;
              return (
                <option key={year} value={year}>
                  {year}
                </option>
              );
            })}
          </select>
        </div>

        <button
          type="button"
          onClick={handleExport}
          className="btn btn-warning"
        >
          Export
        </button>
      </form>
      {/* <FooterSummary /> */}

      <DataTable
        ref={TableRef}
        endpoint={`/finance/report/laba-rugi`}
        columns={columns}
        tree={true}
        withAction={false}
        withSelect={false}
        filter={
          watch('year') && watch('month') ?
          {
            year : parseInt(watch('year')),
            month : parseInt(watch('month'))
          } : {}
        }
      />
    </div>
  );
}

function FooterSummary() {
  const labaRugiSummary = {
    total_pendapatan: 0,
    total_biaya_pendapatan: 0,
    total_biaya_operasional: 0,
    total_pendapatan_lainnya: 0,
    total_biaya_lainnya: 0,
    total_tarikan: 0,
    laba_kotor: 0,
    laba_rugi: 0,
  };
  return (
    <footer className="bg-gray-100 p-4 text-gray-700 mt-4">
      <h4 className="font-semibold mb-2">Ringkasan Laba Rugi</h4>
      <div className="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
        <div>
          <span className="font-bold">Total Pendapatan:</span>{" "}
          {labaRugiSummary.total_pendapatan}
        </div>
        <div>
          <span className="font-bold">Biaya Pendapatan:</span>{" "}
          {labaRugiSummary.total_biaya_pendapatan}
        </div>
        <div>
          <span className="font-bold">Biaya Operasional:</span>{" "}
          {labaRugiSummary.total_biaya_operasional}
        </div>
        <div>
          <span className="font-bold">Pendapatan Lainnya:</span>{" "}
          {labaRugiSummary.total_pendapatan_lainnya}
        </div>
        <div>
          <span className="font-bold">Biaya Lainnya:</span>{" "}
          {labaRugiSummary.total_biaya_lainnya}
        </div>
        <div>
          <span className="font-bold">Total Tarikan:</span>{" "}
          {labaRugiSummary.total_tarikan}
        </div>
        <div>
          <span className="font-bold">Laba Kotor:</span>{" "}
          {labaRugiSummary.laba_kotor}
        </div>
        <div>
          <span className="font-bold">Laba Rugi:</span>{" "}
          {labaRugiSummary.laba_rugi}
        </div>
      </div>
    </footer>
  );
}
