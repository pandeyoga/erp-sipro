"use client";

import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { useSitemapContext } from "@/context/useSitemapContext";
import { UnitBoxProps } from "@/types/unit";
import axios from "@/lib/axios"; // pastikan axios-nya sudah setup pakai token Bearer
import { useSearchParams } from "next/navigation";
import { PropertyUnit } from "@/app/(inside)/property/siteplan/page";
import ModalForm from "../datatable/modal/ModalCreateEdit";
import toast from "react-hot-toast";
import { Cluster } from "@/app/(inside)/property/cluster/page";

export interface UnitFormValues extends PropertyUnit {
  top: number;
  left: number;
}

export interface UnitFromAPI {
  id: string;
  type: string;
}

export function PropertyUnitModal({ modalMode, unit, unitTableRef }: { modalMode: string, unit : any, unitTableRef : any }) {
  const [units, setUnits] = useState<UnitFromAPI[]>([]);
  const [clusters, setClusters] = useState<{ id: string; name: string }[]>([]);
  const [loading, setLoading] = useState(true);

  const { project } = useSitemapContext();

  const {
    register,
    handleSubmit,
    reset,
    setValue,
    formState: { errors },
  } = useForm<UnitFormValues>({
    defaultValues: {
      unit_number: "",
      unit_type_id: "",
      cluster_id: "",
      cluster_name: "",
      top: 50,
      left: 50,
    },
  });

  // Ambil data unit dari API
  useEffect(() => {
    const fetchUnits = async () => {
      try {
        const res = await axios.get("/property/unit-property/unit-types-list");
        setUnits(res.data.data || []);
      } catch (err) {
        console.error("Gagal memuat unit", err);
      } finally {
        setLoading(false);
      }
    };
    const fetchCluster = async () => {
      axios
        .get(`/property/construction/cluster-lists/${project?.id}`)
        .then((res) => setClusters(res.data.data))
        .catch(() => toast.error('Gagal mengambil cluster'))
    }
    fetchUnits();
    fetchCluster();
    if(unit){
      reset(unit)
    }else{
      reset()
    }
  }, [unit]);

  const onSubmit = async (data: UnitFormValues) => {
    try {
      let response;
      if(modalMode == 'create'){
        response = await axios.post("/property/unit-property", {
          project_id: project?.id,
          cluster_id: data.cluster_id,
          unit_type_id: data.unit_type_id,
          unit_number: data.unit_number.toString(),
        });
      }else{
        response = await axios.put(`/property/unit-property/${unit?.id}`, {
          project_id: project?.id,
          cluster_id: data.cluster_id,
          unit_type_id: data.unit_type_id,
          unit_number: data.unit_number.toString(),
        });
      }
      if (response.status === 201 || response.status === 200) {
        closeModal();
        toast.success("Property unit created successfully");
        // opsional: reset form atau fetch ulang data
      }

    } catch (error: any) {
      if (error.response?.status === 400) {
        toast.error("Validation error: " + error.response.data.message);
      } else if (error.response?.status === 401) {
        toast.error("Unauthorized");
      } else {
        toast.error("An error occurred");
      }
    } finally{
      unitTableRef?.current?.reload()
    }
  };

  const closeModal = () => {
    (document.getElementById("modify_unit") as HTMLDialogElement)?.close();
  };

  useEffect(() => {
    setValue("project_name", project?.name ?? "");
  }, [project]);

  return (
    <ModalForm
      id="modify_unit"
      title={
        modalMode === "create" ? "Create Property Unit" : "Edit Property Unit"
      }
      onClose={closeModal}
      onSubmit={handleSubmit(onSubmit)}
      loading={loading}
    >
      <div className="w-full space-y-4">
        <fieldset className="fieldset w-full">
          <legend className="fieldset-legend">Project</legend>
          <input
            type="string"
            className="input input-bordered w-full"
            {...register("project_name")}
            disabled
          />
        </fieldset>
        {/* Pilih Label dari Cluster */}
        <fieldset className="fieldset w-full">
          <legend className="fieldset-legend">Cluster</legend>
          {loading ? (
            <div className="skeleton h-12 w-full rounded"></div>
          ) : (
            <select
              className="select select-bordered w-full"
              {...register("cluster_id", {
                required: "Cluster wajib dipilih",
              })}
            >
              <option value="">Pilih Cluster</option>
              {clusters.map((cluster) => (
                <option key={cluster.id} value={cluster.id}>
                  {cluster.name}
                </option>
              ))}
            </select>
          )}
          {errors.unit_type_id && (
            <p className="text-error text-sm mt-1">
              {errors.unit_type_id.message}
            </p>
          )}
        </fieldset>
        <fieldset className="fieldset w-full">
          <legend className="fieldset-legend">Nomor Unit</legend>
          <input
            type="number"
            className="input input-bordered w-full"
            {...register("unit_number", {
              required: "Nomor unit wajib diisi",
              valueAsNumber: true,
            })}
          />
          {errors.unit_number && (
            <p className="text-error text-sm mt-1">
              {errors.unit_number.message}
            </p>
          )}
        </fieldset>
        {/* Pilih Label dari Type Unit */}
        <fieldset className="fieldset w-full">
          <legend className="fieldset-legend">Tipe Unit</legend>
          {loading ? (
            <div className="skeleton h-12 w-full rounded"></div>
          ) : (
            <select
              className="select select-bordered w-full"
              {...register("unit_type_id", {
                required: "Tipe unit wajib dipilih",
              })}
            >
              <option value="">Pilih Type Unit</option>
              {units.map((unit) => (
                <option key={unit.id} value={unit.id}>
                  {unit.type}
                </option>
              ))}
            </select>
          )}
          {errors.unit_type_id && (
            <p className="text-error text-sm mt-1">
              {errors.unit_type_id.message}
            </p>
          )}
        </fieldset>
      </div>

      {/* Koordinat */}
      <div className="flex gap-4 hidden">
        <div className="form-control w-1/2">
          <label className="label">Top</label>
          <input
            type="number"
            className="input input-bordered"
            {...register("top", { valueAsNumber: true })}
          />
        </div>
        <div className="form-control w-1/2">
          <label className="label">Left</label>
          <input
            type="number"
            className="input input-bordered"
            {...register("left", { valueAsNumber: true })}
          />
        </div>
      </div>
    </ModalForm>
  );
}
