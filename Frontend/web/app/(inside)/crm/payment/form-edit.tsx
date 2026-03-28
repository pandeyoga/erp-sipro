"use client";

import { useForm } from "react-hook-form";
import { useEffect, useState } from "react";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import FileDropzone from "@/components/dropzone";
import { useRouter } from "next/navigation";
import React from "react";

interface KprFormEditProps {
  id: string;
}

interface Bank {
  code: string;
  name: string;
}

const defaultValues = {
  name: "",
  phone: "",
  email: "",
  sp3k_status: "",
  sp3k_bank: "",
  sp3k_code: "",
  sp3k_date: "",
  sp3k_number: "",
  sp3k_expired: "",
  akad_kredit_status: "",
  notes: "",
  checklist_surat_permohonan_akad: false,
  checklist_permohonan_surat_pip_listrik: false,
  checklist_permohonan_surat_pip_jalan: false,
  checklist_permohonan_surat_pip_air: false,
  checklist_surat_permohonan_appraisal: false,
  checklist_permohonan_uji_flpp: false,
  checklist_upload_foto_rumah: false,
  checklist_permohonan_akad_ke_notaris: false,
  checklist_upload_data_debitur_ke_notaris: false,
  checklist_si_pencairan: false,
  checklist_si_notaris: false,
  checklist_si_kyg: false,
  checklist_spk: false,
  checklist_approval_flpp: false,
  checklist_approval_foto_rumah: false,
  checklist_cover_note: false,
  checklist_akta_jual_beli: false,
  checklist_balik_nama_sertifikat: false,
};

const akadChecklist = [
  { key: "checklist_surat_permohonan_akad", label: "Surat Permohonan Akad" },
  {
    key: "checklist_permohonan_surat_pip_listrik",
    label: "Permohonan Surat PIP Listrik",
  },
  {
    key: "checklist_permohonan_surat_pip_jalan",
    label: "Permohonan Surat PIP Jalan",
  },
  {
    key: "checklist_permohonan_surat_pip_air",
    label: "Permohonan Surat PIP Air",
  },
  {
    key: "checklist_surat_permohonan_appraisal",
    label: "Surat Permohonan Appraisal",
  },
  { key: "checklist_permohonan_uji_flpp", label: "Permohonan Uji FLPP" },
  { key: "checklist_upload_foto_rumah", label: "Upload Foto Rumah" },
  {
    key: "checklist_permohonan_akad_ke_notaris",
    label: "Permohonan Akad ke Notaris",
  },
  {
    key: "checklist_upload_data_debitur_ke_notaris",
    label: "Upload Data Debitur ke Notaris",
  },
  { key: "checklist_si_pencairan", label: "SI Pencairan" },
  { key: "checklist_si_notaris", label: "SI Notaris" },
  { key: "checklist_si_kyg", label: "SI KYG" },
  { key: "checklist_spk", label: "SPK" },
  { key: "checklist_approval_flpp", label: "Approval FLPP" },
  { key: "checklist_approval_foto_rumah", label: "Approval Foto Rumah" },
  { key: "checklist_cover_note", label: "Cover Note" },
  { key: "checklist_akta_jual_beli", label: "Akta Jual Beli" },
  { key: "checklist_balik_nama_sertifikat", label: "Balik Nama Sertifikat" },
];

export default function KprFormEdit({ id }: KprFormEditProps) {
  const router = useRouter();
  const { register, handleSubmit, setValue, reset, getValues, watch } =
    useForm<any>({
      defaultValues,
    });
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const res = await axios.get(`/crm/lead-payment/${id}`);
        const data = res.data.data;

        setValue("lead_id", data.id);
        setValue("name", data.name || "");
        setValue("phone", data.phone || "");
        setValue("email", data.email || "");
        setValue("notes", data.notes || "");
        setValue("status", data.status || "");
        setValue("purposed_name_1", data.purposed_name_1 || "");
        setValue("purposed_name_2", data.purposed_name_2 || "");

        // Jika kamu ingin menampilkan sp3k atau akad data (optional)
        setValue("sp3k_status", data.sp3k_status || "");
        setValue("sp3k_document_src", data.sp3k_document || "");
        setValue("sp3k_bank", data.sp3k_bank || "");
        setValue("sp3k_code", data.sp3k_code || "");

        // Pastikan data.sp3k_date ada
        if (data.sp3k_date) {
          const date = new Date(data.sp3k_date);

          // tambah 3 bulan
          date.setMonth(date.getMonth() + 3);

          // format ke YYYY-MM-DD (atau format yang kamu butuhkan)
          const formatted = date.toISOString().split("T")[0];

          setValue("sp3k_expired", formatted);
          setValue("sp3k_date", data.sp3k_date);
        } else {
          setValue("sp3k_expired", "");
          setValue("sp3k_date", "");
        }

        setValue("sp3k_number", data.sp3k_number || "");

        setValue("akad_kredit_status", data.akad_kredit_status || "");
        setValue(
          "akad_kredit_penandatanganan_document_src",
          data.akad_kredit_penandatanganan_document || ""
        );

        // Optional: bisa juga set status atau duration jika dibutuhkan di form
        setValue("status", data.status || "");
        setValue("duration", data.duration || "");

        if (Array.isArray(data.checklists)) {
          data.checklists.forEach((item: any) => {
            setValue(item.key, item.checked);
          });
        }
      } catch (err) {
        toast.error("Gagal memuat data");
      }
    };
    fetchData();
  }, [id, reset]);

  const onSubmit = async (data: any) => {
    const formData = new FormData();
    for (const key in data) {
      const value = data[key];
      if (typeof value === "boolean") {
        formData.append(key, value ? "1" : "0");
      } else {
        formData.append(
          key,
          value !== undefined && value !== null ? value : "-"
        );
      }
    }
    // ubah FormData jadi 1 string x-www-form-urlencoded
    const formatted = Array.from(formData.entries())
      .map(([key, value]) => `${key}: ${value}`)
      .join("\n");
    console.log(getValues());

    try {
      await axios.post(`/crm/lead-payment/${id}`, formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      toast.success("Pembayaran berhasil diperbarui!");
      router.push("/crm/payment");
    } catch (err) {
      toast.error("Gagal menyimpan data");
    }
  };

  const [listBank, setListBank] = useState<Bank[]>([]);

  useEffect(() => {
    const fetchBanks = async () => {
      try {
        const res = await axios.get("/crm/lead-payment/bank-list");
        if (res.data?.success) {
          setListBank(res.data.data);
        }
      } catch (error) {
        console.error("Gagal mengambil daftar bank:", error);
      }
    };

    fetchBanks();
  }, []);

  useEffect(() => {
    if (watch("sp3k_date")) {
      const date = new Date(watch("sp3k_date"));

      // tambah 3 bulan
      date.setMonth(date.getMonth() + 3);

      // format ke YYYY-MM-DD (atau format yang kamu butuhkan)
      const formatted = date.toISOString().split("T")[0];

      setValue("sp3k_expired", formatted);
    }
  }, [watch("sp3k_date")]);

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      <h1 className="text-xl font-bold">Update KPR</h1>

      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-md font-semibold">Document Konsumen</legend>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {/* Nama */}
          <div className="form-control">
            <label className="label">
              <span className="label-text">Nama</span>
            </label>
            <input
              {...register("name")}
              placeholder="Nama Konsumen"
              className="input input-bordered w-full"
              readOnly
            />
          </div>

          {/* No Telepon */}
          <div className="form-control">
            <label className="label">
              <span className="label-text">No Telepon</span>
            </label>
            <input
              {...register("phone")}
              placeholder="No Telepon"
              className="input input-bordered w-full"
              readOnly
            />
          </div>

          {/* Email */}
          <div className="form-control">
            <label className="label">
              <span className="label-text">Email</span>
            </label>
            <input
              {...register("email")}
              placeholder="Email"
              className="input input-bordered w-full"
              readOnly
            />
          </div>
        </div>
      </fieldset>

      {getValues("status") != "cash" && (
        <React.Fragment>
          <fieldset className="border border-base-300 rounded-xl p-6">
            <legend className="text-lg font-semibold px-2">
              Pengajuan Nama
            </legend>
            <div className="form-control mb-4">
              <label>Pengajuan Nama 1</label>
              <input
                {...register("proposed_name_1")}
                placeholder="Pengajuan Nama 1"
                className="input input-bordered w-full"
              />
            </div>
            <div className="form-control">
              <label>Pengajuan Nama 2</label>
              <input
                {...register("proposed_name_2")}
                placeholder="Pengajuan Nama 2"
                className="input input-bordered w-full"
              />
            </div>
          </fieldset>
          <fieldset className="border border-base-300 rounded-xl p-6">
            <legend className="text-md font-semibold">SP3K</legend>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {/* Status SP3K */}
              <div className="form-control flex flex-col col-span-2">
                <label className="label">
                  <span className="label-text">Status SP3K</span>
                </label>
                <select
                  {...register("sp3k_status")}
                  className="select select-bordered"
                >
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                </select>
              </div>

              {/* Dokumen Upload */}
              <div className="form-control col-span-2">
                {/* <label className="label"><span className="label-text">SP3K Document</span></label> */}
                <FileDropzone
                  name="sp3k_document"
                  label="SP3K Document"
                  setValue={setValue}
                  accept={{
                    "application/pdf": [".pdf"],
                  }}
                  initialPreviewSrc={
                    typeof watch("sp3k_document_src") === "string"
                      ? watch("sp3k_document_src")
                      : ""
                  }
                />
              </div>

              {/* Kode SP3K */}
              <div className="form-control flex flex-col">
                <label className="label">
                  <span className="label-text">Kode SP3K</span>
                </label>
                <input
                  {...register("sp3k_code")}
                  placeholder="Kode SP3K"
                  className="input input-bordered"
                />
              </div>

              {/* Tanggal SP3K */}
              <div className="form-control flex flex-col">
                <label className="label">
                  <span className="label-text">Tanggal SP3K</span>
                </label>
                <input
                  type="date"
                  {...register("sp3k_date")}
                  className="input input-bordered"
                />
              </div>

              {/* Nomor SP3K */}
              <div className="form-control flex flex-col">
                <label className="label">
                  <span className="label-text">Nomor SP3K</span>
                </label>
                <input
                  {...register("sp3k_number")}
                  placeholder="Nomor SP3K"
                  className="input input-bordered"
                />
              </div>

              {/* Tanggal Expired SP3K */}
              <div className="form-control flex flex-col">
                <label className="label">
                  <span className="label-text">Tanggal Berakhir SP3K</span>
                </label>
                <input
                  type="date"
                  {...register("sp3k_expired")}
                  className="input input-bordered disabled"
                  readOnly
                />
              </div>

              {/* Bank SP3K */}
              <div className="form-control flex flex-col">
                <label className="label">
                  <span className="label-text">Bank SP3K</span>
                </label>
                <select
                  {...register("sp3k_bank")}
                  className="select select-bordered"
                >
                  <option value="">Pilih Bank SP3K</option>
                  {listBank.map((bank, i) => (
                    <option key={i} value={bank.code}>
                      {bank.name}
                    </option>
                  ))}
                </select>
              </div>
            </div>
          </fieldset>

          <fieldset className="border border-base-300 rounded-xl p-6">
            <legend className="text-md font-semibold">Akad Kredit</legend>
            <div className="grid grid-cols-1  gap-4">
              {/* Status Akad Kredit */}
              <div className="form-control flex flex-col">
                <label className="label">
                  <span className="label-text">Status Akad Kredit</span>
                </label>
                <select
                  {...register("akad_kredit_status")}
                  className="select select-bordered"
                >
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                </select>
              </div>

              {/* Dokumen Akad */}
              <div className="form-control">
                {/* <label className="label"><span className="label-text">Dokumen Akad</span></label> */}
                <FileDropzone
                  name="akad_kredit_penandatanganan_document"
                  label="Upload Foto Dokumen Akad"
                  setValue={setValue}
                  accept={{
                    "application/pdf": [".jpg", ".png", "jpeg"],
                  }}
                  initialPreviewSrc={
                    typeof watch("akad_kredit_penandatanganan_document_src") ===
                    "string"
                      ? watch("akad_kredit_penandatanganan_document_src")
                      : ""
                  }
                />
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 mt-4">
              {akadChecklist.map((item) => (
                <label key={item.key} className="flex gap-2 items-center">
                  <input
                    type="checkbox"
                    {...register(`${item.key}`)}
                    className="checkbox checkbox-sm"
                  />
                  <span>{item.label}</span>
                </label>
              ))}
            </div>
          </fieldset>
        </React.Fragment>
      )}

      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-md font-semibold">Notes</legend>
        <textarea
          {...register("notes")}
          placeholder="Catatan Tambahan"
          className="textarea textarea-bordered w-full"
        />
      </fieldset>

      <div className="flex justify-between">
        <button type="button" onClick={() => router.back()} className="btn">
          Back
        </button>
        <button type="submit" className="btn btn-primary">
          Update
        </button>
      </div>
    </form>
  );
}
