"use client";

import { useForm, useWatch } from "react-hook-form";
import FileDropzone from "@/components/dropzone";
import { useRouter } from "next/navigation";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import { useEffect, useState } from "react";

export type Lead = {
  lead_id: string;
  unit_property_id: string;
  contact_name: string;
  project_name: string;
  cluster_name: string;
  unit_type: string;
  unit_number: string;
  unit_price: number | null;
};

export type SubContractor = { id: string; name: string };

export default function ConstructionForm({
  mode,
  id,
}: {
  mode: "create" | "edit";
  id?: string;
}) {
  const router = useRouter();
  const { register, handleSubmit, setValue, getValues, reset, watch } = useForm();
  const [loading, setLoading] = useState(false);
  const [projects, setProjects] = useState([]);
  const [clusters, setClusters] = useState([]);
  const [unit_types, setUnitTypes] = useState([]);
  const [unit_properties, setUnitProperties] = useState([]);
  const [subcontractors, setSubcontractors] = useState<SubContractor[]>([]);

  // GET: project list
  useEffect(() => {
    axios
      .get("/property/construction/project-lists")
      .then((res) => setProjects(res.data.data))
      .catch(() => toast.error("Gagal mengambil project"));
  }, [mode]);

  const project_id = watch("project_id");
  const cluster_id = watch("cluster_id");
  const unit_type_id = watch("unit_type_id");

  // GET: clusters list
  useEffect(() => {
    if (!project_id) return;
    axios
      .get(`/property/construction/cluster-lists/${project_id}`)
      .then((res) => setClusters(res.data.data))
      .catch(() => toast.error("Gagal mengambil cluster"));
  }, [mode, project_id]);

  // GET: Unit Types list
  useEffect(() => {
    axios
      .get(`/property/construction/unit-type-lists`)
      .then((res) => setUnitTypes(res.data.data))
      .catch(() => toast.error("Gagal mengambil unit type"));
  }, [mode]);

  // GET: unit property list
  useEffect(() => {
    if (!(project_id && cluster_id && unit_type_id)) return;
    axios
      .get(
        `/property/construction/property-lists/${project_id}/${cluster_id}/${unit_type_id}`
      )
      .then((res) => setUnitProperties(res.data.data))
      .catch(() => toast.error("Gagal mengambil cluster"));
  }, [mode, project_id, cluster_id, unit_type_id]);

  // GET: Sub Contractors
  useEffect(() => {
    axios
      .get("/property/construction/sub-contractors")
      .then((res) => setSubcontractors(res.data.data))
      .catch(() => toast.error("Gagal mengambil data kontraktor"));
  }, []);

  // GET: Detail for edit
  useEffect(() => {
    if (mode === "edit" && id) {
      axios
        .get(`/property/construction/${id}`)
        .then((res) => {
          const data = res.data.data;
          const initial = {
            construction_notes : data.construction_notes,
            status_pondasi: data.construction_phases.find(
              (p: any) => p.construction_phase === "pondasi"
            )?.status,
            foto_pondasi: data.construction_phases.find(
              (p: any) => p.construction_phase === "pondasi"
            )?.documentation,
            status_naik_bata: data.construction_phases.find(
              (p: any) => p.construction_phase === "naik_bata"
            )?.status,
            foto_naik_bata: data.construction_phases.find(
              (p: any) => p.construction_phase === "naik_bata"
            )?.documentation,

            status_naik_atap: data.construction_phases.find(
              (p: any) => p.construction_phase === "naik_atap"
            )?.status,
            foto_naik_atap: data.construction_phases.find(
              (p: any) => p.construction_phase === "naik_atap"
            )?.documentation,

            status_plester_aci: data.construction_phases.find(
              (p: any) => p.construction_phase === "plester_aci"
            )?.status,
            foto_plester_aci: data.construction_phases.find(
              (p: any) => p.construction_phase === "plester_aci"
            )?.documentation,

            status_keramik_cat: data.construction_phases.find(
              (p: any) => p.construction_phase === "keramik_cat"
            )?.status,
            foto_keramik_cat: data.construction_phases.find(
              (p: any) => p.construction_phase === "keramik_cat"
            )?.documentation,

            status_finishing: data.construction_phases.find(
              (p: any) => p.construction_phase === "finishing"
            )?.status,
            foto_finishing: data.construction_phases.find(
              (p: any) => p.construction_phase === "finishing"
            )?.documentation,

            notes: data.notes,
          };
          reset(initial);
        })
        .catch(() => toast.error("Gagal memuat data konstruksi"));
    }
  }, [id, mode, reset]);

  const onSubmit = async (data: any) => {
    try {
      const formData = new FormData();
      for (const key in data) {
        if (data[key]) formData.append(key, data[key]);
      }

      setLoading(true);

      if (mode === "edit" && id) {
        await axios.post(`/property/construction/${id}`, formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        toast.success("Konstruksi berhasil diperbarui");
      } else {
        await axios.post(
          "/property/construction",
          formData,
          {
            headers: { "Content-Type": "multipart/form-data" },
          }
        );
        toast.success("Konstruksi berhasil ditambahkan");
      }

      router.push("/property/construction");
    } catch (error) {
      toast.error("Gagal menyimpan data");
    } finally {
      setLoading(false);
    }
  };

  const phases = [
    "pondasi",
    "naik_bata",
    "naik_atap",
    "plester_aci",
    "keramik_cat",
    "finishing",
  ]

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      {/* FORM CREATE */}
      {mode === "create" && (
        <>
          <fieldset className="border border-base-300 rounded-xl p-6 space-y-4">
            <legend className="text-lg font-semibold px-2">
              Informasi Konstruksi
            </legend>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="form-control">
                <label className="label">Project</label>
                <select
                  {...register("project_id")}
                  className="select select-bordered w-full"
                >
                  <option value="">-- Pilih --</option>
                  {projects.map((opt: any) => (
                    <option key={opt.id} value={opt.id}>
                      {opt.name}
                    </option>
                  ))}
                </select>
              </div>

              <div className="form-control">
                <label className="label">Cluster</label>
                <select
                  {...register("cluster_id")}
                  className="select select-bordered w-full"
                >
                  <option value="">-- Pilih --</option>
                  {clusters.map((opt: any) => (
                    <option key={opt.id} value={opt.id}>
                      {opt.name}
                    </option>
                  ))}
                </select>
              </div>

              <div className="form-control">
                <label className="label">Unit Type</label>
                <select
                  {...register("unit_type_id")}
                  className="select select-bordered w-full"
                >
                  <option value="">-- Pilih --</option>
                  {unit_types.map((opt: any) => (
                    <option key={opt.id} value={opt.id}>
                      {opt.type}
                    </option>
                  ))}
                </select>
              </div>

              <div className="form-control">
                <label className="label">Unit Property</label>
                <select
                  {...register("property_unit_id")}
                  className="select select-bordered w-full"
                >
                  <option value="">-- Pilih --</option>
                  {unit_properties.map((opt: any) => (
                    <option key={opt.id} value={opt.id}>
                      {opt.unit_number}
                    </option>
                  ))}
                </select>
              </div>
            </div>

            {/* Sub Contractor */}
            <div className="form-control">
              <label className="label">Subkontraktor</label>
              <select
                {...register("sub_contractor_id")}
                className="select select-bordered w-full"
              >
                <option value="">-- Pilih Sub Kontraktor --</option>
                {subcontractors.map((sub) => (
                  <option key={sub.id} value={sub.id}>
                    {sub.name}
                  </option>
                ))}
              </select>
            </div>

            {/* Dates */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="form-control">
                <label className="label">Tanggal Mulai</label>
                <input
                  type="date"
                  {...register("start_date")}
                  className="input input-bordered w-full"
                />
              </div>
              <div className="form-control">
                <label className="label">Estimasi Selesai</label>
                <input
                  type="date"
                  {...register("estimated_end_date")}
                  className="input input-bordered w-full"
                />
              </div>
            </div>
            <div className="form-control">
              <FileDropzone
                name={`spk`}
                label={`SPK`}
                accept={{'application/pdf': ['.pdf']}}
                setValue={setValue}
                initialPreviewSrc={process.env.NEXT_PUBLIC_BE_URL+"/"+getValues(`spk`)}
              />
            </div>
            {/* Notes */}
            <div className="form-control">
              <label className="label">
                <span className="label-text">Notes</span>
              </label>
              <textarea
                {...register("notes")}
                placeholder="Input text"
                className="textarea textarea-bordered w-full"
              />
            </div>
            {/* <div className="form-control">
              <label className="label">
                <span className="label-text">Lead Notes</span>
              </label>
              <textarea
                {...register("construction_notes")}
                placeholder="Input text"
                readOnly
                className="textarea textarea-bordered w-full"
              />
            </div> */}
            
          </fieldset>
        </>
      )}

      {/* FORM EDIT: Update Progress */}
      {mode === "edit" && (
        <>
          <div className="form-control mb-4">
              <label className="label">
                <span className="label-text">Lead Notes</span>
              </label>
              <textarea
                {...register("construction_notes")}
                placeholder="Input text"
                readOnly
                className="textarea textarea-bordered w-full"
              />
            </div>
          {phases.map((phase, index) => (
            <fieldset
              key={phase}
              className="border border-base-300 rounded-xl p-6 space-y-4"
            >
              <legend className="text-lg font-semibold px-2 capitalize">
                {index+1}. {phase.replace(/_/g, " ")}
              </legend>
              {
                getValues(`status_${phases[index-1 == -1 ? 0 : index-1]}`) == 'completed' || index == 0? (
                  <div
                    key={phase}
                    className="grid grid-cols-1 md:grid-cols-1 gap-4 "
                  >
                    <div className="form-control">
                      <label className="label capitalize">
                        Status {phase.replace(/_/g, " ")}
                      </label>
                      <select
                        {...register(`status_${phase}`)}
                        className="select select-bordered w-full"
                      >
                        <option value="not_started">Belum Mulai</option>
                        <option value="in_progress">Sedang Berjalan</option>
                        <option value="completed">Selesai</option>
                      </select>
                    </div>
                    <div className="form-control">
                      <FileDropzone
                        name={`dokumentasi_${phase}`}
                        label={`Dokumentasi ${phase.replace(/_/g, " ")}`}
                        setValue={setValue}
                        initialPreviewSrc={process.env.NEXT_PUBLIC_BE_URL+"/"+getValues(`foto_${phase}`)}
                      />
                    </div>
                  </div>
                ) : (
                  <div>Anda harus menyelesaikan tahapan sebelumnya </div>
                )
              }
            </fieldset>
          ))}

          <div className="form-control mt-4">
            <label className="label">Catatan</label>
            <textarea
              {...register("notes")}
              className="textarea textarea-bordered w-full"
              placeholder="Opsional"
            />
          </div>
        </>
      )}

      <div className="flex justify-between">
        <button type="button" onClick={() => router.back()} className="btn">
          Back
        </button>
        <button type="submit" className="btn btn-primary" disabled={loading}>
          {loading && <span className="loading loading-spinner"></span>}
          {mode === "edit" ? "Update" : "Submit"}
        </button>
      </div>
    </form>
  );
}
