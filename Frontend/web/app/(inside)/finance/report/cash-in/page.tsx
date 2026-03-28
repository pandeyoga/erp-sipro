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

export default function ReportCashinPage() {
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
    watch
  } = useForm<{
    year: string;
    month: string;
  }>({
    defaultValues : {
      year: new Date().getFullYear().toString(), 
      month: (new Date().getMonth() + 1).toString()
    }
  });


  const TableRef = useRef<{ reload: () => void }>(null);

  const handleExport = async () => {
    try {
      const res = await axiosInstance.get(`/finance/report/cash-in/export`, {
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
      link.setAttribute("download", `cash-in-report-${watch("year")+watch("month")}.xlsx`);
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (error) {
      console.error("Gagal export:", error);
    }
  };

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Cash In</h1>
    
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

      <DataTable
        ref={TableRef}
        endpoint={`/finance/report/cash-in`}
        columns={columns}
        tree={true}
        withAction={false}
        withSelect={false}
        reportType="cash-in"
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
