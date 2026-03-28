"use client";

import { useForm, useWatch } from "react-hook-form";
import FileDropzone from "@/components/dropzone";
import { useRouter } from "next/navigation";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import { useEffect, useRef, useState } from "react";
import { Lead, SubContractor } from "../construction/form";

export default function LegalitasAkhirForm({
  mode,
  id,
}: {
  mode: "create" | "edit";
  id?: string;
}) {
  const router = useRouter();
  const { register, handleSubmit, setValue, reset, watch } = useForm();
  const [loading, setLoading] = useState(false);
  const [leads, setLeads] = useState<Lead[]>([])
  

  // GET: Reserved Leads for create
  

  const fetched = useRef(false);
  useEffect(() => {
    if (mode !== 'create' || fetched.current) return;
    fetched.current = true;
  
    axios.get('/property/retention/reserved-lead')
      .then((res) => {
        setLeads(res.data.data);
      })
      .catch(() => {
        toast.error('Belum ada reserved lead');
      });
  
  }, [mode]);
  

  // Ambil data untuk edit
  useEffect(() => {
    if (mode === "edit" && id) {
      const fetchData = async () => {
        try {
          const res = await axios.get(`/property/retention/${id}`);
          const data = res.data.data;
          reset({
            lead_name: data.lead_name,
            phone: data.phone,
            email: data.email,
            bast_date: data.bast_date,
            retention_start_date: data.retention_start_date,
            retention_end_date: data.retention_end_date,
            retention_document: data.retention_document_src,
            retention_handover_photo: data.retention_hanover_photo_src,
            bast_document: data.bast_document_src,
            bast_handover_photo: data.bast_hanover_photo_src,
            notes: data.notes,
          });
          setValue("lead_id", data.lead_id); // untuk tracking update jika ingin dikirim
        } catch (err) {
          toast.error("Gagal mengambil data retensi");
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
          if(key === 'case_pictures' || key === 'case_documentations'){
            data[key].forEach((file: any) => {
              formData.append(`${key}[]`, file);
            });
          }else{
            formData.append(key, data[key]);
          }
        }
      }

      setLoading(true);
      if (mode === "edit" && id) {
        await axios.post(`/property/retention/${id}`, formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        toast.success("Legalitas berhasil diperbarui");
      } else {
        await axios.post("/property/retention", formData, {
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
  const [paymentLeads, setPaymentLeads] = useState<
    { id: string; name: string; phone?: string; email?: string }[]
  >([]);
  const [loadingLeads, setLoadingLeads] = useState(true);
  const [subcontractors, setSubcontractors] = useState<SubContractor[]>([])
  const selectedLeadId = watch("lead_id");


  // GET: Sub Contractors
  useEffect(() => {
    axios
      .get("/property/construction/sub-contractors")
      .then((res) => setSubcontractors(res.data.data))
      .catch(() => toast.error("Gagal mengambil data kontraktor"));
  }, []);

  useEffect(() => {
    if (!selectedLeadId || mode === "edit") return;
    const selected = leads.find((lead) => lead.lead_id === selectedLeadId);
    if (selected) {
      setValue("name", selected.contact_name || "");
      setValue("project_name", selected.project_name || "");
      setValue("cluster_name", selected.cluster_name || "");
      setValue("unit_type", selected.unit_type || "");
      setValue("unit_number", selected.unit_number || "");
      setValue("unit_price", selected.unit_price || "");
    }
  }, [selectedLeadId, paymentLeads, setValue, mode]);

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      {/* FORM CREATE */}
      {mode === 'create' && (
        <>
          <fieldset className="border border-base-300 rounded-xl p-6 space-y-4">
            <legend className="text-lg font-semibold px-2">Informasi Konstruksi</legend>

            {/* Lead */}
            <div className="form-control">
              <label className="label">Consument</label>
              <select {...register('lead_id')} className="select select-bordered w-full">
                <option value="">-- Pilih Lead --</option>
                {leads.map((lead) => (
                  <option key={lead.lead_id} value={lead.lead_id}>
                    {lead.contact_name}
                  </option>
                ))}
              </select>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="form-control">
                <label className="label">Project</label>
                <input type="text" {...register('project_name')} className="input input-bordered w-full" readOnly />
              </div>

              <div className="form-control">
                <label className="label">Cluster</label>
                <input type="text" {...register('cluster_name')} className="input input-bordered w-full" readOnly />
              </div>

              <div className="form-control">
                <label className="label">Unit Type</label>
                <input type="text" {...register('unit_type')} className="input input-bordered w-full" readOnly />
              </div>

              <div className="form-control">
                <label className="label">Unit Number</label>
                <input type="text" {...register('unit_number')} className="input input-bordered w-full" readOnly />
              </div>

              <div className="form-control">
                <label className="label">Unit Price</label>
                <input
                  type="text"
                  {...register('unit_price')}
                  className="input input-bordered w-full"
                  readOnly
                />
              </div>
            </div>

            <div className="form-control">
              <FileDropzone multiple name={`case_pictures`} label={`Upload Retention`} setValue={setValue} />
            </div>

            <div className="form-control">
              <label className="label">
                <span className="label-text">Deskripsi Kasus Retensi</span>
              </label>
              <textarea {...register("description")} placeholder="Input text" className="textarea textarea-bordered w-full" />
            </div>

            {/* Dates */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="form-control">
                <label className="label">Tanggal Retensi</label>
                <input type="date" {...register('case_date')} className="input input-bordered w-full" />
              </div>
              <div className="form-control">
                <label className="label">Estimasi Selesai</label>
                <input type="date" {...register('estimated_resolved_day')} className="input input-bordered w-full" />
              </div>
            </div>
            
            {/* Sub Contractor */}
            <div className="form-control">
              <label className="label">Subkontraktor</label>
              <select {...register('sub_contractor_id')} className="select select-bordered w-full">
                <option value="">-- Pilih Sub Kontraktor --</option>
                {subcontractors.map((sub) => (
                  <option key={sub.id} value={sub.id}>
                    {sub.name}
                  </option>
                ))}
              </select>
            </div>
            {/* Notes */}
            <div className="form-control">
              <label className="label">
                <span className="label-text">Notes</span>
              </label>
              <textarea {...register("notes")} placeholder="Input text" className="textarea textarea-bordered w-full" />
            </div>
          </fieldset>
          
        </>
      )}

      {/* FORM EDIT: Update Progress */}
      {mode === 'edit' && (
        <>
            <fieldset className="border border-base-300 rounded-xl p-6 space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-1 gap-4 ">
              <div className="form-control">
                <label className="label capitalize">Status</label>
                <select {...register(`status`)} className="select select-bordered w-full">
                  <option value="not_started">Belum Mulai</option>
                  <option value="in_progress">Sedang Berjalan</option>
                  <option value="completed">Selesai</option>
                </select>
              </div>
              <div className="form-control">
                <FileDropzone multiple name={`case_documentations`} label={`Dokumentasi Kasus`} setValue={setValue} />
              </div>
            </div>
            </fieldset>

            <div className="form-control mt-4">
              <label className="label">Catatan</label>
              <textarea {...register('notes')} className="textarea textarea-bordered w-full" placeholder="Opsional" />
            </div>
        </>
      )}
      <div className="flex justify-between">
        <button type="button" onClick={() => router.back()} className="btn">
          Back
        </button>
        <button type="submit" className="btn btn-primary" disabled={loading}>
          {loading && <span className="loading loading-spinner"></span>}
          {mode === "edit" ? "Update" : "Add"}
        </button>
      </div>
    </form>
  );
}
