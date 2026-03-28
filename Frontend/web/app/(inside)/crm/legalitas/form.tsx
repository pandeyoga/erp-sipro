"use client";

import { useForm, useWatch } from "react-hook-form";
import FileDropzone from "@/components/dropzone";
import { useRouter } from "next/navigation";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import { useEffect, useState } from "react";
import Select from "react-select";

export default function LegalitasAkhirForm({ mode, id }: { mode: 'create' | 'edit', id?: string }) {
  const router = useRouter();
  const { register, handleSubmit, setValue, reset, watch } = useForm();
  const [loading, setLoading] = useState(false);
  

  // Ambil data untuk edit
  useEffect(() => {
    if (mode === "edit" && id) {
      const fetchData = async () => {
        try {
          const res = await axios.get(`/crm/final-legality/${id}`);
          const data = res.data.data;
          reset({
            name: data.name,
            phone: data.phone,
            email: data.email,
            bast_date: data.bast_date,
            retention_start_date: data.retention_start_date,
            retention_end_date: data.retention_end_date,
            retention_document_src : data.retention_document,
            retention_hanover_photo_src : data.retention_hanover_photo,
            bast_document_src: data.bast_document,
            bast_hanover_photo_src: data.bast_hanover_photo,
            notes: data.notes,
          });
          setValue("lead_id", data.lead_id); // untuk tracking update jika ingin dikirim
        } catch (err) {
          toast.error("Gagal mengambil data");
        }
      };
      fetchData();
    }
  }, [id, mode, reset, setValue]);

  const onSubmit = async (data: any) => {
    try {
      const formData = new FormData();
      for (const key in data) {
        if (data[key]) {
          formData.append(key, data[key]);
        }
      }

      setLoading(true);
      if (mode === 'edit' && id) {
        await axios.post(`/crm/final-legality/${id}`, formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        toast.success("Legalitas berhasil diperbarui");
      } else {
        await axios.post("/crm/final-legality", formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        toast.success("Legalitas berhasil ditambahkan");
      }

      router.push("/crm/legalitas");
    } catch (error) {
      toast.error("Gagal menyimpan data");
    } finally {
      setLoading(false);
    }
  };

  // Lead select logic
  const [paymentLeads, setPaymentLeads] = useState<{ id: string; name: string; phone?: string; email?: string }[]>([]);
  const [loadingLeads, setLoadingLeads] = useState(true);
  const selectedLeadId = watch("lead_id");

  const [searchTermLead, setSearchTermLead] = useState("");
 
  useEffect(() => {
    if (!searchTermLead) return;
  
    const delay = setTimeout(() => {
      axios
        .get(`/crm/final-legality/get-completed-payment-lead?search=${searchTermLead}`)
        .then((res) => {
          const options = res.data.data.map((p: any) => ({label : p.name, value: p.id, ...p}))
          setPaymentLeads(options)
        })
        .catch(() => console.error("Gagal ambil data"));
    }, 400);
  
    return () => clearTimeout(delay);
  }, [searchTermLead]);

  useEffect(() => {
    const fetchLeads = async () => {
      try {
        const res = await axios.get("/crm/final-legality/get-completed-payment-lead");
        setPaymentLeads(res.data.data.map((p: any) => ({label : p.name, value: p.id, ...p})) || []);
      } catch (err) {
        console.error("Gagal memuat daftar lead", err);
      } finally {
        setLoadingLeads(false);
      }
    };
    fetchLeads();
  }, []);

  useEffect(() => {
    if (!selectedLeadId || mode === "edit") return;
    const selected = paymentLeads.find((lead) => lead.id === selectedLeadId);
    
    if (selected) {
      setValue("name", selected.name || "");
      setValue("phone", selected.phone || "");
      setValue("email", selected.email || "");
    }
  }, [selectedLeadId, paymentLeads, setValue, mode]);

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      {/* Lead Info */}
      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Lead Information</legend>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Lead</span>
            </label>
            {mode === "edit" ? (
              <input {...register("name")} readOnly className="input input-bordered w-full" />
            ) : loadingLeads ? (
              <div className="skeleton h-12 w-full rounded"></div>
            ) : (
              <Select
                options={paymentLeads}
                placeholder="Select or search Prospect Lead"
                onChange={(value : any) => {
                  setValue("lead_id", value.value);
                }}
                onInputChange={(value, { action }) => {
                  if (action === "input-change") {
                    setSearchTermLead(value); 
                  }
                }}
                className="w-full"
                styles={{
                  control: (base) => ({
                    ...base,
                    borderColor: "#d1d5db", // tailwind: border-gray-300
                    borderRadius: "0.5rem",
                    padding: "2px",
                  }),
                }}
              />
            )}
          </div>

          {/* <div className="form-control mt-4">
            <label className="label"><span className="label-text">No. Telepon</span></label>
            <input {...register("phone")} className="input input-bordered w-full" readOnly />
          </div>
          <div className="form-control mt-4">
            <label className="label"><span className="label-text">Email</span></label>
            <input {...register("email")} className="input input-bordered w-full" readOnly />
          </div> */}
        </div>
      </fieldset>

      {/* BAST Section */}
      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-md font-semibold">BAST</legend>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="form-control">
            <FileDropzone name="bast_document" label="Document BAST" setValue={setValue} 
            accept={{
              'application/pdf': ['.pdf']
            }}
            initialPreviewSrc={
              mode === 'edit' && typeof watch('bast_document_src') === 'string'
                ? watch('bast_document_src')
                : ''
            }
            />
          </div>
          <div className="form-control">
            <FileDropzone name="bast_hanover_photo" label="Foto Penyerahan BAST" setValue={setValue} 
            accept={{
              'image/*': ['.png', '.jpg', '.jpeg'],
            }}
            initialPreviewSrc={
              mode === 'edit' && typeof watch('bast_hanover_photo_src') === 'string'
                ? watch('bast_hanover_photo_src')
                : ''
            }
            />
          </div>
          <div className="form-control col-span-2">
            <label className="label"><span className="label-text">Tanggal BAST</span></label>
            <input type="date" {...register("bast_date")} className="input input-bordered w-full" />
          </div>
        </div>
      </fieldset>

      {/* Retensi Section */}
      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-md font-semibold">RETENSI</legend>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="form-control">
            <FileDropzone name="retention_document" label="Document Retensi" setValue={setValue} 
            accept={{
              'application/pdf': ['.pdf']
            }}
            initialPreviewSrc={
              mode === 'edit' && typeof watch('retention_document_src') === 'string'
                ? watch('retention_document_src')
                : ''
            }/>
          </div>
          <div className="form-control">
            <FileDropzone name="retention_hanover_photo" label="Foto Penyerahan Retensi" setValue={setValue} 
            accept={{
              'image/*': ['.png', '.jpg', '.jpeg'],
            }}
            initialPreviewSrc={
              mode === 'edit' && typeof watch('retention_hanover_photo_src') === 'string'
                ? watch('retention_hanover_photo_src')
                : ''
            }
            />
          </div>
          <div className="form-control col-span-2">
            <label className="label"><span className="label-text">Tanggal Mulai Retensi</span></label>
            <input type="date" {...register("retention_start_date")} className="input input-bordered w-full" />
          </div>
        </div>
      </fieldset>

      {/* Notes */}
      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-md font-semibold">Notes</legend>
        <textarea {...register("notes")} placeholder="Input text" className="textarea textarea-bordered w-full" />
      </fieldset>

      <div className="flex justify-between">
        <button type="button" onClick={() => router.back()} className="btn">Back</button>
        <button type="submit" className="btn btn-primary" disabled={loading}>
          {loading && <span className="loading loading-spinner"></span>}
          {mode === 'edit' ? 'Update' : 'Add'}
        </button>
      </div>
    </form>
  );
}
